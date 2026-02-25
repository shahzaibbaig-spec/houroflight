<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\DonationVerifiedReceiptMail;
use App\Models\Donation;
use App\Support\ReceiptNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DonationVerificationController extends Controller
{
    public function verify(Donation $donation): RedirectResponse
    {
        if ($donation->verified_at && $donation->receipt_sent_at) {
            return back()->with('success', 'Already verified.');
        }

        $donation->verified_at = now();
        $donation->verified_by = auth()->id();
        $donation->status = 'verified';
        $donation->receipt_number = ReceiptNumber::forDonation($donation);

        $donation->save();

        try {
            Mail::to($donation->donor_email)->send(new DonationVerifiedReceiptMail($donation));
            $donation->receipt_sent_at = now();
            $donation->save();

            return back()->with('success', 'Donation verified and receipt sent successfully.');
        } catch (\Throwable $e) {
            Log::error('Donation verification email failed', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('success', 'Donation verified, but receipt email could not be sent right now.');
        }
    }
}
