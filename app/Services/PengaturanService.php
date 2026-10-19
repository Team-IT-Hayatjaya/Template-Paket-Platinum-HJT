<?php

namespace App\Services;

use App\Models\PengaturanPpid;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class PengaturanService
{
    /**
     * Dapatkan data pengaturan PPID aktif.
     */
    public function getPengaturan(): PengaturanPpid
    {
        return PengaturanPpid::firstOrCreate(
            ['id' => 1],
            [
                'nama_instansi' => 'Pemerintah Kabupaten / Kota',
                'sub_nama_instansi' => 'Dinas Komunikasi dan Informatika',
                'nama_ppid' => 'PPID Utama Pemerintah Daerah',
                'warna_utama' => '#1e3a8a',
                'warna_sekunder' => '#b91c1c',
                'alamat' => 'Gedung Sekretariat Daerah Lt. 2, Jl. Pahlawan No. 45, Kompleks Perkantoran Pemerintah',
                'telepon' => '(021) 1234-5678',
                'email' => 'ppid@instansi.go.id',
                'jam_layanan' => "Senin s.d. Kamis: 08.00 - 15.30 WIB\nJumat: 08.00 - 15.00 WIB",
                'maklumat_pelayanan' => 'Kami berkomitmen memberikan pelayanan informasi publik dengan sungguh-sungguh, transparan, cepat, tepat waktu, dan tanpa diskriminasi demi terwujudnya keterbukaan informasi di lingkungan instansi kami.',
            ]
        );
    }

    /**
     * Perbarui pengaturan PPID dan kelola unggahan berkas logo & favicon.
     */
    public function updatePengaturan(array $data, ?UploadedFile $logo = null, ?UploadedFile $favicon = null): PengaturanPpid
    {
        $pengaturan = $this->getPengaturan();

        // Pastikan direktori tujuan ada di public/uploads/branding
        $uploadDir = public_path('uploads/branding');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        // Proses unggah logo baru jika ada
        if ($logo && $logo->isValid()) {
            if ($pengaturan->logo_path && File::exists(public_path($pengaturan->logo_path))) {
                File::delete(public_path($pengaturan->logo_path));
            }

            $logoFileName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->move($uploadDir, $logoFileName);
            $data['logo_path'] = 'uploads/branding/' . $logoFileName;
        }

        // Proses unggah favicon baru jika ada
        if ($favicon && $favicon->isValid()) {
            if ($pengaturan->favicon_path && File::exists(public_path($pengaturan->favicon_path))) {
                File::delete(public_path($pengaturan->favicon_path));
            }

            $faviconFileName = 'favicon_' . time() . '.' . $favicon->getClientOriginalExtension();
            $favicon->move($uploadDir, $faviconFileName);
            $data['favicon_path'] = 'uploads/branding/' . $faviconFileName;
        }

        $pengaturan->update($data);

        return $pengaturan;
    }
}
