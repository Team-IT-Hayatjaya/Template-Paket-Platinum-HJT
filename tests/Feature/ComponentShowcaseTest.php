<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComponentShowcaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji halaman katalog komponen UI (/komponen).
     */
    public function test_halaman_komponen_dapat_diakses(): void
    {
        $response = $this->get('/komponen');

        $response->assertStatus(200);
        $response->assertSee('Katalog & Demonstrasi Komponen UI Standar', false);
        $response->assertSee('Komponen Tombol');
        $response->assertSee('Komponen Pemberitahuan');
        $response->assertSee('Komponen Input Formulir');
        $response->assertSee('Komponen Kartu Berita');
        $response->assertSee('Komponen Tabel Dokumen');
        $response->assertSee('Komponen Paginasi');
    }

    /**
     * Uji bahwa komponen aksesibel terpasang dengan atribut ARIA yang benar.
     */
    public function test_komponen_memiliki_atribut_aksesibilitas(): void
    {
        $response = $this->get('/komponen');

        $response->assertStatus(200);
        $response->assertSee('role="alert"', false);
        $response->assertSee('aria-label="Navigasi Halaman Data"', false);
        $response->assertSee('aria-current="page"', false);
        $response->assertSee('aria-describedby', false);
        $response->assertSee('overflow-x-auto', false);
    }
}
