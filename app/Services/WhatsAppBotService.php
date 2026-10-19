<?php

namespace App\Services;

use App\Contracts\WhatsAppProvider;
use App\Models\Berita;
use App\Models\PermohonanInformasi;
use App\Models\SiteSetting;
use App\Models\WhatsappConversation;
use App\Models\WhatsappFaq;
use App\Models\WhatsappTicket;
use App\Support\Package;
use Illuminate\Support\Str;

class WhatsAppBotService
{
    public function __construct(
        protected WhatsAppProvider $provider
    ) {}

    /**
     * Proses pesan masuk dan kirim respon bot WhatsApp.
     */
    public function handleIncomingMessage(string $from, string $text): array
    {
        if (! Package::has('whatsapp_bot')) {
            return [
                'status' => 'ignored',
                'message' => 'Layanan WhatsApp bot tidak aktif pada paket ini.',
            ];
        }

        $cleanText = trim($text);
        $lowerText = strtolower($cleanText);

        $responseMessage = '';
        $tipeMenu = 'menu_utama';
        $status = 'sukses';

        // 1. Menu Utama (Salam / Bantuan)
        if (in_array($lowerText, ['menu', 'halo', 'hai', 'start', 'bantuan', 'hi', 'pilihan', 'help', 'info', 'ppid'], true)) {
            $responseMessage = $this->renderMainMenu();
            $tipeMenu = 'menu_utama';
        }

        // 2. Menu 1: Cek Status Permohonan PPID
        elseif ($lowerText === '1' || str_starts_with($lowerText, '1 ') || str_starts_with($lowerText, 'cek ') || preg_match('/PPID-[A-Z0-9\-]+/i', $cleanText)) {
            $regNumber = '';
            if (preg_match('/(PPID-[A-Z0-9\-]+)/i', $cleanText, $matches)) {
                $regNumber = strtoupper(trim($matches[1]));
            } elseif (str_starts_with($lowerText, '1 ')) {
                $regNumber = strtoupper(trim(substr($cleanText, 2)));
            } elseif (str_starts_with($lowerText, 'cek ')) {
                $regNumber = strtoupper(trim(substr($cleanText, 4)));
            }

            if (empty($regNumber)) {
                $responseMessage = "📄 *Cek Status Permohonan PPID*\n\nSilakan ketik nomor registrasi permohonan Anda dengan format:\n*1 [Nomor Registrasi]*\nContoh: *1 PPID-202610-0001*";
            } else {
                $responseMessage = $this->checkPpidStatus($regNumber);
            }
            $tipeMenu = 'cek_ppid';
        }

        // 3. Menu 2: Berita & Informasi Publik Terbaru
        elseif ($lowerText === '2' || $lowerText === 'berita') {
            $responseMessage = $this->getLatestNewsMessage();
            $tipeMenu = 'info_terbaru';
        }

        // 4. Menu 3: Kontak & Jam Layanan PPID
        elseif ($lowerText === '3' || $lowerText === 'kontak' || $lowerText === 'jam layanan') {
            $responseMessage = $this->getContactInfoMessage();
            $tipeMenu = 'kontak_ppid';
        }

        // 5. Menu 4: Tanya Jawab Umum (FAQ)
        elseif ($lowerText === '4' || $lowerText === 'faq' || $lowerText === 'tanya') {
            $responseMessage = $this->getFaqListMessage();
            $tipeMenu = 'faq';
        }

        // 6. Cek apakah cocok dengan kata kunci FAQ yang ada di database
        elseif ($faq = $this->matchFaq($lowerText)) {
            $responseMessage = "❓ *{$faq->pertanyaan}*\n\n{$faq->jawaban}\n\n_Ketik *menu* untuk kembali ke menu utama._";
            $tipeMenu = 'faq';
        }

        // 7. Di Luar Menu: Eskalasi Otomatis ke Petugas Admin
        else {
            $ticketNumber = WhatsappTicket::generateTicketNumber();
            $maskedPhone = WhatsappConversation::maskPhone($from);

            WhatsappTicket::create([
                'nomor_tiket' => $ticketNumber,
                'phone_number_masked' => $maskedPhone,
                'pesan' => $cleanText,
                'status' => 'menunggu_petugas',
            ]);

            $responseMessage = "Terima kasih telah menghubungi Layanan Informasi Publik (PPID).\n\nPertanyaan Anda telah kami teruskan ke petugas pelayanan dengan *Nomor Tiket: {$ticketNumber}*.\n\nPetugas kami akan segera meninjau dan merespons pertanyaan Anda pada jam kerja layanan.\n\nKetik *menu* untuk kembali ke menu pilihan.";
            $tipeMenu = 'eskalasi_tiket';
            $status = 'eskalasi';
        }

        // Catat percakapan secara terbatas untuk privasi & metrik laporan bulanan
        WhatsappConversation::create([
            'phone_hash' => hash('sha256', $from),
            'phone_masked' => WhatsappConversation::maskPhone($from),
            'pesan_masuk' => Str::limit($cleanText, 500),
            'pesan_keluar' => Str::limit($responseMessage, 500),
            'tipe_menu' => $tipeMenu,
            'status' => $status,
        ]);

        // Kirim balasan melalui WhatsAppProvider
        $sendResult = $this->provider->sendMessage($from, $responseMessage);

        return [
            'status' => 'success',
            'tipe_menu' => $tipeMenu,
            'response_message' => $responseMessage,
            'provider_result' => $sendResult,
        ];
    }

