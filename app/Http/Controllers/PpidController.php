<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermohonanInformasiRequest;
use App\Models\DokumenInformasi;
use App\Models\Halaman;
use App\Models\KategoriInformasi;
use App\Models\PermohonanInformasi;
use App\Services\PpidService;
use App\Support\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PpidController extends Controller
{
    public function __construct(
        protected PpidService $ppidService
    ) {}

    /**
     * Halaman Publik Daftar Informasi Publik (DIP) dengan 4 Klasifikasi UU KIP.
     */
    public function daftarInformasi(Request $request)
    {
        $kategoriSlug = $request->query('klasifikasi', 'semua');
        $tahun = $request->query('tahun');
        $q = $request->query('q');

        $kategoriList = KategoriInformasi::where('is_active', true)->orderBy('urutan')->get();

        $query = DokumenInformasi::where('is_published', true)
            ->with('kategori')
            ->orderByDesc('tahun')
            ->orderByDesc('created_at');

        if ($kategoriSlug && $kategoriSlug !== 'semua') {
            $query->whereHas('kategori', function ($qBuilder) use ($kategoriSlug) {
                $qBuilder->where('slug', $kategoriSlug);
            });
        }

        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('ringkasan', 'like', "%{$q}%");
            });
        }

        $dokumenList = $query->paginate(12)->withQueryString();

        // Ambil daftar tahun unik yang tersedia
        $availableYears = DokumenInformasi::distinct()->orderByDesc('tahun')->pluck('tahun')->toArray();
        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        // Halaman khusus yang bisa dikelola admin
        $halamanPejabat = Halaman::where('slug', 'profil-pejabat')->where('is_active', true)->first();
        $halamanKeuangan = Halaman::where('slug', 'laporan-keuangan')->where('is_active', true)->first();
        $halamanRenstra = Halaman::where('slug', 'rencana-strategis')->where('is_active', true)->first();

        return view('pages.ppid.daftar-informasi', compact(
            'kategoriList',
            'dokumenList',
            'kategoriSlug',
            'tahun',
            'q',
            'availableYears',
            'halamanPejabat',
            'halamanKeuangan',
            'halamanRenstra'
        ));
    }

    /**
     * Halaman Pratinjau Dokumen Informasi Publik (Embedded PDF Viewer).
     */
    public function detailDokumen(string $slug)
    {
        $dokumen = DokumenInformasi::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $dokumen->increment('dilihat');

        $dokumenTerkait = DokumenInformasi::where('is_published', true)
            ->where('id', '!=', $dokumen->id)
            ->where('kategori_id', $dokumen->kategori_id)
            ->take(5)
            ->get();

        return view('pages.ppid.dokumen-detail', compact('dokumen', 'dokumenTerkait'));
    }

    /**
     * Halaman Formulir Permohonan Informasi Publik.
     */
    public function formPermohonan()
    {
        return view('pages.ppid.permohonan');
    }

    /**
     * Kirim Formulir Permohonan Informasi Publik.
     */
    public function kirimPermohonan(PermohonanInformasiRequest $request)
    {
        $validated = $request->validated();
        $identitasFile = $request->file('identitas_file');

        $permohonan = $this->ppidService->submitPermohonan($validated, $identitasFile);

        return redirect()->route('ppid.permohonan')
            ->with('success', "Permohonan Anda berhasil dicatat dengan Nomor Registrasi: {$permohonan->nomor_registrasi}. Bukti registrasi resmi dan rincian tenggat waktu telah dikirimkan ke email {$permohonan->email}.")
            ->with('nomor_registrasi', $permohonan->nomor_registrasi);
    }

    /**
     * Akses berkas identitas privat yang terlindungi hak akses PPID.
     */
    public function downloadIdentitas(PermohonanInformasi $permohonan)
    {
        // Pastikan hanya petugas berwenang yang dapat mengakses
        if (!auth()->check() || (!auth()->user()->hasRole('Super Admin') && !auth()->user()->can('view_ppid'))) {
            abort(403, 'Akses ditolak. Berkas identitas pemohon bersifat rahasia dan terlindungi.');
        }

        if (empty($permohonan->identitas_file_path)) {
            abort(404, 'Berkas identitas tidak ditemukan.');
        }

        if (!Storage::disk('private')->exists($permohonan->identitas_file_path)) {
            abort(404, 'Berkas identitas pada penyimpanan privat tidak ditemukan.');
        }

        return Storage::disk('private')->response($permohonan->identitas_file_path);
    }

    /**
     * Halaman Pelacakan Publik (Fitur Gold & Platinum).
     */
    public function lacak(Request $request)
    {
        if (!Package::has('ppid_tracking')) {
            abort(403, 'Fitur pelacakan publik mandiri tidak aktif pada paket ini. Tanggapan permohonan dikirimkan melalui email resmi.');
        }

        $permohonan = null;
        $searched = false;
        $errorMessage = null;

        $nomor = trim((string) $request->input('nomor_registrasi', ''));
        $email = trim((string) $request->input('email', ''));

        if ($request->isMethod('post') || ($nomor !== '' && $email !== '')) {
            $searched = true;

            $request->validate([
                'nomor_registrasi' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:255'],
            ], [
                'nomor_registrasi.required' => 'Nomor registrasi wajib diisi.',
                'email.required' => 'Alamat email wajib diisi untuk verifikasi keamanan resi.',
                'email.email' => 'Format email tidak valid.',
            ]);

            $permohonan = PermohonanInformasi::with('status')
                ->where('nomor_registrasi', $nomor)
                ->where('email', $email)
                ->first();

            if (!$permohonan) {
                $errorMessage = 'Data permohonan tidak ditemukan. Pastikan kombinasi Nomor Registrasi dan Alamat Email sesuai dengan tanda terima pengajuan resmi Anda.';
            }
        }

        // Siapkan signed URL untuk unduhan dokumen jawaban jika tersedia
        $signedDownloadUrl = null;
        if ($permohonan && !empty($permohonan->jawaban_file_path)) {
            $signedDownloadUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'ppid.download-jawaban',
                now()->addHours(24),
                ['permohonan' => $permohonan->id]
            );
        }

        return view('pages.ppid.lacak', compact(
            'permohonan',
            'searched',
            'errorMessage',
            'nomor',
            'email',
            'signedDownloadUrl'
        ));
    }

    /**
     * Unduh berkas dokumen jawaban resmi lewat tautan bertanda tangan (Signed URL).
     */
    public function downloadJawaban(Request $request, PermohonanInformasi $permohonan)
    {
        if (!Package::has('ppid_tracking')) {
            abort(403, 'Fitur pelacakan publik mandiri tidak aktif pada paket ini.');
        }

        if (!$request->hasValidSignature()) {
            abort(401, 'Tautan unduhan tidak valid atau telah kedaluwarsa. Silakan lakukan pelacakan ulang untuk mendapatkan tautan baru.');
        }

        if (empty($permohonan->jawaban_file_path)) {
            abort(404, 'Berkas dokumen jawaban resmi belum tersedia.');
        }

        if (!Storage::disk('private')->exists($permohonan->jawaban_file_path)) {
            abort(404, 'Berkas jawaban tidak ditemukan di penyimpanan server.');
        }

        $ext = pathinfo($permohonan->jawaban_file_path, PATHINFO_EXTENSION) ?: 'pdf';
        $downloadName = 'Jawaban-PPID-' . \Illuminate\Support\Str::slug($permohonan->nomor_registrasi) . '.' . $ext;

        return Storage::disk('private')->download($permohonan->jawaban_file_path, $downloadName);
    }
}
