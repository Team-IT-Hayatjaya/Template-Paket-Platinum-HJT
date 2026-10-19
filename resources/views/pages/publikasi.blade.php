@extends('layouts.app')

@section('title', 'Publikasi & Dokumen Resmi - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))

@section('content')
<div class="space-y-space-xl">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Publikasi & Dokumen Resmi'],
    ]" />

    <!-- PENGUMUMAN PENTING / SERTA MERTA NOTICE -->
    <section class="w-full">
        <div class="bg-tertiary-container/10 p-space-md rounded-xl shadow-2xs border border-error/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-space-md">
            <div class="flex items-start gap-space-sm">
                <span class="material-symbols-outlined text-error text-[28px] shrink-0" style="font-variation-settings: 'FILL' 1;">warning</span>
                <div>
                    <div class="flex items-center gap-space-xs flex-wrap">
                        <span class="font-label-sm text-label-sm bg-error text-on-error px-space-xs py-0.5 rounded uppercase tracking-wider font-bold">Informasi Serta Merta</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Update Status Pelayanan Publik: {{ date('d F Y') }}</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface mt-space-xs">
                        <strong>Kesiapsiagaan Informasi Publik & Layanan Kedaruratan:</strong> Seluruh saluran informasi kedaruratan, posko layanan, dan publikasi resmi beroperasi aktif. Hubungi hotline PPID untuk informasi resmi terverifikasi.
                    </p>
                </div>
            </div>
            <a class="shrink-0 bg-primary-container text-on-primary hover:bg-primary px-space-md py-space-xs rounded-lg font-label-md text-label-md transition-colors flex items-center gap-space-xs cursor-pointer" href="#klasifikasi-dokumen">
                <span>Daftar Informasi</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    </section>

    <!-- INTRO HEADER PPID & HERO STAT STRIP -->
    <section class="w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
            <div class="lg:col-span-8 bg-surface-container-lowest p-space-lg lg:p-space-xl rounded-xl shadow-2xs border border-surface-container relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-secondary-fixed/30 blur-2xl pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-space-xs mb-space-xs text-secondary font-label-md text-label-md uppercase tracking-wider font-bold">
                        <span class="material-symbols-outlined text-[18px]">verified</span>
                        <span>UU No. 14 Tahun 2008 Tentang Keterbukaan Informasi Publik</span>
                    </div>
                    <h1 class="font-headline-xl text-headline-xl text-primary tracking-tight mb-space-sm">
                        Publikasi & Dokumen Resmi
                    </h1>
                    <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mb-space-lg leading-relaxed">
                        Pejabat Pengelola Informasi dan Dokumentasi (PPID) {{ $siteSetting->institution_name ?? 'Pemerintah Instansi' }} menjamin hak warga negara atas akses informasi publik yang transparan, akuntabel, dan bebas hambatan.
                    </p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-space-sm pt-space-md bg-surface-container-low p-space-md rounded-lg border border-surface-container">
                    <div>
                        <div class="font-headline-md text-headline-md text-primary font-bold">100%</div>
                        <div class="font-body-sm text-body-sm text-on-surface-variant">LHKPN & Realisasi</div>
                    </div>
                    <div>
                        <div class="font-headline-md text-headline-md text-primary font-bold">&lt; 3 Hari</div>
                        <div class="font-body-sm text-body-sm text-on-surface-variant">Rata Respon PPID</div>
                    </div>
                    <div>
                        <div class="font-headline-md text-headline-md text-primary font-bold">Terbuka</div>
                        <div class="font-body-sm text-body-sm text-on-surface-variant">Akses Dokumen</div>
                    </div>
                    <div>
                        <div class="font-headline-md text-headline-md text-primary font-bold">A+ (Baik)</div>
                        <div class="font-body-sm text-body-sm text-on-surface-variant">Indeks Keterbukaan</div>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTION CARD: PERMOHONAN & KEBERATAN -->
            <div class="lg:col-span-4 bg-primary text-on-primary p-space-lg rounded-xl shadow-md flex flex-col justify-between relative overflow-hidden">
                <div class="absolute right-0 bottom-0 translate-x-8 translate-y-8 opacity-10">
                    <span class="material-symbols-outlined text-[180px]">folder_managed</span>
                </div>
                <div class="relative z-10">
                    <span class="font-label-sm text-label-sm bg-secondary-fixed text-primary px-space-xs py-0.5 rounded font-bold uppercase">Layanan Mandiri PPID</span>
                    <h2 class="font-headline-md text-headline-md text-on-primary mt-space-sm mb-space-xs">Ajukan Permohonan Informasi</h2>
                    <p class="font-body-sm text-body-sm text-on-primary-container leading-relaxed mb-space-md">
                        Warga berhak meminta salinan data publik secara langsung maupun daring. Batas waktu penyelesaian maksimal 10 hari kerja.
                    </p>
                </div>
                <div class="space-y-space-sm relative z-10">
                    <button class="w-full bg-secondary text-primary hover:bg-secondary-fixed py-space-sm px-space-md rounded font-label-md text-label-md font-bold transition-all shadow-xs flex items-center justify-center gap-space-xs cursor-pointer" onclick="document.getElementById('modal-permohonan').classList.remove('hidden')">
                        <span class="material-symbols-outlined text-[18px]">post_add</span>
                        <span>Formulir Permohonan Online</span>
                    </button>
                    <button class="w-full bg-primary-container text-on-primary hover:bg-surface-tint py-space-sm px-space-md rounded font-label-md text-label-md transition-colors flex items-center justify-center gap-space-xs cursor-pointer" onclick="document.getElementById('modal-keberatan').classList.remove('hidden')">
                        <span class="material-symbols-outlined text-[18px]">gavel</span>
                        <span>Pengajuan Keberatan Informasi</span>
                    </button>
                    <a class="block text-center text-on-primary-container hover:text-secondary-fixed font-label-sm text-label-sm underline transition-colors pt-space-xs" href="{{ route('home') }}#ppid">
                        Pelajari Prosedur Lengkap PPID
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- KLASIFIKASI INFORMASI PUBLIK SESUAI REGULASI -->
    <section class="w-full" id="klasifikasi-dokumen">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-space-md gap-space-xs">
            <div>
                <span class="font-label-sm text-label-sm text-secondary font-bold uppercase tracking-wider">Transparansi Penyelenggaraan</span>
                <h2 class="font-headline-xl text-headline-xl text-primary">Klasifikasi Dokumen Informasi Publik</h2>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant max-w-md">
                Seluruh dokumen dapat diakses secara cuma-cuma dalam format digital terbuka sesuai Perki No. 1 Tahun 2021.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
            <!-- Informasi Berkala -->
            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-2xs border border-surface-container hover:shadow-md transition-shadow">
                <div class="flex items-center gap-space-xs mb-space-sm">
                    <div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                    </div>
                    <div>
                        <h3 class="font-title-lg text-title-lg text-primary">Informasi Berkala</h3>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Diperbarui rutin triwulanan/tahunan</span>
                    </div>
                </div>
                <ul class="space-y-space-xs font-body-sm text-body-sm text-on-surface-variant">
                    <li class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[16px] text-secondary shrink-0 mt-0.5">check_circle</span>
                        <span>Laporan Akuntabilitas Kinerja (LAKIP/LKjIP)</span>
                    </li>
                    <li class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[16px] text-secondary shrink-0 mt-0.5">check_circle</span>
                        <span>Rencana Strategis (Renstra) & Rencana Kerja (Renja)</span>
                    </li>
                    <li class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[16px] text-secondary shrink-0 mt-0.5">check_circle</span>
                        <span>Ringkasan Realisasi Anggaran Pendapatan & Belanja</span>
                    </li>
                    <li class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[16px] text-secondary shrink-0 mt-0.5">check_circle</span>
                        <span>Daftar Informasi Publik (DIP) Tahunan</span>
                    </li>
                </ul>
            </div>

            <!-- Informasi Setiap Saat -->
            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-2xs border border-surface-container hover:shadow-md transition-shadow">
                <div class="flex items-center gap-space-xs mb-space-sm">
                    <div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-[20px]">sync</span>
                    </div>
                    <div>
                        <h3 class="font-title-lg text-title-lg text-primary">Informasi Setiap Saat</h3>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Tersedia langsung di meja layanan</span>
                    </div>
                </div>
                <ul class="space-y-space-xs font-body-sm text-body-sm text-on-surface-variant">
                    <li class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[16px] text-secondary shrink-0 mt-0.5">check_circle</span>
                        <span>Standar Operasional Prosedur (SOP) Layanan</span>
                    </li>
                    <li class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[16px] text-secondary shrink-0 mt-0.5">check_circle</span>
                        <span>Daftar Regulasi Daerah & Keputusan Instansi</span>
                    </li>
                    <li class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[16px] text-secondary shrink-0 mt-0.5">check_circle</span>
                        <span>Maklumat Pelayanan & Piagam Standar Pelayanan</span>
                    </li>
                    <li class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[16px] text-secondary shrink-0 mt-0.5">check_circle</span>
                        <span>Profil Pejabat Struktural & Tugas Pokok Fungsi</span>
                    </li>
                </ul>
            </div>

            <!-- Informasi Serta Merta -->
            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-2xs border border-surface-container hover:shadow-md transition-shadow">
                <div class="flex items-center gap-space-xs mb-space-sm">
                    <div class="w-9 h-9 rounded-lg bg-tertiary-container/10 flex items-center justify-center text-error">
                        <span class="material-symbols-outlined text-[20px]">fmd_bad</span>
                    </div>
                    <div>
                        <h3 class="font-title-lg text-title-lg text-primary">Informasi Serta Merta</h3>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Pemberitahuan darurat keselamatan warga</span>
                    </div>
                </div>
                <ul class="space-y-space-xs font-body-sm text-body-sm text-on-surface-variant">
                    <li class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[16px] text-error shrink-0 mt-0.5">campaign</span>
                        <span>Panduan Tanggap Darurat & Evakuasi Bencana</span>
                    </li>
                    <li class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[16px] text-error shrink-0 mt-0.5">campaign</span>
                        <span>Peringatan Dini Cuaca Ekstrem & Mitigasi Banjir</span>
                    </li>
                    <li class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[16px] text-error shrink-0 mt-0.5">campaign</span>
                        <span>Saluran Tanggap Darurat Puskesmas 24 Jam</span>
                    </li>
                    <li class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[16px] text-error shrink-0 mt-0.5">campaign</span>
                        <span>Protokol Pencegahan Wabah & Kesehatan Publik</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- REPOSITORI DOKUMEN UNDUHAN RESMI -->
    <section class="w-full bg-surface-container-lowest rounded-xl shadow-2xs border border-surface-container p-space-md lg:p-space-lg space-y-space-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
            <div>
                <h3 class="font-title-lg text-title-lg text-primary">Repositori Dokumen Publik Resmi</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Daftar berkas resmi yang disahkan oleh {{ $siteSetting->institution_name ?? 'Sekretariat Instansi' }}</p>
            </div>
            
            <!-- Filter Kategori Dokumen -->
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <a href="{{ route('publikasi') }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ empty($kategori) ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }} transition-colors">
                    Semua Dokumen
                </a>
                @foreach($kategoriList as $kat)
                    <a href="{{ route('publikasi', ['kategori' => $kat]) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ (strtolower($kategori ?? '') === strtolower($kat)) ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }} transition-colors">
                        {{ $kat }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Tabel Dokumen Menggunakan Komponen Reusable x-document-table -->
        <div class="w-full overflow-hidden">
            <x-document-table :documents="$daftarDokumen" caption="Daftar Dokumen Publik dan Publikasi Resmi" />
        </div>

        <!-- Paginasi -->
        <x-pagination :currentPage="1" :totalPages="1" baseUrl="{{ route('publikasi') }}" />
    </section>

    <!-- DASHBOARD DATA & STATISTIK WILAYAH -->
    <section class="w-full space-y-space-md">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-xs">
            <div>
                <span class="font-label-sm text-label-sm text-secondary font-bold uppercase tracking-wider">Transparansi Data Terbuka</span>
                <h2 class="font-headline-xl text-headline-xl text-primary">Dashboard Statistik & Capaian Kinerja</h2>
            </div>
            <div class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-space-xs">
                <span class="material-symbols-outlined text-[16px] text-secondary">update</span>
                <span>Sumber: Satu Data Pemerintahan Terintegrasi</span>
            </div>
        </div>

        <!-- BENTO GRID DATA SECTION -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg">
            <!-- CARD 1: DEMOGRAFI & STRUKTUR -->
            <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-2xs border border-surface-container flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-space-xs">
                        <span class="font-label-sm text-label-sm text-secondary font-bold uppercase">Demografi Pelayanan</span>
                        <span class="material-symbols-outlined text-primary text-[20px]">groups</span>
                    </div>
                    <div class="font-headline-lg text-headline-lg text-primary font-bold">54.820 Warga</div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mb-space-md">Terlayani di seluruh unit administrasi</p>
                    
                    <div class="space-y-space-xs mb-space-md">
                        <div class="flex justify-between font-label-sm text-label-sm">
                            <span class="text-primary font-semibold">Laki-laki (51.8%)</span>
                            <span class="text-on-surface-variant font-semibold">Perempuan (48.2%)</span>
                        </div>
                        <div class="w-full h-3 bg-surface-container rounded-full overflow-hidden flex">
                            <div class="h-full bg-primary" style="width: 51.8%;"></div>
                            <div class="h-full bg-secondary-fixed-dim" style="width: 48.2%;"></div>
                        </div>
                    </div>

                    <div class="space-y-space-xs">
                        <div class="text-on-surface-variant font-label-sm text-label-sm mb-space-xs">Kelompok Usia:</div>
                        <div>
                            <div class="flex justify-between font-body-sm text-body-sm">
                                <span>Usia Produktif (15 - 64 Thn)</span>
                                <span class="font-bold text-primary">68.4%</span>
                            </div>
                            <div class="w-full bg-surface-container h-2 rounded overflow-hidden">
                                <div class="bg-primary h-2 rounded" style="width: 68.4%;"></div>
                            </div>
                        </div>
                        <div class="pt-1">
                            <div class="flex justify-between font-body-sm text-body-sm">
                                <span>Usia Anak (0 - 14 Thn)</span>
                                <span class="font-bold text-primary">24.2%</span>
                            </div>
                            <div class="w-full bg-surface-container h-2 rounded overflow-hidden">
                                <div class="bg-surface-tint h-2 rounded" style="width: 24.2%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pt-space-md mt-space-md bg-surface-container-low p-space-sm rounded border border-surface-container">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Indeks Aksesibilitas: <strong>Tinggi (Terjangkau)</strong></span>
                </div>
            </div>

            <!-- CARD 2: SARANA & PELAYANAN -->
            <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-2xs border border-surface-container flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-space-xs">
                        <span class="font-label-sm text-label-sm text-secondary font-bold uppercase">Layanan Dasar</span>
                        <span class="material-symbols-outlined text-primary text-[20px]">local_hospital</span>
                    </div>
                    <div class="font-headline-lg text-headline-lg text-primary font-bold">Fasilitas Publik</div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mb-space-md">Pemerataan sarana kesehatan dan pendidikan terintegrasi</p>
                    
                    <div class="space-y-space-sm mb-space-md">
                        <div class="bg-surface-container-low p-space-sm rounded flex items-center justify-between border border-surface-container">
                            <div>
                                <div class="font-label-sm text-label-sm text-on-surface-variant">Satuan Pendidikan</div>
                                <div class="font-bold text-primary text-title-lg">44 Unit</div>
                            </div>
                            <span class="material-symbols-outlined text-secondary text-[26px]">school</span>
                        </div>
                        <div class="bg-surface-container-low p-space-sm rounded flex items-center justify-between border border-surface-container">
                            <div>
                                <div class="font-label-sm text-label-sm text-on-surface-variant">Fasilitas Kesehatan & Posko</div>
                                <div class="font-bold text-primary text-title-lg">27 Unit</div>
                            </div>
                            <span class="material-symbols-outlined text-secondary text-[26px]">medical_services</span>
                        </div>
                    </div>
                </div>
                <div class="pt-space-md mt-space-md bg-surface-container-low p-space-sm rounded border border-surface-container">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Status Pelayanan: <strong>Aktif 24 Jam Darurat</strong></span>
                </div>
            </div>

            <!-- CARD 3: REALISASI KEUANGAN -->
            <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-2xs border border-surface-container flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-space-xs">
                        <span class="font-label-sm text-label-sm text-secondary font-bold uppercase">Transparansi Anggaran</span>
                        <span class="material-symbols-outlined text-primary text-[20px]">account_balance_wallet</span>
                    </div>
                    <div class="font-headline-lg text-headline-lg text-primary font-bold">81.4 %</div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mb-space-md">Realisasi Anggaran T.A. Berjalan</p>
                    
                    <div class="space-y-space-xs font-body-sm text-body-sm">
                        <div class="flex justify-between">
                            <span>Pembangunan & Fisik</span>
                            <span class="font-bold text-primary">84%</span>
                        </div>
                        <div class="w-full bg-surface-container h-2 rounded overflow-hidden">
                            <div class="bg-secondary-fixed-dim h-2 rounded" style="width: 84%;"></div>
                        </div>

                        <div class="flex justify-between pt-2">
                            <span>Pemberdayaan Masyarakat</span>
                            <span class="font-bold text-primary">79%</span>
                        </div>
                        <div class="w-full bg-surface-container h-2 rounded overflow-hidden">
                            <div class="bg-secondary-fixed-dim h-2 rounded" style="width: 79%;"></div>
                        </div>

                        <div class="flex justify-between pt-2">
                            <span>Pelayanan Publik</span>
                            <span class="font-bold text-primary">88%</span>
                        </div>
                        <div class="w-full bg-surface-container h-2 rounded overflow-hidden">
                            <div class="bg-primary h-2 rounded" style="width: 88%;"></div>
                        </div>
                    </div>
                </div>
                <div class="pt-space-md mt-space-md bg-surface-container-low p-space-sm rounded border border-surface-container">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Akuntabilitas: <strong>Opini Wajar Tanpa Pengecualian</strong></span>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL FORMULIR PERMOHONAN INFORMASI ONLINE -->
    <div class="fixed inset-0 z-50 bg-primary/60 backdrop-blur-xs hidden flex items-center justify-center p-margin-mobile" id="modal-permohonan">
        <div class="bg-surface-container-lowest max-w-2xl w-full rounded-xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col border border-surface-container">
            <div class="bg-primary text-on-primary p-space-md flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-secondary-fixed">post_add</span>
                    <span class="font-title-lg text-title-lg font-bold">Formulir Permohonan Informasi Publik (Online)</span>
                </div>
                <button class="text-on-primary hover:text-secondary-fixed cursor-pointer" onclick="document.getElementById('modal-permohonan').classList.add('hidden')">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>
            <div class="p-space-lg overflow-y-auto space-y-space-md font-body-sm text-body-sm">
                <p class="text-on-surface-variant">Berdasarkan Ketentuan UU No. 14 Tahun 2008, PPID berhak meminta identitas pemohon yang sah guna pertanggungjawaban hukum.</p>
                <div class="space-y-space-sm">
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-1">Rincian Informasi yang Dibutuhkan *</label>
                        <textarea class="w-full bg-surface-container-low p-space-xs rounded outline-none focus:ring-1 focus:ring-secondary border border-surface-container" placeholder="Tuliskan judul laporan, periode tahun, dan format dokumen..." rows="3"></textarea>
                    </div>
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-1">Tujuan Penggunaan Informasi *</label>
                        <input class="w-full bg-surface-container-low p-space-xs rounded outline-none focus:ring-1 focus:ring-secondary border border-surface-container" placeholder="Contoh: Skripsi / Kajian Warga / Pengetahuan Umum" type="text"/>
                    </div>
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-1">Format Penyerahan Dokumen</label>
                        <select class="w-full bg-surface-container-low p-space-xs rounded outline-none focus:ring-1 focus:ring-secondary border border-surface-container">
                            <option>Salinan Elektronik (Email / PDF via Link)</option>
                            <option>Salinan Cetak Fisik (Mengambil di Meja Layanan)</option>
                        </select>
                    </div>
                </div>
                <div class="p-space-sm bg-surface-container-low rounded text-on-surface-variant font-label-sm text-label-sm border border-surface-container">
                    Biaya perolehan salinan elektronik adalah <strong>Rp 0 (GRATIS)</strong>. Untuk cetak fisik, biaya penggantian fotokopi ditanggung pemohon sesuai standar retribusi daerah.
                </div>
                <div class="flex justify-end gap-space-xs pt-space-xs">
                    <button class="px-space-md py-space-xs rounded font-label-md text-label-md text-on-surface-variant hover:bg-surface-container cursor-pointer" onclick="document.getElementById('modal-permohonan').classList.add('hidden')">Batal</button>
                    <button class="bg-primary text-on-primary hover:bg-primary-container px-space-md py-space-xs rounded font-label-md text-label-md font-bold transition-colors cursor-pointer" onclick="alert('Permohonan berhasil diregistrasi ke sistem PPID.'); document.getElementById('modal-permohonan').classList.add('hidden')">Kirim Permohonan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL KEBERATAN INFORMASI -->
    <div class="fixed inset-0 z-50 bg-primary/60 backdrop-blur-xs hidden flex items-center justify-center p-margin-mobile" id="modal-keberatan">
        <div class="bg-surface-container-lowest max-w-2xl w-full rounded-xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col border border-surface-container">
            <div class="bg-tertiary-container text-on-tertiary p-space-md flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-secondary-fixed">gavel</span>
                    <span class="font-title-lg text-title-lg font-bold">Formulir Pernyataan Keberatan Informasi</span>
                </div>
                <button class="text-on-tertiary hover:text-secondary-fixed cursor-pointer" onclick="document.getElementById('modal-keberatan').classList.add('hidden')">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>
            <div class="p-space-lg overflow-y-auto space-y-space-md font-body-sm text-body-sm">
                <p class="text-on-surface-variant">Diajukan apabila pemohon merasa ditolak tanpa alasan sah, informasi tidak diberikan dalam batas 10 hari kerja, atau dikenakan biaya tidak wajar.</p>
                <div class="space-y-space-sm">
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-1">Nomor Registrasi Permohonan Awal *</label>
                        <input class="w-full bg-surface-container-low p-space-xs rounded outline-none focus:ring-1 focus:ring-secondary border border-surface-container" placeholder="Contoh: PPID-REG-2026-XXXX" type="text"/>
                    </div>
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-1">Alasan Keberatan *</label>
                        <select class="w-full bg-surface-container-low p-space-xs rounded outline-none focus:ring-1 focus:ring-secondary border border-surface-container">
                            <option>Permohonan Informasi ditolak tanpa penjelasan tertulis</option>
                            <option>Informasi berkala tidak disediakan sesuai jadwal</option>
                            <option>Permohonan tidak ditanggapi melebihi batas waktu (10 hari kerja)</option>
                            <option>Pengenaan biaya yang tidak sesuai ketentuan</option>
                            <option>Informasi yang diberikan tidak sesuai dengan yang dimohonkan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-1">Uraian Keterangan Tambahan Pemohon</label>
                        <textarea class="w-full bg-surface-container-low p-space-xs rounded outline-none focus:ring-1 focus:ring-secondary border border-surface-container" placeholder="Tuliskan kronologi dan tanggapan dari petugas sebelumnya..." rows="3"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-space-xs pt-space-xs">
                    <button class="px-space-md py-space-xs rounded font-label-md text-label-md text-on-surface-variant hover:bg-surface-container cursor-pointer" onclick="document.getElementById('modal-keberatan').classList.add('hidden')">Batal</button>
                    <button class="bg-primary text-on-primary hover:bg-primary-container px-space-md py-space-xs rounded font-label-md text-label-md font-bold transition-colors cursor-pointer" onclick="alert('Pengajuan keberatan diterima dan diteruskan ke Atasan PPID.'); document.getElementById('modal-keberatan').classList.add('hidden')">Ajukan Keberatan</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
