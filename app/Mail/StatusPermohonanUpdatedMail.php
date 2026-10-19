<?php

namespace App\Mail;

use App\Models\PermohonanInformasi;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusPermohonanUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PermohonanInformasi $permohonan,
        public ?string $keterangan = null,
        public ?SiteSetting $setting = null
    ) {
        $this->setting = $setting ?? SiteSetting::current();
    }

    public function envelope(): Envelope
    {
        $statusNama = $this->permohonan->status?->nama ?? 'Pembaruan';

        return new Envelope(
            subject: "Pembaruan Status Permohonan Informasi Publik [{$this->permohonan->nomor_registrasi}] — {$statusNama}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.status-permohonan-updated',
        );
    }
}
