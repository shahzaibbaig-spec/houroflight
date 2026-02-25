<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use InvalidArgumentException;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class DonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['dashboard', 'showDonation']);
    }

    public function showForm(): View
    {
        return view('pages.donate');
    }

    public function createCheckout(StoreDonationRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $donation = null;

        try {
            $userId = $this->resolveDonationUserId($request, $validated);
            $impact = $this->calculateImpact((float) $validated['amount'], $validated['currency']);

            $donation = Donation::create([
                'user_id' => $userId,
                'donor_name' => $validated['donor_name'],
                'donor_email' => $validated['donor_email'],
                'amount' => $validated['amount'],
                'currency' => strtoupper($validated['currency']),
                'donation_type' => $validated['donation_type'],
                'status' => 'pending',
                'impact_snapshot' => $impact,
            ]);

            $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');
            if (! $stripeSecret) {
                throw new InvalidArgumentException('Stripe is not configured. Please set STRIPE_SECRET.');
            }

            Stripe::setApiKey($stripeSecret);

            $isMonthly = $validated['donation_type'] === 'monthly';
            $unitAmount = (int) round(((float) $validated['amount']) * 100);
            $currency = strtolower($validated['currency']);

            $priceData = [
                'currency' => $currency,
                'unit_amount' => $unitAmount,
                'product_data' => [
                    'name' => 'Hour of Light Donation',
                ],
            ];

            if ($isMonthly) {
                $priceData['recurring'] = ['interval' => 'month'];
            }

            $session = StripeCheckoutSession::create([
                'mode' => $isMonthly ? 'subscription' : 'payment',
                'line_items' => [[
                    'price_data' => $priceData,
                    'quantity' => 1,
                ]],
                'customer_email' => $validated['donor_email'],
                'success_url' => route('donate.success').'?session_id={CHECKOUT_SESSION_ID}&donation='.$donation->id,
                'cancel_url' => route('donate.cancel').'?donation='.$donation->id,
                'metadata' => [
                    'donation_id' => (string) $donation->id,
                    'donation_type' => $validated['donation_type'],
                ],
            ]);

            $donation->update([
                'stripe_checkout_session_id' => $session->id,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'url' => $session->url,
                ]);
            }

            return redirect($session->url);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (ApiErrorException $e) {
            if ($donation) {
                $donation->update(['status' => 'failed']);
            }

            Log::error('Stripe checkout creation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to create Stripe checkout session.',
            ], 500);
        } catch (\Throwable $e) {
            if ($donation) {
                $donation->update(['status' => 'failed']);
            }

            Log::error('Donation checkout failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to process donation checkout.',
            ], 500);
        }
    }

    public function success(): View
    {
        return view('pages.donation-success');
    }

    public function submitPledge(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'donor_name' => ['required', 'string', 'max:255'],
            'donor_email' => ['required', 'email', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'size:3', Rule::in(array_keys(config('donations.rates_to_usd', [])))],
            'donation_type' => ['required', 'in:one_time,monthly'],
            'proof_type' => ['required', Rule::in(['pdf', 'image'])],
            'payment_screenshot' => ['required', 'file', 'max:8192'],
        ]);

        $proofFile = $request->file('payment_screenshot');
        $proofMime = strtolower((string) $proofFile->getMimeType());
        $isImage = str_starts_with($proofMime, 'image/');
        $isPdf = in_array($proofMime, ['application/pdf', 'application/x-pdf'], true);

        if ($validated['proof_type'] === 'image' && ! $isImage) {
            return back()
                ->withErrors(['payment_screenshot' => 'Please upload an image file for Image proof type.'])
                ->withInput();
        }

        if ($validated['proof_type'] === 'pdf' && ! $isPdf) {
            return back()
                ->withErrors(['payment_screenshot' => 'Please upload a PDF file for PDF proof type.'])
                ->withInput();
        }

        $proofDirectory = $validated['proof_type'] === 'pdf'
            ? 'donations/payment-proofs/pdfs'
            : 'donations/payment-proofs/images';
        $proofPath = $proofFile->store($proofDirectory, 'public');
        $impact = $this->calculateImpact((float) $validated['amount'], strtoupper($validated['currency']));

        Donation::create([
            'user_id' => $request->user()->id,
            'donor_name' => $validated['donor_name'],
            'donor_email' => $validated['donor_email'],
            'amount' => $validated['amount'],
            'currency' => strtoupper($validated['currency']),
            'donation_type' => $validated['donation_type'],
            'status' => 'pending',
            'payment_proof_path' => $proofPath,
            'impact_snapshot' => $impact,
        ]);

        return redirect()
            ->route('donate.form')
            ->with('success', 'Payment received. Thank you for contributing to the cause. You will receive an email from us after your payment is verified.');
    }

    public function cancel(): View
    {
        return view('pages.donation-cancel');
    }

    public function storeHardware(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'donor_name' => ['required', 'string', 'max:255'],
            'donor_email' => ['required', 'email', 'max:255'],
            'hardware_type' => ['required', Rule::in(['Laptop', 'PC'])],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'hardware_condition' => ['nullable', 'string', 'max:500'],
            'pickup_address' => ['required', 'string', 'max:2000'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $body = implode("\n", [
            'New Hardware Donation Request - Hour of Light',
            'Donor Name: '.$validated['donor_name'],
            'Donor Email: '.$validated['donor_email'],
            'Hardware Type: '.$validated['hardware_type'],
            'Quantity: '.$validated['quantity'],
            'Condition: '.($validated['hardware_condition'] ?? '-'),
            'Pickup Address: '.$validated['pickup_address'],
            'Message: '.($validated['message'] ?? '-'),
        ]);

        try {
            $recipients = ['info@houroflight.com', 'shahzaib.baig@gmail.com'];
            Mail::raw($body, function ($message) use ($recipients) {
                $message->to($recipients)->subject('Hardware Donation Request - Hour of Light');
            });
        } catch (\Throwable $e) {
            Log::error('Hardware donation notification failed', ['error' => $e->getMessage()]);
        }

        return redirect()
            ->route('donate.form')
            ->with('success', 'Thank you. Your hardware donation request has been submitted.');
    }

    public function impact(Request $request): JsonResponse
    {
        $supportedCurrencies = array_keys(config('donations.rates_to_usd', []));

        $validator = Validator::make($request->query(), [
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'size:3', Rule::in($supportedCurrencies)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $amount = (float) $validated['amount'];
        $currency = strtoupper($validated['currency']);
        $impact = $this->calculateImpact($amount, $currency);

        return response()->json([
            'amount' => $amount,
            'currency' => $currency,
            'amount_usd' => $impact['amount_usd'],
            'devices_supported' => $impact['devices_supported'],
            'teacher_training_hours' => $impact['teacher_training_hours'],
            'classroom_upgrade_contribution' => $impact['classroom_upgrade_contribution'],
        ]);
    }

    public function dashboard(Request $request): View
    {
        $user = $request->user();

        $donations = Donation::query()
            ->when($user->role !== 'admin', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->get();

        return view('donor.donations.index', compact('donations'));
    }

    public function showDonation(Request $request, Donation $donation): View
    {
        $this->authorizeDonationAccess($request, $donation);

        return view('donor.donations.show', compact('donation'));
    }

    protected function authorizeDonationAccess(Request $request, Donation $donation): void
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return;
        }

        abort_unless((int) $donation->user_id === (int) $user->id, 403);
    }

    protected function calculateImpact(float $amount, string $currency): array
    {
        $normalizedAmount = max(0, $amount);
        $rates = config('donations.rates_to_usd', []);
        $rate = $rates[$currency] ?? 1.0;
        $amountUsd = round($normalizedAmount * $rate, 2);

        $pkrToUsd = (float) ($rates['PKR'] ?? 0);
        $chromebookCostPkr = (float) config('donations.chromebook_cost_pkr', 20000);
        $deviceCost = $pkrToUsd > 0
            ? ($chromebookCostPkr * $pkrToUsd)
            : (float) config('donations.device_cost_usd', 80);
        $trainingHourCost = (float) config('donations.training_hour_cost_usd', 10);
        $classroomKitCost = (float) config('donations.classroom_kit_cost_usd', 500);

        return [
            'amount_usd' => $amountUsd,
            'devices_supported' => (int) floor($amountUsd / $deviceCost),
            'teacher_training_hours' => (int) floor($amountUsd / $trainingHourCost),
            'classroom_upgrade_contribution' => min(100.0, round(($amountUsd / $classroomKitCost) * 100, 1)),
        ];
    }

    protected function resolveDonationUserId(StoreDonationRequest $request, array $validated): ?int
    {
        if ($request->user()) {
            return (int) $request->user()->id;
        }

        $email = $validated['donor_email'];
        $existingUser = User::where('email', $email)->first();

        if ($request->boolean('create_account')) {
            if (! $existingUser) {
                $user = User::create([
                    'name' => $validated['donor_name'],
                    'email' => $email,
                    'role' => 'donor',
                    'password' => Hash::make(Str::random(32)),
                ]);

                Password::sendResetLink(['email' => $user->email]);

                return (int) $user->id;
            }
        }

        if ($existingUser && $existingUser->role === 'donor') {
            return (int) $existingUser->id;
        }

        return null;
    }
}
