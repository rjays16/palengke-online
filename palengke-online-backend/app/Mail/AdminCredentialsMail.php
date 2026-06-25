<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $recipientName;
    public string $loginUrl;

    public function __construct(string $recipientName = '')
    {
        $this->recipientName = $recipientName;
        $this->loginUrl = 'http://localhost:3000/login';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Palengke Online Admin Account',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-credentials',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
