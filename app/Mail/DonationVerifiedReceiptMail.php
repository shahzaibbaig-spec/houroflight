<?php

namespace App\Mail;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DonationVerifiedReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Donation $donation)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Donation Received & Verified — Hour of Light',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.donation_verified',
            with: [
                'donation' => $this->donation,
            ],
        );
    }

    public function attachments(): array
    {
        $receiptNumber = $this->donation->receipt_number ?: ('HOL-'.now()->format('Y').'-'.str_pad((string) $this->donation->id, 6, '0', STR_PAD_LEFT));
        $fileName = 'Receipt-'.$receiptNumber.'.pdf';

        try {
            if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                Log::warning('DomPDF not installed; sending donation verified email without PDF attachment.', [
                    'donation_id' => $this->donation->id,
                ]);

                return [];
            }

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.donation_receipt', [
                'donation' => $this->donation,
            ]);

            return [
                Attachment::fromData(
                    fn () => $pdf->output(),
                    $fileName
                )->withMime('application/pdf'),
            ];
        } catch (\Throwable $e) {
            Log::warning('Donation receipt PDF generation failed; sending email without attachment.', [
                'donation_id' => $this->donation->id,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
