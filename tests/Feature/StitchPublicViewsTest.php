<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StitchPublicViewsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji halaman layanan publik memuat 4 langkah alur PATEN dan katalog layanan.
     */
    public function test_halaman_layanan_memuat_alur_paten_dan_katalog(): void
    {
        $response = $this->get('/layanan');

        $response->assertStatus(200);
        $response->assertSee('Pusat Pelayanan Administrasi Terpadu (PATEN)');
        $response->assertSee('Alur 4 Langkah Pengurusan Dokumen');
        $response->assertSee('Penyerahan Berkas');
        $response->assertSee('Verifikasi Kasi Terkait');
        $response->assertSee('Penandatanganan (TTE)');
        $response->assertSee('Pengambilan & SKM', false);
        $response->assertSee('Maklumat Standar Pelayanan Publik');
        $response->assertSee('WhatsApp Helpdesk');
    }

    /**
     * Uji halaman publikasi dokumen dan PPID memuat klasifikasi dan statistik.
     */
    public function test_halaman_publikasi_memuat_klasifikasi_dan_statistik(): void
    {
        $response = $this->get('/publikasi');

        $response->assertStatus(200);
        $response->assertSee('Publikasi & Dokumen Resmi');
        $response->assertSee('UU No. 14 Tahun 2008 Tentang Keterbukaan Informasi Publik');
        $response->assertSee('Informasi Berkala');
        $response->assertSee('Informasi Setiap Saat');
        $response->assertSee('Informasi Serta Merta');
        $response->assertSee('Dashboard Statistik');
        $response->assertSee('Formulir Permohonan Online');
    }

    /**
     * Uji halaman 404 menampilkan layout kedinasan resmi yang elegan.
     */
    public function test_halaman_tidak_ditemukan_menampilkan_tampilan_404_resmi(): void
    {
        $response = $this->get('/rute-halaman-pasti-tidak-ada-xyz-123');

        $response->assertStatus(404);
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('Kembali ke Beranda');
    }

    /**
     * Uji perubahan dinamis identitas instansi di site_settings tampil di layout publik.
     */
    public function test_identitas_instansi_dinamis_tampil_pada_layout_publik(): void
    {
        SiteSetting::query()->delete();
        SiteSetting::create([
            'institution_name' => 'Dinas Koperasi dan UKM Kabupaten Kutai Kartanegara',
            'institution_acronym' => 'Diskop UKM',
            'email' => 'diskop@kukarkab.go.id',
            'phone' => '081299998888',
            'address' => 'Jl. Kebudayaan No. 10, Tenggarong',
            'primary_color' => '#14304F',
            'secondary_color' => '#C9A227',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dinas Koperasi dan UKM Kabupaten Kutai Kartanegara');
        $response->assertSee('Diskop UKM');
    }
}