    /**
     * Render pesan Menu Utama Bot.
     */
    protected function renderMainMenu(): string
    {
        $setting = SiteSetting::first();
        $instansi = $setting->institution_name ?? 'Pemerintah Instansi';

        return "Halo! Selamat datang di *Layanan WhatsApp Resmi PPID {$instansi}* 🏛️\n\n"
            . "Silakan balas dengan angka pilihan berikut:\n\n"
            . "1️⃣ *Cek Status Permohonan PPID*\n(Ketik: *1 [Nomor Registrasi]*)\n\n"
            . "2️⃣ *Informasi Publik Terbaru*\n(Ketik: *2*)\n\n"
            . "3️⃣ *Kontak & Jam Layanan PPID*\n(Ketik: *3*)\n\n"
            . "4️⃣ *Tanya Jawab Umum (FAQ)*\n(Ketik: *4*)\n\n"
            . "💬 Atau langsung ketik pertanyaan Anda untuk diteruskan ke petugas layanan kami.";
    }

    /**
     * Cari status permohonan PPID berdasarkan nomor registrasi.
     */
    protected function checkPpidStatus(string $regNumber): string
    {
        $permohonan = PermohonanInformasi::with('status')
            ->where('nomor_registrasi', $regNumber)
            ->first();

        if (! $permohonan) {
            return "⚠️ Nomor registrasi *{$regNumber}* tidak ditemukan dalam sistem PPID.\n\nPastikan nomor registrasi yang Anda masukkan sesuai dengan resi pendaftaran Anda (contoh: *PPID-202610-0001*).\n\nKetik *menu* untuk kembali.";
        }

        $statusName = $permohonan->status?->nama ?? 'Dalam Proses Verifikasi';
        $tglDaftar = $permohonan->created_at?->translatedFormat('d F Y') ?? '-';
        $rincian = Str::limit($permohonan->rincian_informasi, 120);

        $msg = "📄 *STATUS PERMOHONAN INFORMASI PUBLIK*\n\n"
            . "• *No. Registrasi:* {$permohonan->nomor_registrasi}\n"
            . "• *Nama Pemohon:* {$permohonan->nama_pemohon}\n"
            . "• *Tanggal Diajukan:* {$tglDaftar}\n"
            . "• *Informasi Dimohon:* {$rincian}\n"
            . "• *Status Saat Ini:* *{$statusName}*\n";

        if ($permohonan->jawaban_teks) {
            $msg .= "• *Catatan Jawaban:* " . Str::limit($permohonan->jawaban_teks, 150) . "\n";
        }

        $msg .= "\n_Ketik *menu* untuk kembali ke menu utama._";

        return $msg;
    }

    /**
     * Daftar berita & pengumuman terbaru.
     */
    protected function getLatestNewsMessage(): string
    {
        $news = Berita::published()->orderByDesc('tanggal_terbit')->take(3)->get();

        if ($news->isEmpty()) {
            return "📰 Belum ada informasi publik atau berita terbaru saat ini.\n\nKetik *menu* untuk kembali.";
        }

        $msg = "📰 *INFORMASI & BERITA PUBLIK TERBARU*\n\n";
        foreach ($news as $idx => $b) {
            $num = $idx + 1;
            $tgl = $b->tanggal_terbit?->format('d/m/Y') ?? '';
            $link = route('berita.detail', $b->slug);
            $msg .= "{$num}. *{$b->judul}* ({$tgl})\n   🔗 {$link}\n\n";
        }

        $msg .= "_Ketik *menu* untuk kembali ke menu utama._";
        return $msg;
    }

