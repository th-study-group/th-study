<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * 사용자 안내 메일
 */
class UserNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $mailSubject = '',
        public string $bladeName = '',
        public array $params = [],
    ) {}

    /**
     * 메일 제목 설정
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    /**
     * 메일 본문 설정
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.' . $this->bladeName,
            with: $this->params,
        );
    }

    /**
     * 메일 첨부파일 설정
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
