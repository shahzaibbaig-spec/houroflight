<?php

namespace App\Http\Controllers;

use App\Mail\DonationAdminNotificationMail;
use App\Mail\DonationReceiptMail;
use App\Models\Donation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = (string) $request->header('Stripe-Signature', '');
        $webhookSecret = (string) config('services.stripe.webhook_secret');

        if ($webhookSecret === '') {
            return response()->json(['message' => 'Stripe webhook secret is not configured.'], 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (\UnexpectedValueException|SignatureVerificationException $e) {
            return response()->json(['message' => 'Invalid webhook payload or signature.'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            default:
                // No-op for unsupported event types.
                break;
        }

        return response()->json(['received' => true]);
    }

    protected function handleCheckoutSessionCompleted(object $session): void
    {
        $sessionId = (string) ($session->id ?? '');
        if ($sessionId === '') {
            return;
        }

        $donation = Donation::where('stripe_checkout_session_id', $sessionId)->first();
        if (! $donation) {
            return;
        }

        // Idempotency: do not process successful donation twice.
        if ($donation->status === 'succeeded') {
            return;
        }

        $paymentIntentId = $session->payment_intent ?? null;
        $subscriptionId = $session->subscription ?? null;
        $receiptUrl = null;

        if ($paymentIntentId) {
            $stripeSecret = (string) config('services.stripe.secret');
            if ($stripeSecret !== '') {
                try {
                    $client = new StripeClient($stripeSecret);
                    $paymentIntent = $client->paymentIntents->retrieve((string) $paymentIntentId, [
                        'expand' => ['charges.data'],
                    ]);

                    if (! empty($paymentIntent->charges->data[0]->receipt_url)) {
                        $receiptUrl = $paymentIntent->charges->data[0]->receipt_url;
                    }
                } catch (\Throwable $e) {
                    Log::warning('Unable to retrieve Stripe payment intent receipt URL.', [
                        'payment_intent_id' => (string) $paymentIntentId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $donation->update([
            'status' => 'succeeded',
            'stripe_payment_intent_id' => $paymentIntentId ? (string) $paymentIntentId : $donation->stripe_payment_intent_id,
            'stripe_subscription_id' => $subscriptionId ? (string) $subscriptionId : $donation->stripe_subscription_id,
            'receipt_url' => $receiptUrl ?? $donation->receipt_url,
        ]);

        try {
            $this->dispatchMail(
                Mail::to($donation->donor_email),
                new DonationReceiptMail($donation)
            );

            $this->dispatchMail(
                Mail::to(['info@houroflight.com', 'shahzaib.baig@gmail.com']),
                new DonationAdminNotificationMail($donation)
            );
        } catch (\Throwable $e) {
            Log::error('Donation webhook email dispatch failed.', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function handleInvoicePaymentFailed(object $invoice): void
    {
        $subscriptionId = (string) ($invoice->subscription ?? '');
        if ($subscriptionId === '') {
            return;
        }

        Donation::where('stripe_subscription_id', $subscriptionId)
            ->update(['status' => 'failed']);
    }

    protected function handleSubscriptionDeleted(object $subscription): void
    {
        $subscriptionId = (string) ($subscription->id ?? '');
        if ($subscriptionId === '') {
            return;
        }

        Donation::where('stripe_subscription_id', $subscriptionId)
            ->where('status', '!=', 'succeeded')
            ->update(['status' => 'canceled']);
    }

    protected function dispatchMail($pendingMail, $mailable): void
    {
        if (config('queue.default') !== 'sync') {
            $pendingMail->queue($mailable);

            return;
        }

        $pendingMail->send($mailable);
    }
}
