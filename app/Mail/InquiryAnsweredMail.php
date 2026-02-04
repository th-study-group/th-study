<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryAnsweredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectTitle,
        public string $bodyText,
        public string $link
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('[%s] %s', config('app.name'), $this->subjectTitle),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inquiry_answered',
            with: [
                'subjectTitle' => $this->subjectTitle,
                'bodyText' => $this->bodyText,
                'link' => $this->link,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
