<?php

namespace App\Mail;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountLockedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public int $attempts,
        public int $decayMinutes,
        public ?string $ip = null,
        public ?SiteSetting $setting = null
    ) {
        $this->setting = $setting ?? SiteSetting::current();
        $this->ip = $ip ?? request()->ip();
    }

    public function envelope(): Envelope
    {
        $instansi = $this->setting?->institution_acronym ?? 'Portal';

        return new Envelope(
            subject: "[PERINGATAN KEAMANAN] Akun dikunci sementara karena gagal login berulang — {$this->user->email} ({$instansi})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-locked',
        );
    }
}
