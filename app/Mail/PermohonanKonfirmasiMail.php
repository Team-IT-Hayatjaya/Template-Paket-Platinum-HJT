<?php

namespace App\Mail;

use App\Models\PermohonanInformasi;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PermohonanKonfirmasiMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PermohonanInformasi $permohonan,
        public ?SiteSetting $setting = null
    ) {
        $this->setting = $setting ?? SiteSetting::current();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Konfirmasi Registrasi Permohonan Informasi Publik [{$this->permohonan->nomor_registrasi}]",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.permohonan-konfirmasi',
        );
    }
}
