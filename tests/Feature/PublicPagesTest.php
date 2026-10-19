<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Uji halaman beranda publik.
     */
    public function test_halaman_beranda_dapat_diakses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Portal Resmi');
        $response->assertSee('UU No. 14 Tahun 2008');
        $response->assertSee('PT Hayat Jaya Teknologi');
    }

    /**
     * Uji halaman profil instansi & PPID.
     */
    public function test_halaman_profil_dapat_diakses(): void
    {
        $response = $this->get('/profil');

        $response->assertStatus(200);
        $response->assertSee('Profil Instansi');
        $response->assertSee('Visi & Misi', false);
    }

    /**
     * Uji halaman kontak instansi.
     */
    public function test_halaman_kontak_dapat_diakses(): void
    {
        $response = $this->get('/kontak');

        $response->assertStatus(200);
        $response->assertSee('Kontak Kami');
        $response->assertSee('Meja Layanan PPID');
    }
}
