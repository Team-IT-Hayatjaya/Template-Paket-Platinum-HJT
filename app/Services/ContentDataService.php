<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Berita;
use App\Models\DokumenInformasi;
use App\Models\Galeri;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ContentDataService
{
    /**
     * Dapatkan daftar berita dan pengumuman.
     */
    public function getBerita(int $limit = 6, ?string $kategori = null): array
    {
        try {
            if (Schema::hasTable('berita')) {
                $query = Berita::published()
                    ->with(['author', 'unit'])
                    ->orderByDesc('tanggal_terbit')
                    ->orderByDesc('created_at');

                if ($kategori) {
                    $query->where('kategori', $kategori);
                }

                $records = $query->take($limit)->get();

                if ($records->isNotEmpty()) {
                    return $records->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'slug' => $item->slug,
                            'judul' => $item->judul,
                            'kategori' => $item->kategori,
                            'tanggal' => $item->tanggal_terbit?->translatedFormat('d F Y') ?? $item->created_at->translatedFormat('d F Y'),
                            'penulis' => $item->author?->name ?? 'Humas Instansi',
                            'ringkasan' => $item->ringkasan ?: Str::limit(strip_tags($item->isi), 160),
                            'isi' => $item->isi,
                            'gambar' => $item->gambar_utama ? asset('storage/' . $item->gambar_utama) : null,
                            'views_count' => $item->views_count,
                            'meta_title' => $item->meta_title,
                            'meta_description' => $item->meta_description,
                            'unit_id' => $item->unit_id,
                            'unit_nama' => $item->unit?->nama,
                            'unit_singkatan' => $item->unit?->singkatan,
                            'unit_subdomain' => $item->unit?->subdomain,
                        ];
                    })->toArray();
                }
            }
        } catch (\Throwable $e) {
            // Fallback gracefully on query error
        }

        return $this->getFallbackBerita($limit, $kategori);
    }

    /**
     * Dapatkan berita agregasi lintas unit/UPTD dan portal utama (Paket Platinum).
     */
    public function getBeritaAgregasi(int $limit = 6): array
    {
        try {
            if (Schema::hasTable('berita')) {
                $query = Berita::withoutGlobalScope(\App\Models\Scopes\UnitScope::class)
                    ->published()
                    ->with(['author', 'unit'])
                    ->orderByDesc('tanggal_terbit')
                    ->orderByDesc('created_at');

                $records = $query->take($limit)->get();

                if ($records->isNotEmpty()) {
                    return $records->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'slug' => $item->slug,
                            'judul' => $item->judul,
                            'kategori' => $item->kategori,
                            'tanggal' => $item->tanggal_terbit?->translatedFormat('d F Y') ?? $item->created_at->translatedFormat('d F Y'),
                            'penulis' => $item->author?->name ?? 'Humas Instansi',
                            'ringkasan' => $item->ringkasan ?: Str::limit(strip_tags($item->isi), 160),
                            'isi' => $item->isi,
                            'gambar' => $item->gambar_utama ? asset('storage/' . $item->gambar_utama) : null,
                            'views_count' => $item->views_count,
                            'meta_title' => $item->meta_title,
                            'meta_description' => $item->meta_description,
                            'unit_id' => $item->unit_id,
                            'unit_nama' => $item->unit?->nama,
                            'unit_singkatan' => $item->unit?->singkatan,
                            'unit_subdomain' => $item->unit?->subdomain,
                        ];
                    })->toArray();
                }
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return $this->getBerita($limit);
    }

    /**
     * Dapatkan detail satu berita berdasarkan slug.
     */
    public function findBeritaBySlug(string $slug): ?array
    {
        try {
            if (Schema::hasTable('berita')) {
                $item = Berita::published()->with('author')->where('slug', $slug)->first();
                if ($item) {
                    $item->increment('views_count');
                    return [
                        'id' => $item->id,
                        'slug' => $item->slug,
                        'judul' => $item->judul,
                        'kategori' => $item->kategori,
                        'tanggal' => $item->tanggal_terbit?->translatedFormat('d F Y') ?? $item->created_at->translatedFormat('d F Y'),
                        'penulis' => $item->author?->name ?? 'Humas Instansi',
                        'ringkasan' => $item->ringkasan ?: Str::limit(strip_tags($item->isi), 160),
                        'isi' => $item->isi,
                        'gambar' => $item->gambar_utama ? asset('storage/' . $item->gambar_utama) : null,
                        'views_count' => $item->views_count,
                        'meta_title' => $item->meta_title,
                        'meta_description' => $item->meta_description,
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Fallback gracefully on query error
        }

        $all = $this->getFallbackBerita(100);
        foreach ($all as $item) {
            if ($item['slug'] === $slug) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Dapatkan galeri foto dokumentasi & infografis.
     */
    public function getGaleri(): array
    {
        try {
            if (Schema::hasTable('galeri')) {
                $records = Galeri::published()->get();
                if ($records->isNotEmpty()) {
                    return $records->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'judul' => $item->judul,
                            'slug' => $item->slug,
                            'tipe' => $item->tipe,
                            'kategori' => $item->kategori,
                            'deskripsi' => $item->deskripsi,
                            'tanggal' => $item->tanggal?->translatedFormat('d F Y') ?? '',
                            'gambar' => $item->gambar ? asset('storage/' . $item->gambar) : null,
                        ];
                    })->toArray();
                }
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return $this->getFallbackGaleri();
    }

    /**
     * Dapatkan daftar publikasi dan dokumen resmi instansi.
     */
    public function getPublikasi(): array
    {
        try {
            if (Schema::hasTable('dokumen_informasi')) {
                $records = DokumenInformasi::where('is_published', true)
                    ->with('kategori')
                    ->orderByDesc('tahun')
                    ->orderByDesc('created_at')
                    ->get();

                if ($records->isNotEmpty()) {
                    return $records->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'judul' => $item->judul,
                            'keterangan' => $item->ringkasan,
                            'kategori' => $item->kategori?->nama ?? 'Umum',
                            'tahun' => (string) $item->tahun,
                            'format' => strtoupper($item->tipe_file ?: 'PDF'),
                            'ukuran' => $item->ukuran_file ?: 'PDF',
                            'url' => asset('storage/' . $item->file_path),
                            'diunduh' => $item->diunduh,
                        ];
                    })->toArray();
                }
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return $this->getFallbackPublikasi();
    }

    /**
     * Dapatkan banner beranda atau pengumuman.
     */
    public function getBanners(string $tipe = 'slider')
    {
        try {
            if (Schema::hasTable('banners')) {
                return Banner::active()->where('tipe', $tipe)->get();
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return collect();
    }

    /**
     * Dapatkan banner pengumuman / darurat teratas.
     */
    public function getEmergencyBanner(): ?Banner
    {
        try {
            if (Schema::hasTable('banners')) {
                return Banner::active()->whereIn('tipe', ['darurat', 'pengumuman'])->first();
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return null;
    }

    /**
     * Fallback data berita jika database kosong.
     */
    protected function getFallbackBerita(int $limit = 6, ?string $kategori = null): array
    {
        $all = [
            [
                'id' => 1,
                'slug' => 'pemerintah-luncurkan-portal-keterbukaan-informasi-publik',
                'judul' => 'Pemerintah Luncurkan Portal Keterbukaan Informasi Publik Terpadu',
                'kategori' => 'Keterbukaan Informasi',
                'tanggal' => '09 Oktober 2026',
                'penulis' => 'Humas PPID',
                'ringkasan' => 'Optimalisasi pelayanan informasi publik sesuai amanat UU No. 14 Tahun 2008 dengan penyediaan Daftar Informasi Publik secara transparan dan mudah diakses masyarakat.',
                'isi' => 'Pemerintah daerah resmi meluncurkan portal layanan Pejabat Pengelola Informasi dan Dokumentasi (PPID) terpadu. Langkah ini diambil guna memperkuat implementasi Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik. Melalui portal ini, masyarakat dapat dengan mudah mengakses dokumen informasi berkala, mengajukan permohonan informasi publik secara daring, serta menyampaikan keberatan apabila terdapat kendala dalam pemenuhan hak informasi. Kepala Dinas menyampaikan bahwa transparansi adalah pilar utama pelayanan masyarakat di era keterbukaan.',
                'gambar' => asset('images/berita-musrenbang.jpg'),
            ],
            [
                'id' => 2,
                'slug' => 'bimtek-pengelolaan-dokumentasi-dan-pelayanan-ppid',
                'judul' => 'Semarak Kebudayaan Pesisir & Festival Wisata Tepian Mahakam Loa Kulu',
                'kategori' => 'Kebudayaan',
                'tanggal' => '07 Oktober 2026',
                'penulis' => 'Bidang Informasi Publik',
                'ringkasan' => 'Perahu hias tradisional berlomba melintasi perairan Sungai Mahakam dalam rangka Festival Kebudayaan Pesisir Kutai Kartanegara.',
                'isi' => 'Kegiatan budaya pesisir Kutai Kartanegara di Tepian Mahakam Loa Kulu dihadiri ribuan masyarakat dan wisatawan lokal. Acara ini memadukan olahraga tradisional balap perahu naga dengan pertunjukan seni tari khas pedalaman dan pesisir.',
                'gambar' => asset('images/berita-festival.jpg'),
            ],
            [
                'id' => 3,
                'slug' => 'pengumuman-jadwal-pelayanan-meja-ppid-triwulan-iv',
                'judul' => 'Musrenbang Kecamatan Loa Kulu Sepakati Prioritas Pembangunan Desa',
                'kategori' => 'Pembangunan',
                'tanggal' => '04 Oktober 2026',
                'penulis' => 'Sekretariat Kecamatan',
                'ringkasan' => 'Musyawarah Perencanaan Pembangunan tingkat kecamatan memprioritaskan semenisasi jalan usaha tani dan tanggul banjir.',
                'isi' => 'Forum musyawarah antar-desa menekankan percepatan semenisasi jalan usaha tani di wilayah Pongkor dan peningkatan sarana tanggul penahan banjir di sepanjang bantaran Sungai Mahakam untuk mendukung ketahanan pangan lokal.',
                'gambar' => asset('images/berita-musrenbang.jpg'),
            ],
            [
                'id' => 4,
                'slug' => 'sosialisasi-uji-konsekuensi-informasi-dikecualikan',
                'judul' => 'Sosialisasi Hasil Uji Konsekuensi Informasi yang Dikecualikan',
                'kategori' => 'Keterbukaan Informasi',
                'tanggal' => '28 September 2026',
                'penulis' => 'Tim Advokasi PPID',
                'ringkasan' => 'Penyampaian ringkasan hasil pengujian konsekuensi terhadap informasi yang dikecualikan sesuai dengan Pasal 17 UU No. 14 Tahun 2008.',
                'isi' => 'PPID Utama telah menyelesaikan rapat pleno penetapan klasifikasi informasi yang dikecualikan berdasarkan uji konsekuensi yang ketat. Hasil pengujian memastikan perlindungan terhadap data pribadi, rahasia negara, dan strategi penegakan hukum tanpa mengurangi hak masyarakat atas informasi terbuka lainnya.',
                'gambar' => asset('images/berita-musrenbang.jpg'),
            ],
            [
                'id' => 5,
                'slug' => 'penghargaan-keterbukaan-informasi-badan-publik-terbaik',
                'judul' => 'Raih Predikat Badan Publik Informatif Tingkat Provinsi Tahun 2026',
                'kategori' => 'Prestasi',
                'tanggal' => '20 September 2026',
                'penulis' => 'Humas Protokol',
                'ringkasan' => 'Komisi Informasi Provinsi memberikan apresiasi predikat Informatif atas komitmen penyediaan informasi publik yang konsisten.',
                'isi' => 'Pemerintah daerah kembali meraih predikat Badan Publik Informatif dengan skor 98,5 dalam evaluasi kepatuhan keterbukaan informasi publik tahun 2026. Penghargaan ini menjadi motivasi untuk terus meningkatkan kualitas pelayanan informasi bagi masyarakat luas.',
                'gambar' => asset('images/berita-festival.jpg'),
            ],
            [
                'id' => 6,
                'slug' => 'konsultasi-publik-rancangan-peraturan-daerah-infrastruktur',
                'judul' => 'Konsultasi Publik Rancangan Peraturan Daerah Penataan Wilayah',
                'kategori' => 'Kegiatan',
                'tanggal' => '15 September 2026',
                'penulis' => 'Humas Sekretariat',
                'ringkasan' => 'Masyarakat diundang memberikan masukan dan saran terkait draf Rancangan Peraturan Daerah Penataan Wilayah Terbuka Hijau.',
                'isi' => 'Dalam rangka penyusunan kebijakan partisipatif, naskah akademik dan rancangan perda dapat diunduh melalui menu Publikasi Dokumen. Masukan dapat dikirimkan melalui meja konsultasi atau kanal kontak resmi yang tersedia.',
                'gambar' => asset('images/berita-musrenbang.jpg'),
            ],
        ];

        if ($kategori) {
            $filtered = array_filter($all, fn($item) => strtolower($item['kategori']) === strtolower($kategori));
            return array_slice(array_values($filtered), 0, $limit);
        }

        return array_slice($all, 0, $limit);
    }

    /**
     * Dapatkan daftar agenda kegiatan instansi.
     */
    public function getAgenda(): array
    {
        return [
            [
                'tanggal' => '15 Oktober 2026',
                'waktu' => '09.00 WIB',
                'acara' => 'Sosialisasi Hak Masyarakat atas Keterbukaan Informasi Publik',
                'lokasi' => 'Aula Gedung Pertemuan Lt. 3',
            ],
            [
                'tanggal' => '22 Oktober 2026',
                'waktu' => '13.30 WIB',
                'acara' => 'Rapat Koordinasi Pengelola PPID Pelaksana se-Kabupaten/Kota',
                'lokasi' => 'Ruang Rapat Utama Sekretariat Daerah',
            ],
            [
                'tanggal' => '05 November 2026',
                'waktu' => '08.30 WIB',
                'acara' => 'Uji Publik Pemutakhiran Daftar Informasi Publik Semester II',
                'lokasi' => 'Balai Media Center Diskominfo',
            ],
        ];
    }

    /**
     * Dapatkan daftar profil pimpinan dan pejabat instansi.
     */
    public function getPejabat(): array
    {
        return [
            [
                'nama' => 'Dr. H. Raden Santoso, M.Si.',
                'jabatan' => 'Kepala Dinas / Atasan PPID',
                'nip' => '19750812 199903 1 002',
                'pendidikan' => 'S3 Kebijakan Publik Universitas Indonesia',
                'foto' => 'images/pejabat-camat.jpg',
            ],
            [
                'nama' => 'Ir. Hj. Sri Wahyuni, M.Si.',
                'jabatan' => 'Sekretaris Dinas / Ketua PPID Utama',
                'nip' => '19800415 200312 2 001',
                'pendidikan' => 'S2 Teknik Informatika Institut Teknologi Bandung',
                'foto' => 'images/pejabat-sekretaris.jpg',
            ],
            [
                'nama' => 'Bambang Supriyadi, S.Kom., M.Eng.',
                'jabatan' => 'Kepala Bidang Pengelolaan Informasi Publik',
                'nip' => '19850620 200801 1 003',
                'pendidikan' => 'S2 Sistem Informasi Universitas Gadjah Mada',
                'foto' => 'images/pejabat-kabid.jpg',
            ],
            [
                'nama' => 'Dewi Anggraini, S.H., M.H.',
                'jabatan' => 'Kepala Subbagian Hukum & Advokasi Sengketa PPID',
                'nip' => '19890210 201101 2 004',
                'pendidikan' => 'S2 Hukum Tata Negara Universitas Diponegoro',
                'foto' => 'images/pejabat-kasi.jpg',
            ],
        ];
    }

    /**
     * Dapatkan daftar layanan publik instansi.
     */
    public function getLayanan(): array
    {
        return [
            [
                'id' => 'permohonan-informasi',
                'nama' => 'Pelayanan Permohonan Informasi Publik (PPID)',
                'deskripsi' => 'Layanan pemberian akses dan salinan dokumen publik kepada warga negara atau badan hukum sesuai UU No. 14/2008.',
                'persyaratan' => [
                    'Fotokopi / Scan KTP pemohon perseorangan atau Akta Pendirian bagi badan hukum.',
                    'Mengisi formulir permohonan informasi secara lengkap.',
                    'Menyertakan surat kuasa apabila dikuasakan kepada pihak lain.',
                ],
                'alur' => '1. Pengajuan Formulir -> 2. Verifikasi Berkas (1-2 hari) -> 3. Pemberitahuan Tertulis (maks. 10 hari kerja) -> 4. Penyerahan Dokumen.',
                'biaya' => 'Gratis (Rp 0). Biaya fotokopi atau media rekam mandiri ditanggung pemohon.',
                'waktu' => 'Maksimal 10 (sepuluh) hari kerja, dapat diperpanjang 7 hari kerja.',
            ],
            [
                'id' => 'keberatan-informasi',
                'nama' => 'Pelayanan Pengajuan Keberatan Informasi Publik',
                'deskripsi' => 'Fasilitas keberatan apabila permohonan informasi tidak ditanggapi, ditolak, atau biaya tidak wajar.',
                'persyaratan' => [
                    'Nomor tanda bukti registrasi permohonan awal.',
                    'Kartu identitas pemohon yang masih berlaku.',
                    'Alasan pengajuan keberatan yang jelas dan objektif.',
                ],
                'alur' => '1. Pengajuan Formulir Keberatan -> 2. Diteruskan ke Atasan PPID -> 3. Tanggapan Tertulis Atasan PPID (maks. 30 hari kerja).',
                'biaya' => 'Gratis (Rp 0).',
                'waktu' => 'Maksimal 30 (tiga puluh) hari kerja sejak keberatan dicatat.',
            ],
            [
                'id' => 'konsultasi-luring',
                'nama' => 'Layanan Meja Informasi & Konsultasi Tatap Muka',
                'deskripsi' => 'Konsultasi langsung di meja lobi kantor dinas terkait ketersediaan informasi publik dan petunjuk teknis.',
                'persyaratan' => [
                    'Kunjungan langsung pada jam kerja kedinasan.',
                    'Mengisi buku tamu pelayanan PPID.',
                ],
                'alur' => '1. Datang ke Meja Layanan -> 2. Petugas memberikan penjelasan dan memandu pengisian dokumen.',
                'biaya' => 'Gratis (Rp 0).',
                'waktu' => 'Langsung pada saat kunjungan kerja.',
            ],
        ];
    }

    /**
     * Fallback data dokumen publikasi jika database kosong.
     */
    protected function getFallbackPublikasi(): array
    {
        return [
            [
                'judul' => 'Rencana Strategis (Renstra) Dinas Komunikasi & Informatika 2024 - 2029',
                'keterangan' => 'Rencana strategis jangka menengah dinas dalam transformasi digital daerah.',
                'kategori' => 'Rencana Strategis',
                'tahun' => '2024',
                'format' => 'PDF',
                'ukuran' => '4.2 MB',
                'url' => '#',
            ],
            [
                'judul' => 'Rencana Kerja Pemerintah Daerah (RKPD) Tahun Anggaran 2026',
                'keterangan' => 'Dokumen perencanaan tahunan arah pembangunan dan penganggaran daerah.',
                'kategori' => 'Perencanaan',
                'tahun' => '2026',
                'format' => 'PDF',
                'ukuran' => '3.5 MB',
                'url' => '#',
            ],
            [
                'judul' => 'Laporan Akuntabilitas Kinerja Instansi Pemerintah (LAKIP) Tahun 2025',
                'keterangan' => 'Laporan capaian indikator kinerja utama dan realisasi program kerja dinas.',
                'kategori' => 'Laporan Kinerja',
                'tahun' => '2025',
                'format' => 'PDF',
                'ukuran' => '2.8 MB',
                'url' => '#',
            ],
            [
                'judul' => 'Peraturan Daerah tentang Penyelenggaraan Keterbukaan Informasi Publik',
                'keterangan' => 'Regulasi daerah yang mendasari pembentukan dan kewenangan PPID daerah.',
                'kategori' => 'Peraturan Daerah',
                'tahun' => '2023',
                'format' => 'PDF',
                'ukuran' => '1.5 MB',
                'url' => '#',
            ],
            [
                'judul' => 'Standar Operasional Prosedur (SOP) Pengelolaan Permohonan Informasi Publik',
                'keterangan' => 'Alur dan tata cara penanganan permohonan informasi publik dari awal hingga respon.',
                'kategori' => 'SOP Teknis',
                'tahun' => '2025',
                'format' => 'PDF',
                'ukuran' => '920 KB',
                'url' => '#',
            ],
            [
                'judul' => 'Ringkasan Laporan Keuangan Pemerintah Daerah (Audited) TA 2025',
                'keterangan' => 'Neraca dan laporan realisasi anggaran yang telah diaudit oleh BPK.',
                'kategori' => 'Keuangan',
                'tahun' => '2025',
                'format' => 'PDF',
                'ukuran' => '3.1 MB',
                'url' => '#',
            ],
        ];
    }

    /**
     * Fallback galeri jika database kosong.
     */
    protected function getFallbackGaleri(): array
    {
        return [
            [
                'judul' => 'Sosialisasi Keterbukaan Informasi Publik bagi Pelajar & Mahasiswa',
                'tanggal' => '05 Oktober 2026',
                'kategori' => 'Edukasi',
                'gambar' => asset('images/berita-musrenbang.jpg'),
            ],
            [
                'judul' => 'Pelatihan Digitalisasi Arsip dan Dokumentasi Pejabat PPID Pelaksana',
                'tanggal' => '29 September 2026',
                'kategori' => 'Pelatihan',
                'gambar' => asset('images/galeri-rapat.jpg'),
            ],
            [
                'judul' => 'Kunjungan Kerja Tim Komisi Informasi Daerah dalam Rangka Monev',
                'tanggal' => '18 September 2026',
                'kategori' => 'Monev',
                'gambar' => asset('images/galeri-pelayanan.jpg'),
            ],
            [
                'judul' => 'Pemberian Layanan Tatap Muka di Meja Informasi Kantor Sekretariat',
                'tanggal' => '10 September 2026',
                'kategori' => 'Pelayanan',
                'gambar' => asset('images/galeri-pelayanan.jpg'),
            ],
            [
                'judul' => 'Rapat Pleno Penyusunan Daftar Informasi Publik Semester II Tahun 2026',
                'tanggal' => '02 September 2026',
                'kategori' => 'Rapat Kerja',
                'gambar' => asset('images/galeri-rapat.jpg'),
            ],
            [
                'judul' => 'Semarak Seni Budaya Festival Tepian Mahakam Loa Kulu',
                'tanggal' => '25 Agustus 2026',
                'kategori' => 'Kebudayaan',
                'gambar' => asset('images/berita-festival.jpg'),
            ],
        ];
    }
}
