<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $firstName;
    public string $storeName;
    public string $reason;

    public function __construct(string $firstName, string $storeName, string $reason)
    {
        $this->firstName = $firstName;
        $this->storeName = $storeName;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Update – Palengke Online',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-rejected',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
