@extends('layouts.app')

@section('title', 'Katalog Komponen UI Standar Instansi')

@section('content')
<div class="space-y-12">
    <!-- Header Halaman Komponen -->
    <div class="border-b border-slate-200 pb-6">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold text-blue-800 bg-blue-100 mb-2">
            <span>Paket SILVER</span> &bull; <span>PT Hayat Jaya Teknologi (HJT)</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Katalog & Demonstrasi Komponen UI Standar
        </h1>
        <p class="text-sm sm:text-base text-slate-600 mt-2 max-w-3xl leading-relaxed">
            Halaman ini mendemonstrasikan sistem desain dan komponen Blade standar website instansi pemerintah. Seluruh komponen dirancang mobile-first, memenuhi standar kontras, kontrol keyboard, dan dapat digunakan kembali untuk paket Silver, Gold, maupun Platinum.
        </p>
    </div>

    <!-- 1. Tombol (Buttons) -->
    <section class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm" aria-labelledby="heading-buttons">
        <h2 id="heading-buttons" class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
            <span>🔘</span> 1. Komponen Tombol (<code class="text-xs bg-slate-100 px-2 py-0.5 rounded text-blue-700">&lt;x-button /&gt;</code>)
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 mb-6">
            Mendukung berbagai varian semantik, ukuran, serta state disabled. Status fokus terlihat jelas saat menggunakan tombol <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-300 rounded text-xs">Tab</kbd>.
        </p>

        <!-- Variasi Varian -->
        <div class="mb-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Varian Tombol</h3>
            <div class="flex flex-wrap items-center gap-3">
                <x-button variant="primary">Primary (Warna Tema)</x-button>
                <x-button variant="secondary">Secondary</x-button>
                <x-button variant="danger">Danger / Keberatan</x-button>
                <x-button variant="outline">Outline</x-button>
                <x-button variant="light">Light</x-button>
                <x-button variant="ghost">Ghost</x-button>
                <x-button variant="primary" disabled="true">Disabled</x-button>
            </div>
        </div>

        <!-- Variasi Ukuran -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Ukuran Tombol</h3>
            <div class="flex flex-wrap items-center gap-3">
                <x-button variant="primary" size="sm">Kecil (sm)</x-button>
                <x-button variant="primary" size="md">Standar (md)</x-button>
                <x-button variant="primary" size="lg">Besar (lg)</x-button>
                <x-button variant="outline" href="#heading-buttons">Tombol Tautan (Link)</x-button>
            </div>
        </div>
    </section>

    <!-- 2. Pemberitahuan (Alerts) -->
    <section class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm" aria-labelledby="heading-alerts">
        <h2 id="heading-alerts" class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
            <span>📢</span> 2. Komponen Pemberitahuan (<code class="text-xs bg-slate-100 px-2 py-0.5 rounded text-blue-700">&lt;x-alert /&gt;</code>)
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 mb-6">
            Menggunakan <code class="text-xs bg-slate-100 px-1 py-0.5 rounded">role="alert"</code> untuk keramahan pembaca layar (screen reader) dan tombol tutup (dismiss) dengan Alpine.js.
        </p>

        <div class="space-y-4">
            <x-alert type="success" title="Permohonan Berhasil Dikirim" dismissible="true">
                Nomor registrasi permohonan Anda adalah <strong>REG-202610-0012</strong>. Konfirmasi dan respon resmi akan dikirimkan melalui alamat email yang Anda daftarkan.
            </x-alert>

            <x-alert type="danger" title="Kesalahan Validasi Data">
                Format NIK yang Anda masukkan tidak valid. Pastikan memasukkan 16 digit angka sesuai KTP.
            </x-alert>

            <x-alert type="warning" title="Peringatan Batas Waktu">
                Pengajuan keberatan hanya dapat dilakukan paling lambat 30 hari kerja setelah diterimanya tanggapan permohonan.
            </x-alert>

            <x-alert type="info" title="Informasi Jam Pelayanan">
                Meja pelayanan langsung PPID buka setiap hari Senin s.d. Jumat pukul 08.00 - 15.30 WIB.
            </x-alert>
        </div>
    </section>

    <!-- 3. Formulir Input (Form Controls) -->
    <section class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm" aria-labelledby="heading-forms">
        <h2 id="heading-forms" class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
            <span>📝</span> 3. Komponen Input Formulir (<code class="text-xs bg-slate-100 px-2 py-0.5 rounded text-blue-700">&lt;x-form-input /&gt;</code>)
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 mb-6">
            Dilengkapi label terhubung, teks bantuan, pesan kesalahan terkait, atribut ARIA, serta status fokus terlihat.
        </p>

        <form onsubmit="return false;" class="space-y-5 max-w-2xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-form-input name="contoh_nama" 
                              label="Nama Lengkap Pemohon" 
                              placeholder="Masukkan nama sesuai KTP" 
                              required="true"
                              helpText="Nama akan dicantumkan pada tanda bukti registrasi permohonan." />

                <x-form-input name="contoh_email" 
                              label="Alamat Email Aktif" 
                              type="email" 
                              placeholder="nama@email.com" 
                              required="true"
                              helpText="Respon dan jawaban informasi akan dikirimkan ke email ini." />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-form-input name="contoh_kategori" 
                              label="Kategori Pemohon Informasi" 
                              type="select"
                              required="true"
                              placeholder="-- Pilih Kategori Pemohon --"
                              :options="[
                                  'perorangan' => 'Perorangan / Pribadi',
                                  'badan_hukum' => 'Badan Hukum / Lembaga',
                                  'kelompok' => 'Kelompok Orang / Komunitas'
                              ]" />

                <x-form-input name="contoh_nik_error" 
                              label="Nomor Induk Kependudukan (NIK)" 
                              value="12345" 
                              required="true"
                              error="NIK harus terdiri dari 16 digit angka yang valid sesuai KTP." />
            </div>

            <x-form-input name="contoh_uraian" 
                          label="Rincian Informasi yang Dibutuhkan" 
                          type="textarea" 
                          rows="3"
                          placeholder="Tuliskan secara spesifik informasi publik apa yang Anda butuhkan..." 
                          required="true"
                          helpText="Jelaskan jenis informasi, kurun waktu/tahun dokumen, dan satuan kerja terkait." />

            <div>
                <x-button variant="primary" type="submit">
                    Simpan Formulir Contoh
                </x-button>
            </div>
        </form>
    </section>

    <!-- 4. Kartu Berita (News Cards) -->
    <section class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm" aria-labelledby="heading-news">
        <h2 id="heading-news" class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
            <span>📰</span> 4. Komponen Kartu Berita (<code class="text-xs bg-slate-100 px-2 py-0.5 rounded text-blue-700">&lt;x-news-card /&gt;</code>)
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 mb-6">
            Tata letak kisi (grid) responsif: 1 kolom di ponsel (360px), 2 kolom di tablet (768px), dan 3 kolom di desktop (1280px). Mendukung gambar WebP dan lazy-loading.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <x-news-card 
                title="Pemerintah Luncurkan Portal Keterbukaan Informasi Publik Terpadu"
                date="09 Oktober 2026"
                category="Keterbukaan Informasi"
                excerpt="Optimalisasi pelayanan informasi publik sesuai amanat UU No. 14 Tahun 2008 dengan penyediaan Daftar Informasi Publik secara transparan dan mudah diakses masyarakat."
                url="#" />

            <x-news-card 
                title="Bimtek Pengelolaan Dokumentasi & Pelayanan PPID bagi Perangkat Daerah"
                date="07 Oktober 2026"
                category="Kegiatan"
                excerpt="Kegiatan bimbingan teknis peningkatan kompetensi pengelola informasi publik dalam melayani permohonan informasi serta penyusunan klasifikasi dokumen berkala."
                url="#" />

            <x-news-card 
                title="Pengumuman Jadwal Pelayanan Meja PPID Selama Periode Triwulan IV"
                date="04 Oktober 2026"
                category="Pengumuman"
                excerpt="Informasi penyesuaian jam operasional tatap muka pelayanan permohonan informasi publik dan konsultasi dokumen luring di lobi kantor dinas."
                url="#" />
        </div>
    </section>

    <!-- 5. Tabel Dokumen Informasi Publik -->
    <section class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm" aria-labelledby="heading-table">
        <h2 id="heading-table" class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
            <span>📊</span> 5. Komponen Tabel Dokumen (<code class="text-xs bg-slate-100 px-2 py-0.5 rounded text-blue-700">&lt;x-document-table /&gt;</code>)
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 mb-6">
            Tabel dokumen responsif yang dapat digulir secara horizontal di layar kecil (mobile-friendly) tanpa merusak tata letak halaman. Tombol unduh memiliki label khusus pembaca layar.
        </p>

        @php
            $contohDokumen = [
                [
                    'judul' => 'Rencana Kerja Pemerintah Daerah (RKPD) Tahun Anggaran 2026',
                    'keterangan' => 'Dokumen perencanaan pembangunan daerah tahunan.',
                    'kategori' => 'Informasi Berkala',
                    'tahun' => '2026',
                    'format' => 'PDF',
                    'ukuran' => '3.4 MB',
                    'url' => '#',
                ],
                [
                    'judul' => 'Laporan Kinerja Instansi Pemerintah (LKjIP) Periode 2025',
                    'keterangan' => 'Pertanggungjawaban capaian kinerja tahun sebelumnya.',
                    'kategori' => 'Informasi Berkala',
                    'tahun' => '2025',
                    'format' => 'PDF',
                    'ukuran' => '2.1 MB',
                    'url' => '#',
                ],
                [
                    'judul' => 'Standar Operasional Prosedur (SOP) Layanan Permohonan Informasi',
                    'keterangan' => 'Pedoman tata cara pengajuan dan pelayanan informasi publik PPID.',
                    'kategori' => 'Setiap Saat',
                    'tahun' => '2026',
                    'format' => 'PDF',
                    'ukuran' => '850 KB',
                    'url' => '#',
                ],
            ];
        @endphp

        <x-document-table :documents="$contohDokumen" caption="Daftar Dokumen Informasi Publik Percontohan" />
    </section>

    <!-- 6. Paginasi Halaman -->
    <section class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm" aria-labelledby="heading-pagination">
        <h2 id="heading-pagination" class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
            <span>📑</span> 6. Komponen Paginasi (<code class="text-xs bg-slate-100 px-2 py-0.5 rounded text-blue-700">&lt;x-pagination /&gt;</code>)
        </h2>
        <p class="text-xs sm:text-sm text-slate-500 mb-4">
            Navigasi halaman aksesibel dengan penanda <code class="text-xs bg-slate-100 px-1 py-0.5 rounded">aria-current="page"</code> pada halaman yang sedang aktif.
        </p>

        <x-pagination :currentPage="2" :totalPages="5" baseUrl="/komponen" />
    </section>
</div>
@endsection
