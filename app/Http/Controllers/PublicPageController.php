<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Halaman;
use App\Models\Layanan;
use App\Models\PesanKontak;
use App\Models\Statistik;
use App\Services\ContentDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PublicPageController extends Controller
{
    public function __construct(
        protected ContentDataService $contentService
    ) {}

    /**
     * Halaman Beranda Publik.
     */
    public function home()
    {
        $currentUnit = \App\Facades\UnitContext::current();
        $beritaTerbaru = $this->contentService->getBerita(3);
        $pengumuman = $this->contentService->getBerita(2, 'Pengumuman');
        $agenda = $this->contentService->getAgenda();
        $emergencyBanner = $this->contentService->getEmergencyBanner();
        $heroBanners = $this->contentService->getBanners('slider');

        // Agregasi berita UPTD/Bidang di Portal Utama (Paket Platinum)
        $beritaAgregasi = [];
        if (! $currentUnit && \App\Support\Package::has('subdomains')) {
            $beritaAgregasi = $this->contentService->getBeritaAgregasi(6);
        }

        return view('pages.home', compact('beritaTerbaru', 'pengumuman', 'agenda', 'emergencyBanner', 'heroBanners', 'beritaAgregasi', 'currentUnit'));
    }

    /**
     * Halaman Profil Instansi & PPID.
     */
    public function profil()
    {
        $pejabat = $this->contentService->getPejabat();
        $halamanProfil = null;

        if (Schema::hasTable('halaman')) {
            $halamanProfil = Halaman::active()->where('slug', 'profil')->first();
        }

        return view('pages.profil', compact('pejabat', 'halamanProfil'));
    }

    /**
     * Halaman Pemerintahan & Kebijakan Wilayah.
     */
    public function pemerintahan()
    {
        $pejabat = Halaman::active()->where('slug', 'profil-pejabat')->first();
        $unitKerja = Halaman::active()->where('slug', 'unit-kerja')->first();
        $dasarHukum = Halaman::active()->where('slug', 'dasar-hukum')->first();
        $renstra = Halaman::active()->where('slug', 'rencana-strategis')->first();

        return view('pages.pemerintahan', compact('pejabat', 'unitKerja', 'dasarHukum', 'renstra'));
    }

    /**
     * Halaman Statis Dinamis (Profil, Visi Misi, Struktur Organisasi, dll.)
     */
    public function halaman(string $slug)
    {
        if (!Schema::hasTable('halaman')) {
            abort(404, 'Halaman yang Anda tuju tidak ditemukan.');
        }

        $halaman = Halaman::active()->where('slug', $slug)->first();

        if (!$halaman) {
            abort(404, 'Halaman yang Anda tuju tidak ditemukan.');
        }

        $halamanLain = Halaman::active()
            ->where('id', '!=', $halaman->id)
            ->where('show_in_menu', true)
            ->orderBy('urutan')
            ->take(6)
            ->get();

        return view('pages.halaman.show', compact('halaman', 'halamanLain'));
    }

    /**
     * Halaman Daftar Berita & Artikel.
     */
    public function berita(Request $request)
    {
        $kategori = $request->query('kategori');
        $q = $request->query('q');
        $isAgregasi = $request->boolean('agregasi') || $request->query('mode') === 'semua';

        if ($isAgregasi && \App\Support\Package::has('subdomains') && ! \App\Facades\UnitContext::hasUnit()) {
            $daftarBerita = $this->contentService->getBeritaAgregasi(12);
        } else {
            $daftarBerita = $this->contentService->getBerita(12, $kategori);
        }

        if ($q) {
            $daftarBerita = array_filter($daftarBerita, function ($item) use ($q) {
                return stripos($item['judul'], $q) !== false || stripos($item['ringkasan'], $q) !== false;
            });
        }

        $kategoriList = ['Keterbukaan Informasi', 'Kegiatan', 'Pengumuman', 'Prestasi', 'Pemerintahan', 'Pembangunan'];

        return view('pages.berita.index', compact('daftarBerita', 'kategoriList', 'kategori', 'q', 'isAgregasi'));
    }

    /**
     * Halaman Detail Baca Berita.
     */
    public function detailBerita(string $slug)
    {
        $berita = $this->contentService->findBeritaBySlug($slug);

        if (!$berita) {
            abort(404, 'Berita atau artikel yang Anda cari tidak ditemukan.');
        }

        $beritaTerkait = array_slice($this->contentService->getBerita(3), 0, 3);

        return view('pages.berita.detail', compact('berita', 'beritaTerkait'));
    }

    /**
     * Halaman Galeri Dokumentasi Foto Kegiatan & Infografis.
     */
    public function galeri()
    {
        $galeri = $this->contentService->getGaleri();

        return view('pages.galeri', compact('galeri'));
    }

    /**
     * Halaman Daftar Layanan Publik.
     */
    public function layanan()
    {
        $dbLayanan = \App\Models\Layanan::active()->get();
        if ($dbLayanan->isNotEmpty()) {
            $layananList = $dbLayanan;
        } else {
            $layananList = $this->contentService->getLayanan();
        }

        return view('pages.layanan', compact('layananList'));
    }

    /**
     * Halaman Detail Prosedur Layanan Publik.
     */
    public function detailLayanan(string $slug)
    {
        $layanan = \App\Models\Layanan::where('slug', $slug)->where('is_active', true)->first();

        if (!$layanan) {
            abort(404, 'Layanan publik yang Anda tuju tidak ditemukan.');
        }

        $layananLain = \App\Models\Layanan::active()->where('id', '!=', $layanan->id)->take(5)->get();

        return view('pages.layanan.detail', compact('layanan', 'layananLain'));
    }

    /**
     * Halaman Katalog Data & Statistik Wilayah.
     */
    public function statistik(Request $request)
    {
        $kategoriPilihan = $request->query('kategori');
        $tahunPilihan = $request->query('tahun');
        $q = $request->query('q');

        $query = Statistik::active();

        if ($kategoriPilihan && $kategoriPilihan !== 'semua') {
            $query->where('kategori', $kategoriPilihan);
        }

        if ($tahunPilihan) {
            $query->where('tahun', $tahunPilihan);
        }

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('sumber', 'like', "%{$q}%")
                    ->orWhere('isi', 'like', "%{$q}%");
            });
        }

        $daftarStatistik = $query->paginate(10)->withQueryString();

        $kategoriList = ['Kependudukan', 'Sosial & Ekonomi', 'Pendidikan', 'Kesehatan', 'Pertanian & Perikanan', 'Infrastruktur'];
        $tahunList = Statistik::where('is_active', true)
            ->distinct()
            ->pluck('tahun')
            ->filter()
            ->sortDesc()
            ->values()
            ->toArray();
        if (empty($tahunList)) {
            $tahunList = [(int) date('Y'), (int) date('Y') - 1];
        }

        return view('pages.statistik.index', compact(
            'daftarStatistik',
            'kategoriList',
            'tahunList',
            'kategoriPilihan',
            'tahunPilihan',
            'q'
        ));
    }

    /**
     * Halaman Detail Rincian Data Statistik.
     */
    public function detailStatistik(string $slug)
    {
        $statistik = Statistik::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $statistikLain = Statistik::active()
            ->where('id', '!=', $statistik->id)
            ->where('kategori', $statistik->kategori)
            ->take(4)
            ->get();

        if ($statistikLain->isEmpty()) {
            $statistikLain = Statistik::active()
                ->where('id', '!=', $statistik->id)
                ->take(4)
                ->get();
        }

        return view('pages.statistik.detail', compact('statistik', 'statistikLain'));
    }

    /**
     * Halaman Publikasi dan Dokumen Resmi.
     */
    public function publikasi(Request $request)
    {
        $kategori = $request->query('kategori');
        $daftarDokumen = $this->contentService->getPublikasi();

        if ($kategori) {
            $daftarDokumen = array_filter($daftarDokumen, fn($doc) => strtolower($doc['kategori']) === strtolower($kategori));
        }

        $kategoriList = ['Rencana Strategis', 'Perencanaan', 'Laporan Kinerja', 'Peraturan Daerah', 'SOP Teknis', 'Keuangan', 'Informasi Berkala'];

        return view('pages.publikasi', compact('daftarDokumen', 'kategoriList', 'kategori'));
    }

    /**
     * Halaman Kontak & Pengaduan.
     */
    public function kontak()
    {
        return view('pages.kontak');
    }

    /**
     * Kirim Pengaduan / Pesan Masyarakat.
     */
    public function kirimPengaduan(Request $request)
    {
        // Proteksi Honeypot Anti-Spam: jika field jebakan diisi bot, tolak tanpa simpan ke database
        if (!empty($request->input('_hp_security_check'))) {
            return redirect()->route('kontak')
                ->with('success', 'Terima kasih, pesan atau aspirasi Anda telah berhasil dikirimkan. Petugas kami akan menindaklanjuti.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'topik' => ['required', 'string', 'max:255'],
            'pesan' => ['required', 'string', 'max:2000'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'topik.required' => 'Subjek atau topik pesan wajib dipilih.',
            'pesan.required' => 'Uraian pengaduan atau pesan wajib diisi.',
        ]);

        PesanKontak::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'telepon' => $validated['telepon'] ?? null,
            'topik' => $validated['topik'],
            'pesan' => $validated['pesan'],
            'status' => 'belum_dibaca',
        ]);

        return redirect()->route('kontak')
            ->with('success', 'Terima kasih, pesan atau aspirasi Anda telah berhasil dikirimkan. Petugas kami akan menindaklanjuti melalui email atau nomor kontak Anda.');
    }

    /**
     * Halaman Showcase Komponen UI Standar (Paket Silver).
     */
    public function komponen()
    {
        return view('pages.komponen');
    }

    /**
     * SEO: Sitemap XML.
     */
    public function sitemap()
    {
        $urls = [
            ['loc' => url('/'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('profil'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('layanan'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => route('publikasi'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => route('berita.index'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('galeri'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => route('kontak'), 'lastmod' => now()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ];

        if (Schema::hasTable('berita')) {
            $beritas = Berita::published()->get();
            foreach ($beritas as $b) {
                $urls[] = [
                    'loc' => route('berita.detail', $b->slug),
                    'lastmod' => ($b->updated_at ?? now())->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }
        }

        if (Schema::hasTable('halaman')) {
            $halamans = Halaman::active()->get();
            foreach ($halamans as $h) {
                $urls[] = [
                    'loc' => route('halaman.show', $h->slug),
                    'lastmod' => ($h->updated_at ?? now())->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($u['loc']) . "</loc>\n";
            $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$u['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * SEO: Robots.txt.
     */
    public function robots()
    {
        $sitemapUrl = url('/sitemap.xml');
        $content = "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /livewire/\n\nSitemap: {$sitemapUrl}\n";

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}
