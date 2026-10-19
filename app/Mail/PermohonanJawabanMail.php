<?php

namespace App\Mail;

use App\Models\PermohonanInformasi;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PermohonanJawabanMail extends Mailable
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
        $statusNama = $this->permohonan->status?->nama ?? 'Tanggapan';

        return new Envelope(
            subject: "Tanggapan Resmi Permohonan Informasi Publik [{$this->permohonan->nomor_registrasi}] - Status: {$statusNama}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.permohonan-jawaban',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if (!empty($this->permohonan->jawaban_file_path)) {
            // Cek apakah berkas ada di private disk atau public disk
            if (Storage::disk('private')->exists($this->permohonan->jawaban_file_path)) {
                $attachments[] = Attachment::fromStorageDisk('private', $this->permohonan->jawaban_file_path)
                    ->as('Lampiran_Jawaban_PPID.pdf');
            } elseif (Storage::disk('public')->exists($this->permohonan->jawaban_file_path)) {
                $attachments[] = Attachment::fromStorageDisk('public', $this->permohonan->jawaban_file_path)
                    ->as('Lampiran_Jawaban_PPID.pdf');
            }
        }

        return $attachments;
    }
}