    /**
     * Informasi kontak dan jam kerja PPID.
     */
    protected function getContactInfoMessage(): string
    {
        $setting = SiteSetting::first();
        $instansi = $setting->institution_name ?? 'Pemerintah Instansi';
        $alamat = $setting->address ?? 'Gedung Pelayanan Publik Lantai 1';
        $email = $setting->email ?? 'ppid@instansi.go.id';
        $telepon = $setting->phone ?? '(0541) 123456';
        $jam = $setting->service_hours ?? 'Senin - Jumat (08.00 - 16.00 WITA)';

        return "📞 *KONTAK & JAM LAYANAN PPID*\n\n"
            . "🏛️ *{$instansi}*\n"
            . "📍 Alamat: {$alamat}\n"
            . "📧 Email: {$email}\n"
            . "☎️ Telepon: {$telepon}\n"
            . "⏰ Jam Layanan: {$jam}\n\n"
            . "_Ketik *menu* untuk kembali ke menu utama._";
    }

    /**
     * Daftar tanya jawab umum (FAQ).
     */
    protected function getFaqListMessage(): string
    {
        $faqs = WhatsappFaq::active()->get();

        if ($faqs->isEmpty()) {
            return "❓ *TANYA JAWAB UMUM (FAQ)*\n\n"
                . "Ketik kata kunci berikut untuk melihat jawaban:\n"
                . "• *syarat* : Syarat permohonan informasi\n"
                . "• *biaya* : Biaya layanan PPID\n"
                . "• *waktu* : Jangka waktu penyelesaian\n"
                . "• *keberatan* : Cara mengajukan keberatan\n\n"
                . "_Ketik *menu* untuk kembali._";
        }

        $msg = "❓ *TANYA JAWAB UMUM (FAQ)*\n\nKetik kata kunci berikut untuk melihat informasi:\n\n";
        foreach ($faqs as $f) {
            $msg .= "• *{$f->kata_kunci}* : {$f->pertanyaan}\n";
        }

        $msg .= "\n_Ketik salah satu kata kunci di atas atau ketik *menu*._";
        return $msg;
    }

    /**
     * Cari kecocokan FAQ dari teks pengguna.
     */
    protected function matchFaq(string $text): ?WhatsappFaq
    {
        $faqs = WhatsappFaq::active()->get();

        foreach ($faqs as $faq) {
            $keyword = strtolower($faq->kata_kunci);
            if ($text === $keyword || str_contains($text, $keyword)) {
                return $faq;
            }
        }

        // Bawaan default jika tabel belum diisi seeder
        $defaultFaqs = [
            'syarat' => [
                'pertanyaan' => 'Apa syarat permohonan informasi publik?',
                'jawaban' => "Syarat permohonan informasi publik:\n1. KTP/Identitas diri pemohon\n2. Formulir permohonan informasi\n3. Tujuan penggunaan informasi yang jelas dan tidak melanggar hukum.",
            ],
            'biaya' => [
                'pertanyaan' => 'Berapa biaya permohonan informasi publik?',
                'jawaban' => "Pelayanan informasi publik di PPID adalah GRATIS (Rp 0). Biaya penggandaan atau pengiriman berkas fisik ditanggung pemohon sesuai ketentuan regulasi.",
            ],
            'waktu' => [
                'pertanyaan' => 'Berapa lama waktu penyelesaian permohonan?',
                'jawaban' => "Sesuai UU No. 14 Tahun 2008, PPID memberikan jawaban paling lambat 10 (sepuluh) hari kerja sejak permohonan diterima, dan dapat diperpanjang maksimal 7 (tujuh) hari kerja.",
            ],
            'keberatan' => [
                'pertanyaan' => 'Bagaimana cara mengajukan keberatan informasi?',
                'jawaban' => "Pemohon dapat mengajukan keberatan secara tertulis kepada Atasan PPID paling lambat 30 hari kerja setelah alasan keberatan diketahui. Atasan PPID wajib memberikan tanggapan tertulis maksimal 30 hari kerja.",
            ],
        ];

        foreach ($defaultFaqs as $key => $item) {
            if ($text === $key || str_contains($text, $key)) {
                return new WhatsappFaq([
                    'kata_kunci' => $key,
                    'pertanyaan' => $item['pertanyaan'],
                    'jawaban' => $item['jawaban'],
                ]);
            }
        }

        return null;
    }
}
