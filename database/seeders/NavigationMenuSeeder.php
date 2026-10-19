<?php

namespace Database\Seeders;

use App\Models\NavigationMenu;
use Illuminate\Database\Seeder;

class NavigationMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'nama' => 'Beranda',
                'url' => '/',
                'urutan' => 1,
                'target' => '_self',
                'is_active' => true,
                'submenus' => [],
            ],
            [
                'nama' => 'Profil',
                'url' => '/profil',
                'urutan' => 2,
                'target' => '_self',
                'is_active' => true,
                'submenus' => [
                    ['nama' => 'Sejarah', 'url' => '/halaman/sejarah', 'urutan' => 1],
                    ['nama' => 'Visi dan Misi', 'url' => '/halaman/visi-misi', 'urutan' => 2],
                    ['nama' => 'Struktur Organisasi', 'url' => '/halaman/struktur-organisasi', 'urutan' => 3],
                    ['nama' => 'Tugas dan Fungsi', 'url' => '/halaman/tugas-fungsi', 'urutan' => 4],
                ],
            ],
            [
                'nama' => 'Pemerintahan',
                'url' => '/pemerintahan',
                'urutan' => 3,
                'target' => '_self',
                'is_active' => true,
                'submenus' => [
                    ['nama' => 'Profil Pejabat', 'url' => '/halaman/profil-pejabat', 'urutan' => 1],
                    ['nama' => 'Unit Kerja', 'url' => '/halaman/unit-kerja', 'urutan' => 2],
                    ['nama' => 'Dasar Hukum', 'url' => '/halaman/dasar-hukum', 'urutan' => 3],
                    ['nama' => 'Rencana Strategis', 'url' => '/halaman/rencana-strategis', 'urutan' => 4],
                ],
            ],
            [
                'nama' => 'Layanan Publik',
                'url' => '/layanan',
                'urutan' => 4,
                'target' => '_self',
                'is_active' => true,
                'submenus' => [],
            ],
            [
                'nama' => 'Informasi Publik / PPID',
                'url' => '/ppid/daftar-informasi',
                'urutan' => 5,
                'target' => '_self',
                'is_active' => true,
                'submenus' => array_merge([
                    ['nama' => 'Daftar Informasi Publik', 'url' => '/ppid/daftar-informasi', 'urutan' => 1],
                    ['nama' => 'Laporan Keuangan', 'url' => '/halaman/laporan-keuangan', 'urutan' => 2],
                    ['nama' => 'Formulir Permohonan', 'url' => '/ppid/permohonan', 'urutan' => 3],
                ], \App\Support\Package::has('ppid_tracking') ? [
                    ['nama' => 'Lacak Resi Permohonan', 'url' => '/ppid/lacak', 'urutan' => 4],
                    ['nama' => 'Profil PPID', 'url' => '/halaman/profil-ppid', 'urutan' => 5],
                ] : [
                    ['nama' => 'Profil PPID', 'url' => '/halaman/profil-ppid', 'urutan' => 4],
                ]),
            ],
            [
                'nama' => 'Data & Statistik',
                'url' => '/data-statistik',
                'urutan' => 6,
                'target' => '_self',
                'is_active' => true,
                'submenus' => [],
            ],
            [
                'nama' => 'Publikasi',
                'url' => '/berita',
                'urutan' => 7,
                'target' => '_self',
                'is_active' => true,
                'submenus' => [
                    ['nama' => 'Berita', 'url' => '/berita', 'urutan' => 1],
                    ['nama' => 'Pengumuman', 'url' => '/berita?kategori=Pengumuman', 'urutan' => 2],
                    ['nama' => 'Galeri', 'url' => '/galeri', 'urutan' => 3],
                    ['nama' => 'Unduhan', 'url' => '/publikasi', 'urutan' => 4],
                ],
            ],
            [
                'nama' => 'Kontak',
                'url' => '/kontak',
                'urutan' => 8,
                'target' => '_self',
                'is_active' => true,
                'submenus' => [],
            ],
        ];

        foreach ($menus as $menuData) {
            $submenus = $menuData['submenus'];
            unset($menuData['submenus']);

            $menu = NavigationMenu::updateOrCreate(
                ['nama' => $menuData['nama'], 'parent_id' => null],
                $menuData
            );

            foreach ($submenus as $sub) {
                NavigationMenu::updateOrCreate(
                    ['nama' => $sub['nama'], 'parent_id' => $menu->id],
                    [
                        'url' => $sub['url'],
                        'urutan' => $sub['urutan'],
                        'target' => '_self',
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
