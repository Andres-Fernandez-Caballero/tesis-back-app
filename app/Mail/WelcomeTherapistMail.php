<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeTherapistMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $password = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tus credenciales de acceso — BodyFix',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-therapist',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
