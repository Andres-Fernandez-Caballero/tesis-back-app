<?php

namespace App\Mail;

use App\Models\LocalRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LocalRegistrationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly LocalRegistration $registration
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Actualización sobre tu solicitud — BodyFix',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.local-registration-rejected',
        );
    }
}
