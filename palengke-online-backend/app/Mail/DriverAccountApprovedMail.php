<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverAccountApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $firstName;

    public function __construct(string $firstName)
    {
        $this->firstName = $firstName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Driver Account Has Been Approved! – Palengke Online',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.driver-account-approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
