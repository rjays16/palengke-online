<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $firstName;
    public string $storeName;

    public function __construct(string $firstName, string $storeName)
    {
        $this->firstName = $firstName;
        $this->storeName = $storeName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Account Has Been Approved! – Palengke Online',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
