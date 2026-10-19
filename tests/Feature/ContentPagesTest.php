<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji halaman beranda memuat komponen sambutan dan agenda.
     */
    public function test_halaman_beranda_memuat_sambutan_dan_agenda(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sambutan Pimpinan');
        $response->assertSee('Agenda Kegiatan');
        $response->assertSee('Pengumuman');
    }

    /**
     * Uji halaman profil memuat sejarah, visi misi, dan jajaran pejabat.
     */
    public function test_halaman_profil_memuat_sejarah_dan_pejabat(): void
    {
        $response = $this->get('/profil');

        $response->assertStatus(200);
        $response->assertSee('Sejarah Singkat Instansi');
        $response->assertSee('Visi & Misi', false);
        $response->assertSee('Profil Pejabat Struktural');
    }

    /**
     * Uji halaman arsip berita dapat dibuka dan difilter.
     */
    public function test_halaman_berita_dapat_difilter_dan_dicari(): void
    {
        $response = $this->get('/berita?q=Portal');

        $response->assertStatus(200);
        $response->assertSee('Berita & Siaran Pers Resmi', false);
        $response->assertSee('Pemerintah Luncurkan Portal');
    }

    /**
     * Uji halaman detail berita berhasil dibuka dengan slug valid.
     */
    public function test_halaman_detail_berita_berhasil_dibuka(): void
    {
        $slug = 'pemerintah-luncurkan-portal-keterbukaan-informasi-publik';
        $response = $this->get("/berita/{$slug}");

        $response->assertStatus(200);
        $response->assertSee('Pemerintah Luncurkan Portal Keterbukaan Informasi Publik Terpadu');
        $response->assertSee('Kembali ke Daftar Berita');
    }

    /**
     * Uji detail berita mengembalikan 404 jika slug tidak valid.
     */
    public function test_detail_berita_mengembalikan_404_jika_slug_tidak_ditemukan(): void
    {
        $response = $this->get('/berita/slug-tidak-ada');

        $response->assertStatus(404);
    }

    /**
     * Uji halaman galeri dokumentasi kegiatan.
     */
    public function test_halaman_galeri_dapat_diakses(): void
    {
        $response = $this->get('/galeri');

        $response->assertStatus(200);
        $response->assertSee('Galeri Kegiatan & Pelayanan Publik', false);
        $response->assertSee('Dokumentasi Foto');
    }

    /**
     * Uji halaman standar pelayanan publik.
     */
    public function test_halaman_layanan_dapat_diakses_dan_memuat_maklumat(): void
    {
        $response = $this->get('/layanan');

        $response->assertStatus(200);
        $response->assertSee('Layanan Publik & Maklumat Prosedur', false);
        $response->assertSee('Maklumat Standar Pelayanan Publik');
        $response->assertSee('Pelayanan Permohonan Informasi Publik (PPID)');
    }

    /**
     * Uji halaman publikasi dokumen resmi.
     */
    public function test_halaman_publikasi_dapat_diakses_dengan_tabel_dokumen(): void
    {
        $response = $this->get('/publikasi');

        $response->assertStatus(200);
        $response->assertSee('Publikasi & Dokumen Resmi', false);
        $response->assertSee('Rencana Strategis');
        $response->assertSee('Unduh');
    }

    /**
     * Uji formulir pengaduan masyarakat berhasil dikirim.
     */
    public function test_pengiriman_formulir_pengaduan_masyarakat_berhasil(): void
    {
        $response = $this->post('/kontak', [
            'nama' => 'Budi Pratama',
            'email' => 'budi@example.com',
            'telepon' => '08123456789',
            'topik' => 'pelayanan_ppid',
            'pesan' => 'Mohon petunjuk jam operasional meja layanan PPID untuk konsultasi.',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('kontak'));
        $response->assertSessionHas('success');
    }

    /**
     * Uji pengiriman pengaduan gagal jika kolom wajib tidak diisi.
     */
    public function test_pengiriman_pengaduan_gagal_jika_data_tidak_lengkap(): void
    {
        $response = $this->from('/kontak')->post('/kontak', [
            'nama' => '',
            'email' => 'email-salah',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/kontak');
        $response->assertSessionHasErrors(['nama', 'email', 'topik', 'pesan']);
    }

    /**
     * Uji katalog data statistik dapat diakses tanpa query conflict DISTINCT MySQL.
     */
    public function test_halaman_data_statistik_dapat_diakses_tanpa_error_query(): void
    {
        $response = $this->get('/data-statistik');

        $response->assertStatus(200);
        $response->assertSee('Katalog Data &amp; Statistik', false);
        $response->assertSee('Semua Kategori');
    }

    /**
     * Uji halaman detail statistik memuat grafik interaktif dan tabel.
     */
    public function test_halaman_detail_statistik_memuat_grafik_dan_tabel(): void
    {
        $stat = \App\Models\Statistik::create([
            'judul' => 'Statistik Uji Kependudukan',
            'slug' => 'statistik-uji-kependudukan',
            'kategori' => 'Kependudukan',
            'periode' => 'Tahun 2026',
            'tahun' => 2026,
            'is_active' => true,
            'tipe_grafik' => 'bar',
            'data_grafik' => [
                'labels' => ['Desa A', 'Desa B'],
                'datasets' => [
                    [
                        'label' => 'Jumlah',
                        'data' => [100, 200],
                    ],
                ],
                'summary' => [
                    'total' => 300,
                    'rata_rata' => 150,
                    'tertinggi' => 200,
                    'terendah' => 100,
                    'jumlah_baris' => 2,
                ],
            ],
            'data_tabel' => [
                'headers' => ['Desa', 'Jumlah'],
                'rows' => [
                    ['Desa A', 100],
                    ['Desa B', 200],
                ],
            ],
        ]);

        $response = $this->get('/data-statistik/' . $stat->slug);

        $response->assertStatus(200);
        $response->assertSee('Visualisasi Grafik Interaktif');
        $response->assertSee('canvasStatistik');
        $response->assertSee('dynamicDataTable');
        $response->assertSee('Desa A');
    }

    /**
     * Uji parser service mengolah baris spreadsheet menjadi dataset grafik.
     */
    public function test_parser_service_mengolah_data_tabular_ke_grafik(): void
    {
        $parser = new \App\Services\StatistikDataParserService();
        $rawRows = [
            ['Kecamatan', 'Produksi Padi (Ton)', 'Produksi Jagung (Ton)'],
            ['Wilayah 1', 1200, 450],
            ['Wilayah 2', 1500, 600],
        ];

        $result = $parser->processRawRows($rawRows);

        $this->assertNotNull($result['data_grafik']);
        $this->assertCount(2, $result['data_grafik']['labels']);
        $this->assertEquals(['Wilayah 1', 'Wilayah 2'], $result['data_grafik']['labels']);
        $this->assertCount(2, $result['data_grafik']['datasets']);
        $this->assertEquals(2700, $result['data_grafik']['summary']['total']);
    }
}
