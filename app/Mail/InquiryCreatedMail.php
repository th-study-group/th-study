<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $title,
        public string $content,
        public string $link
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('[%s] %s', config('app.name'), $this->title),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inquiry_created',
            with: [
                'title' => $this->title,
                'content' => $this->content,
                'link' => $this->link,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
