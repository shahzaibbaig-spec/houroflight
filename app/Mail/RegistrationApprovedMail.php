<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $accountType,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('info@houroflight.com', 'Hour of Light'),
            subject: 'Welcome to Hour of Light - Account Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-approved',
            with: [
                'recipientName' => $this->recipientName,
                'accountType' => $this->accountType,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
