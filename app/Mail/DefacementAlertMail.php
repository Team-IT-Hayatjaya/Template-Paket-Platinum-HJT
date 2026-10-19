<?php

namespace App\Mail;

use App\Models\IntegrityCheck;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DefacementAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public IntegrityCheck $check,
        public ?SiteSetting $setting = null
    ) {
        $this->setting = $setting ?? SiteSetting::current();
    }

    public function envelope(): Envelope
    {
        $instansi = $this->setting?->institution_acronym ?? 'Portal';

        return new Envelope(
            subject: "[PERINGATAN KEAMANAN] Perubahan berkas terdeteksi — {$instansi}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.defacement-alert',
        );
    }
}
