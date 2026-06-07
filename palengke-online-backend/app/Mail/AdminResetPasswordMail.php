<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $resetUrl;
    public string $recipientName;
    public string $email;

    public function __construct(string $resetUrl, string $recipientName = '', string $email = '')
    {
        $this->resetUrl = $resetUrl;
        $this->recipientName = $recipientName;
        $this->email = $email;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Palengke Online Admin Password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-reset-password',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
