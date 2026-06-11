<?php

namespace App\Mail;

use App\Models\LocalRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LocalRegistrationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly LocalRegistration $registration,
        public readonly string $password = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tu local fue aprobado! — BodyFix',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.local-registration-approved',
        );
    }
}
