<?php

namespace App\Mail;

use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LessonStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lesson $lesson,
        public string $subjectLine,
        public ?string $customMessage = null,
        public ?string $adminPanelLink = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lessons.status',
            with: [
                'lesson' => $this->lesson,
                'customMessage' => $this->customMessage,
                'adminPanelLink' => $this->adminPanelLink,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

