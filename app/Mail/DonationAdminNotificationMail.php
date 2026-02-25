<?php

namespace App\Mail;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonationAdminNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Donation $donation)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Donation Received - Hour of Light',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.donations.admin-notification',
            with: [
                'donation' => $this->donation,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
