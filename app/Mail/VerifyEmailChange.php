<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailChange extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your New Email Address',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-email-change',
            with: [
                'user' => $this->user,
                'newEmail' => $this->user->pending_email,
                'verificationUrl' => $this->getVerificationUrl(),
                'expiresAt' => $this->user->email_change_token_expires_at,
            ],
        );
    }

    /**
     * Get the email change verification URL.
     */
    protected function getVerificationUrl(): string
    {
        return route('profile.email.verify', [
            'token' => $this->user->email_change_token,
            'email' => $this->user->pending_email,
        ]);
    }
}
