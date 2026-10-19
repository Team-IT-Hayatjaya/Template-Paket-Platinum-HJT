<?php

namespace App\Services;

use App\Mail\PermohonanJawabanMail;
use App\Mail\PermohonanKonfirmasiMail;
use App\Mail\StatusPermohonanUpdatedMail;
use App\Models\PermohonanInformasi;
use App\Models\StatusPermohonan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PpidService
{
    /**
     * Daftarkan permohonan informasi baru dari formulir publik.
     */
    public function submitPermohonan(array $data, ?UploadedFile $identitasFile = null): PermohonanInformasi
    {
        // 1. Simpan berkas identitas ke private storage jika diunggah
        $identitasPath = null;
        if ($identitasFile) {
            $extension = $identitasFile->getClientOriginalExtension() ?: 'bin';
            $filename = Str::uuid()->toString() . '.' . $extension;
            $identitasPath = 'identitas/' . $filename;

            // Simpan ke private disk (non-publik, tidak memiliki symbolic link ke public)
            Storage::disk('private')->put($identitasPath, file_get_contents($identitasFile->getRealPath()));
        }

        // 2. Tentukan status awal: "Diterima"
        $statusDiterima = StatusPermohonan::where('slug', 'diterima')->first();
        $statusId = $statusDiterima ? $statusDiterima->id : 1;

        // 3. Buat nomor registrasi dan hitung tenggat waktu (10 hari kerja)
        $nomorRegistrasi = PermohonanInformasi::generateNomorRegistrasi();
        $tenggatJawab = PermohonanInformasi::calculateDeadline(now(), 10);

        // 4. Buat record permohonan informasi
        $permohonan = PermohonanInformasi::create([
            'nomor_registrasi' => $nomorRegistrasi,
            'status_id' => $statusId,
            'kategori_pemohon' => $data['kategori_pemohon'] ?? 'perorangan',
            'nama_pemohon' => $data['nama_pemohon'],
            'nik' => $data['nik'] ?? null, // Otomatis terenkripsi via Eloquent cast
            'alamat' => $data['alamat'],
            'telepon' => $data['telepon'],
            'email' => $data['email'],
            'pekerjaan' => $data['pekerjaan'] ?? null,
            'identitas_file_path' => $identitasPath,
            'rincian_informasi' => $data['rincian_informasi'],
            'tujuan_penggunaan' => $data['tujuan_penggunaan'],
            'cara_memperoleh_informasi' => $data['cara_memperoleh_informasi'] ?? 'salinan',
            'cara_mendapat_salinan' => $data['cara_mendapat_salinan'] ?? 'email',
            'tenggat_jawab' => $tenggatJawab,
        ]);

        // 5. Kirim email konfirmasi registrasi ke pemohon
        try {
            Mail::to($permohonan->email)->send(new PermohonanKonfirmasiMail($permohonan));
        } catch (\Throwable $e) {
            Log::warning("Gagal mengirim email konfirmasi permohonan PPID: {$e->getMessage()}");
        }

        return $permohonan;
    }

    /**
     * Proses jawaban atau pembaruan status oleh petugas PPID.
     */
    public function prosesJawaban(PermohonanInformasi $permohonan, array $data, ?UploadedFile $jawabanFile = null): PermohonanInformasi
    {
        $jawabanPath = $permohonan->jawaban_file_path;

        if ($jawabanFile) {
            $filename = Str::uuid()->toString() . '.' . ($jawabanFile->getClientOriginalExtension() ?: 'pdf');
            $jawabanPath = 'jawaban/' . $filename;
            Storage::disk('private')->put($jawabanPath, file_get_contents($jawabanFile->getRealPath()));
        }

        $permohonan->update([
            'status_id' => $data['status_id'] ?? $permohonan->status_id,
            'catatan_internal' => $data['catatan_internal'] ?? $permohonan->catatan_internal,
            'jawaban_teks' => $data['jawaban_teks'] ?? $permohonan->jawaban_teks,
            'jawaban_file_path' => $jawabanPath,
            'tanggal_dijawab' => now(),
        ]);

        $permohonan->refresh();

        // Kirim email notifikasi resmi sesuai status
        $this->kirimNotifikasiStatus($permohonan, $data['catatan_internal'] ?? null);

        return $permohonan;
    }

    /**
     * Kirim email pemberitahuan setiap ada perubahan status permohonan.
     */
    public function kirimNotifikasiStatus(PermohonanInformasi $permohonan, ?string $keterangan = null): void
    {
        $permohonan->loadMissing('status');
        $statusSlug = $permohonan->status?->slug;

        if (in_array($statusSlug, ['dijawab', 'ditolak', 'selesai'])) {
            try {
                Mail::to($permohonan->email)->send(new PermohonanJawabanMail($permohonan));
            } catch (\Throwable $e) {
                Log::warning("Gagal mengirim email jawaban PPID: {$e->getMessage()}");
            }
        } else {
            try {
                Mail::to($permohonan->email)->send(new StatusPermohonanUpdatedMail($permohonan, $keterangan));
            } catch (\Throwable $e) {
                Log::warning("Gagal mengirim email pembaruan status PPID: {$e->getMessage()}");
            }
        }
    }
}
