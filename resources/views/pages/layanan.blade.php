@extends('layouts.app')

@section('title', 'Layanan Publik & Maklumat Prosedur - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))

@section('content')
<div class="space-y-space-xl">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Standar Pelayanan Publik'],
    ]" />

    <!-- TOP ANNOUNCEMENT TICKER / SERVICE INTEGRITY PLEDGE -->
    <section class="w-full bg-primary text-on-primary py-space-xs px-margin-mobile lg:px-margin rounded-xl shadow-xs">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-space-sm text-center md:text-left">
            <div class="flex items-center gap-space-xs font-label-sm text-label-sm">
                <span class="inline-flex items-center justify-center bg-secondary-fixed text-primary px-space-xs py-[2px] rounded font-label-sm text-[11px] uppercase tracking-wider font-bold">Resmi</span>
                <span class="text-surface-container-high">
                    Semua pengurusan dokumen & rekomendasi di loket PATEN {{ $siteSetting->institution_name ?? 'Kecamatan' }} <strong>100% BEBAS BIAYA (Rp 0,-)</strong>
                </span>
            </div>
            <div class="flex items-center gap-space-md text-surface-container-high font-label-sm text-label-sm">
                <span class="flex items-center gap-[4px]">
                    <span class="material-symbols-outlined text-[15px] text-secondary-fixed">verified_user</span> ISO 9001:2015 Terakreditasi
                </span>
                <span class="hidden sm:inline">•</span>
                <span class="flex items-center gap-[4px]">
                    <span class="material-symbols-outlined text-[15px] text-secondary-fixed">electric_bolt</span> Tanda Tangan Elektronik (TTE) BSrE
                </span>
            </div>
        </div>
    </section>

    <!-- PAGE HERO INTRO (CIVIC EDITORIAL STYLE) -->
    <section class="relative w-full bg-surface-container-low py-space-xl px-margin-mobile lg:px-margin rounded-2xl overflow-hidden border border-surface-container">
        <div class="absolute -right-24 -top-24 w-96 h-96 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 bottom-0 w-80 h-80 bg-secondary-fixed/30 rounded-full blur-2xl pointer-events-none"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
                <div class="lg:col-span-8 space-y-space-md">
                    <div class="inline-flex items-center gap-space-xs bg-surface-container-lowest px-space-sm py-space-xs rounded-full shadow-2xs border border-surface-container">
                        <span class="material-symbols-outlined text-secondary text-[18px]">account_balance</span>
                        <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant font-bold">Standar Pelayanan Publik Terpadu</span>
                    </div>
                    <h1 class="font-headline-xl text-headline-xl text-primary tracking-tight">
                        Pusat Pelayanan Administrasi Terpadu (PATEN)
                    </h1>
                    <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl leading-relaxed">
                        Layanan Publik & Maklumat Prosedur satu atap {{ $siteSetting->institution_name ?? 'Pemerintah Instansi' }}. Memberikan kemudahan, transparansi, serta kepastian hukum untuk seluruh warga dengan tata kelola administrasi terverifikasi.
                    </p>

                    <!-- QUICK HIGHLIGHT TILES -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-space-sm pt-space-xs">
                        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-2xs border border-surface-container">
                            <div class="font-label-sm text-label-sm text-on-surface-variant">Rata-rata Waktu Proses</div>
                            <div class="font-headline-md text-headline-md text-primary font-bold">1 - 2 Hari</div>
                            <div class="font-body-sm text-body-sm text-secondary">Tergantung jenis berkas</div>
                        </div>
                        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-2xs border border-surface-container">
                            <div class="font-label-sm text-label-sm text-on-surface-variant">Biaya Retribusi</div>
                            <div class="font-headline-md text-headline-md text-secondary font-bold">Rp 0,-</div>
                            <div class="font-body-sm text-body-sm text-on-surface-variant">Gratis tanpa pungli</div>
                        </div>
                        <div class="col-span-2 sm:col-span-1 bg-surface-container-lowest p-space-md rounded-xl shadow-2xs border border-surface-container">
                            <div class="font-label-sm text-label-sm text-on-surface-variant">Indeks Kepuasan (IKM)</div>
                            <div class="font-headline-md text-headline-md text-primary font-bold">96.8 %</div>
                            <div class="font-body-sm text-body-sm text-secondary">Kategori Sangat Baik</div>
                        </div>
                    </div>
                </div>

                <!-- TRACKING TICKET COMPONENT -->
                <div class="lg:col-span-4">
                    <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-md border border-surface-container space-y-space-md">
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-secondary text-[24px]">troubleshoot</span>
                            <div>
                                <h3 class="font-title-lg text-title-lg text-primary">Lacak Berkas Permohonan</h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Cek progres tanda tangan & verifikasi loket</p>
                            </div>
                        </div>

                        @packageHas('ppid_tracking')
                            <form class="space-y-space-sm" id="trackingForm" onsubmit="event.preventDefault(); window.lacakPermohonan();">
                                <div class="space-y-space-xs">
                                    <label class="font-label-sm text-label-sm text-on-surface font-semibold" for="resiNumber">Nomor Resi / Tiket Registrasi</label>
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-3 text-[18px] text-on-surface-variant">receipt_long</span>
                                        <input class="w-full bg-surface-container-low pl-10 pr-4 py-2.5 rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-secondary/50 transition border border-surface-container" id="resiNumber" placeholder="Contoh: PTN-2026-0842" required type="text"/>
                                    </div>
                                </div>
                                <div class="space-y-space-xs">
                                    <label class="font-label-sm text-label-sm text-on-surface font-semibold" for="nikNumber">4 Digit Terakhir NIK Pemohon</label>
                                    <input class="w-full bg-surface-container-low px-4 py-2.5 rounded-lg text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-secondary/50 transition tracking-widest border border-surface-container" id="nikNumber" maxlength="4" placeholder="••••" required type="text"/>
                                </div>
                                <button class="w-full bg-primary hover:bg-primary-container text-on-primary py-3 rounded-lg font-label-md text-label-md flex items-center justify-center gap-space-xs shadow-xs transition" type="submit">
                                    <span class="material-symbols-outlined text-[18px]">search</span>
                                    <span>Cek Status Berkas</span>
                                </button>
                            </form>
                            <div class="hidden p-space-sm bg-surface-container-low rounded-lg space-y-space-xs text-body-sm font-body-sm border border-surface-container" id="trackingResult">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-primary">No: PTN-2026-0842</span>
                                    <span class="bg-secondary-fixed text-primary px-space-xs rounded text-[11px] font-bold uppercase">Proses TTE Camat</span>
                                </div>
                                <p class="text-on-surface-variant">Pengesahan Keterangan Waris • Terverifikasi Seksi PMD • Estimasi Selesai: Hari ini, 14.30 WITA</p>
                            </div>
                        @else
                            <div class="p-space-md bg-surface-container-low rounded-xl border border-surface-container text-body-sm space-y-space-xs">
                                <div class="flex items-center gap-1.5 text-primary font-bold">
                                    <span class="material-symbols-outlined text-[18px] text-secondary">info</span>
                                    <span>Status Penelusuran Berkas</span>
                                </div>
                                <p class="text-on-surface-variant text-xs leading-relaxed">
                                    Pada sistem standar pelayanan, konfirmasi status berkas diproses langsung oleh petugas. Silakan tanyakan nomor agenda ke meja pelayanan atau hubungi Helpdesk WhatsApp resmi.
                                </p>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSetting->phone ?? '6281255557890') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs font-bold text-secondary hover:underline pt-1">
                                    <span class="material-symbols-outlined text-[15px]">chat</span> Tanya Petugas Meja Layanan &rarr;
                                </a>
                            </div>
                        @endpackageHas

                        <p class="font-label-sm text-[12px] text-on-surface-variant text-center">
                            Belum punya nomor registrasi? Silakan ajukan permohonan langsung di Loket Pelayanan {{ $siteSetting->institution_name ?? 'Kantor Instansi' }}.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- WORKFLOW STEPS / 4-LANGKAH ALUR PATEN -->
    <section class="w-full py-space-md">
        <div class="max-w-7xl mx-auto space-y-space-lg">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-sm">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary font-bold">Standard Operating Procedure</span>
                    <h2 class="font-headline-lg text-headline-lg text-primary">Alur 4 Langkah Pengurusan Dokumen</h2>
                </div>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-md">
                    Sistem administrasi terintegrasi memastikan berkas Anda diproses tanpa perantara dan terpantau digital.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-space-md">
                <!-- Step 1 -->
                <div class="relative bg-surface-container-lowest p-space-lg rounded-xl shadow-2xs border border-surface-container flex flex-col justify-between">
                    <div class="space-y-space-sm">
                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center font-headline-md text-headline-md font-bold">1</span>
                            <span class="material-symbols-outlined text-secondary text-[24px]">folder_shared</span>
                        </div>
                        <h3 class="font-title-lg text-title-lg text-primary">Penyerahan Berkas</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                            Warga membawa kelengkapan pengantar RT/Desa ke Loket Front Office PATEN atau mengajukan secara daring.
                        </p>
                    </div>
                    <div class="pt-space-md">
                        <span class="font-label-sm text-label-sm text-secondary bg-surface-container-low px-space-xs py-1 rounded inline-block">Estimasi: 5-10 Menit</span>
                    </div>
                </div>
                <!-- Step 2 -->
                <div class="relative bg-surface-container-lowest p-space-lg rounded-xl shadow-2xs border border-surface-container flex flex-col justify-between">
                    <div class="space-y-space-sm">
                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center font-headline-md text-headline-md font-bold">2</span>
                            <span class="material-symbols-outlined text-secondary text-[24px]">rule</span>
                        </div>
                        <h3 class="font-title-lg text-title-lg text-primary">Verifikasi Kasi Terkait</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                            Pemeriksaan keabsahan dokumen fisik dan pencocokan data kependudukan ke database dinas terkait.
                        </p>
                    </div>
                    <div class="pt-space-md">
                        <span class="font-label-sm text-label-sm text-secondary bg-surface-container-low px-space-xs py-1 rounded inline-block">Estimasi: 1-3 Jam</span>
                    </div>
                </div>
                <!-- Step 3 -->
                <div class="relative bg-surface-container-lowest p-space-lg rounded-xl shadow-2xs border border-surface-container flex flex-col justify-between">
                    <div class="space-y-space-sm">
                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center font-headline-md text-headline-md font-bold">3</span>
                            <span class="material-symbols-outlined text-secondary text-[24px]">draw</span>
                        </div>
                        <h3 class="font-title-lg text-title-lg text-primary">Penandatanganan (TTE)</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                            Pemberian tanda tangan digital tersertifikasi oleh pimpinan secara real-time via aplikasi persuratan resmi.
                        </p>
                    </div>
                    <div class="pt-space-md">
                        <span class="font-label-sm text-label-sm text-secondary bg-surface-container-low px-space-xs py-1 rounded inline-block">Sistem Digital BSrE</span>
                    </div>
                </div>
                <!-- Step 4 -->
                <div class="relative bg-surface-container-lowest p-space-lg rounded-xl shadow-2xs border border-surface-container flex flex-col justify-between">
                    <div class="space-y-space-sm">
                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center font-headline-md text-headline-md font-bold">4</span>
                            <span class="material-symbols-outlined text-secondary text-[24px]">handshake</span>
                        </div>
                        <h3 class="font-title-lg text-title-lg text-primary">Pengambilan & SKM</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                            Pengambilan dokumen resmi berbarcode ke Loket Pengeluaran diiringi pengisian Indeks Kepuasan Masyarakat.
                        </p>
                    </div>
                    <div class="pt-space-md">
                        <span class="font-label-sm text-label-sm text-secondary bg-surface-container-low px-space-xs py-1 rounded inline-block">Survey Mandiri</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KATALOG LAYANAN DENGAN FILTER KATEGORI -->
    <section class="w-full py-space-xl bg-surface-container-low px-margin-mobile lg:px-margin rounded-2xl border border-surface-container">
        <div class="max-w-7xl mx-auto space-y-space-lg">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-space-md">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary font-bold">Katalog Resmi</span>
                    <h2 class="font-headline-lg text-headline-lg text-primary">Persyaratan & Prosedur Layanan PATEN</h2>
                </div>
                <!-- Live Search Box -->
                <div class="w-full md:w-72 relative">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-on-surface-variant">search</span>
                    <input class="w-full bg-surface-container-lowest pl-10 pr-3 py-2 rounded-lg text-body-sm font-body-sm focus:outline-none focus:ring-2 focus:ring-secondary/40 shadow-2xs border border-surface-container" id="serviceSearch" oninput="window.filterBySearch()" placeholder="Cari layanan (KTP, PPID, SKTM)..." type="text"/>
                </div>
            </div>

            <!-- FILTER TABS -->
            <div class="flex items-center gap-space-xs overflow-x-auto pb-2 border-b-0" id="filterTabContainer">
                <button class="cat-btn active-tab bg-primary text-on-primary px-space-md py-space-xs rounded-lg font-label-md text-label-md whitespace-nowrap transition" data-cat="all" onclick="window.filterCategory('all')">
                    Semua Layanan
                </button>
                <button class="cat-btn bg-surface-container-lowest hover:bg-surface-container-high text-on-surface-variant px-space-md py-space-xs rounded-lg font-label-md text-label-md whitespace-nowrap transition border border-surface-container" data-cat="ppid" onclick="window.filterCategory('ppid')">
                    PPID & Informasi Publik
                </button>
                <button class="cat-btn bg-surface-container-lowest hover:bg-surface-container-high text-on-surface-variant px-space-md py-space-xs rounded-lg font-label-md text-label-md whitespace-nowrap transition border border-surface-container" data-cat="kependudukan" onclick="window.filterCategory('kependudukan')">
                    Kependudukan
                </button>
                <button class="cat-btn bg-surface-container-lowest hover:bg-surface-container-high text-on-surface-variant px-space-md py-space-xs rounded-lg font-label-md text-label-md whitespace-nowrap transition border border-surface-container" data-cat="usaha" onclick="window.filterCategory('usaha')">
                    Perizinan & Usaha
                </button>
                <button class="cat-btn bg-surface-container-lowest hover:bg-surface-container-high text-on-surface-variant px-space-md py-space-xs rounded-lg font-label-md text-label-md whitespace-nowrap transition border border-surface-container" data-cat="sosial" onclick="window.filterCategory('sosial')">
                    Kesejahteraan Sosial
                </button>
            </div>

            <!-- SERVICE CARDS GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-md" id="cardsGrid">
                <!-- DYNAMIC CARDS FROM BACKEND ($layananList) -->
                @foreach($layananList as $item)
                    @php
                        $isModel = $item instanceof \Illuminate\Database\Eloquent\Model;
                        $itemId = $isModel ? $item->id : ($item['id'] ?? '');
                        $nama = $isModel ? $item->nama : ($item['nama'] ?? '');
                        $slug = $isModel ? $item->slug : ($item['id'] ?? '');
                        $deskripsi = $isModel ? ($item->ringkasan ?: Str::limit(strip_tags($item->persyaratan), 120)) : ($item['deskripsi'] ?? '');
                        $biaya = $isModel ? ($item->biaya ?: 'GRATIS') : ($item['biaya'] ?? 'GRATIS');
                        $waktu = $isModel ? ($item->waktu_penyelesaian ?: '1-2 Hari Kerja') : ($item['waktu'] ?? '1-2 Hari Kerja');
                        $unit = $isModel ? ($item->penanggung_jawab ?: 'PATEN') : 'Meja PPID';
                        $detailUrl = $isModel ? route('layanan.detail', $item->slug) : null;
                    @endphp
                    <article id="{{ $itemId }}" class="service-card bg-surface-container-lowest rounded-xl p-space-lg shadow-2xs border border-surface-container flex flex-col justify-between space-y-space-md hover:shadow-md transition" data-category="ppid all" data-keywords="{{ strtolower($nama . ' ' . $deskripsi) }}">
                        <div class="space-y-space-sm">
                            <div class="flex items-center justify-between">
                                <span class="bg-surface-container px-space-xs py-1 rounded text-primary font-label-sm text-label-sm font-semibold">{{ $unit }}</span>
                                <span class="text-secondary font-headline-md text-headline-md font-bold">{{ $biaya }}</span>
                            </div>
                            <h3 class="font-headline-md text-headline-md text-primary">
                                @if($detailUrl)
                                    <a href="{{ $detailUrl }}" class="hover:text-secondary transition-colors">{{ $nama }}</a>
                                @else
                                    {{ $nama }}
                                @endif
                            </h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                                {{ $deskripsi }}
                            </p>
                        </div>
                        <div class="space-y-space-sm pt-space-xs">
                            <div class="flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant">
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-secondary">timer</span> {{ $waktu }}</span>
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-secondary">domain</span> {{ $unit }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($detailUrl)
                                    <a href="{{ $detailUrl }}" class="flex-1 bg-primary hover:bg-primary-container text-on-primary py-2 rounded-lg font-label-md text-label-md flex items-center justify-center gap-1 transition">
                                        <span>Lihat Prosedur Lengkap</span>
                                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                    </a>
                                @else
                                    <button type="button" class="flex-1 bg-surface-container hover:bg-surface-container-high text-primary py-2 rounded-lg font-label-md text-label-md flex items-center justify-center gap-1 transition" onclick="window.showDetailModal('{{ addslashes($nama) }}')">
                                        <span>Lihat Alur</span>
                                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach

                <!-- ADDITIONAL STITCH CIVIC SERVICE CARDS -->
                <!-- CARD: KTP & KIA -->
                <div class="service-card bg-surface-container-lowest rounded-xl p-space-lg shadow-2xs border border-surface-container flex flex-col justify-between space-y-space-md hover:shadow-md transition" data-category="kependudukan all" data-keywords="ktp elektronik ktp-el kia kependudukan kartu identitas anak disdukcapil">
                    <div class="space-y-space-sm">
                        <div class="flex items-center justify-between">
                            <span class="bg-surface-container px-space-xs py-1 rounded text-primary font-label-sm text-label-sm font-semibold">Kependudukan</span>
                            <span class="text-secondary font-headline-md text-headline-md font-bold">GRATIS</span>
                        </div>
                        <h3 class="font-headline-md text-headline-md text-primary">Perekaman & Pencetakan KTP-el / KIA</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Layanan rekam data biometrik iris/sidik jari baru, penggantian KTP rusak/hilang, serta pembuatan Kartu Identitas Anak.
                        </p>
                        <div class="bg-surface-container-low p-space-sm rounded-lg space-y-1 border border-surface-container">
                            <div class="flex items-center gap-space-xs font-label-sm text-label-sm text-primary font-semibold">
                                <span class="material-symbols-outlined text-[16px] text-secondary">checklist</span> Syarat Utama:
                            </div>
                            <ul class="text-body-sm font-body-sm text-on-surface-variant list-disc list-inside space-y-0.5">
                                <li>Fotokopi Kartu Keluarga (KK) terbaru</li>
                                <li>KTP lama (untuk perpanjangan/rusak)</li>
                                <li>Surat Kehilangan Polsek (bila hilang)</li>
                            </ul>
                        </div>
                    </div>
                    <div class="space-y-space-sm pt-space-xs">
                        <div class="flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-secondary">timer</span> 1 Hari Kerja</span>
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-secondary">domain</span> Loket 1 PATEN</span>
                        </div>
                        <button class="w-full bg-surface-container hover:bg-surface-container-high text-primary py-2 rounded-lg font-label-md text-label-md flex items-center justify-center gap-1 transition" onclick="window.showDetailModal('KTP-el & KIA')">
                            <span>Lihat Alur Lengkap</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- CARD: IUMK & NIB -->
                <div class="service-card bg-surface-container-lowest rounded-xl p-space-lg shadow-2xs border border-surface-container flex flex-col justify-between space-y-space-md hover:shadow-md transition" data-category="usaha all" data-keywords="iumk nib izin usaha mikro kecil perizinan umkm oss oss-rba">
                    <div class="space-y-space-sm">
                        <div class="flex items-center justify-between">
                            <span class="bg-surface-container px-space-xs py-1 rounded text-primary font-label-sm text-label-sm font-semibold">Perizinan & Usaha</span>
                            <span class="text-secondary font-headline-md text-headline-md font-bold">GRATIS</span>
                        </div>
                        <h3 class="font-headline-md text-headline-md text-primary">Rekomendasi Izin Usaha Mikro Kecil (IUMK) & NIB</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Pendampingan registrasi OSS RBA untuk penerbitan Nomor Induk Berusaha (NIB) serta verifikasi kesesuaian lokasi kegiatan usaha warga.
                        </p>
                        <div class="bg-surface-container-low p-space-sm rounded-lg space-y-1 border border-surface-container">
                            <div class="flex items-center gap-space-xs font-label-sm text-label-sm text-primary font-semibold">
                                <span class="material-symbols-outlined text-[16px] text-secondary">checklist</span> Syarat Utama:
                            </div>
                            <ul class="text-body-sm font-body-sm text-on-surface-variant list-disc list-inside space-y-0.5">
                                <li>Fotokopi KTP & NPWP Pemohon</li>
                                <li>Foto tempat/sarana kegiatan usaha</li>
                                <li>Surat Keterangan Domisili Usaha dari Kades/Lurah</li>
                            </ul>
                        </div>
                    </div>
                    <div class="space-y-space-sm pt-space-xs">
                        <div class="flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-secondary">timer</span> 1 - 2 Hari Kerja</span>
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-secondary">domain</span> Seksi Pemberdayaan</span>
                        </div>
                        <button class="w-full bg-surface-container hover:bg-surface-container-high text-primary py-2 rounded-lg font-label-md text-label-md flex items-center justify-center gap-1 transition" onclick="window.showDetailModal('Rekomendasi IUMK & NIB')">
                            <span>Bantuan Pendaftaran OSS</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- CARD: SKTM SOSIAL -->
                <div class="service-card bg-surface-container-lowest rounded-xl p-space-lg shadow-2xs border border-surface-container flex flex-col justify-between space-y-space-md hover:shadow-md transition" data-category="sosial all" data-keywords="sktm surat keterangan tidak mampu beasiswa bpjs bansos pbi dtks ditsos">
                    <div class="space-y-space-sm">
                        <div class="flex items-center justify-between">
                            <span class="bg-surface-container px-space-xs py-1 rounded text-primary font-label-sm text-label-sm font-semibold">Kesejahteraan Sosial</span>
                            <span class="text-secondary font-headline-md text-headline-md font-bold">GRATIS</span>
                        </div>
                        <h3 class="font-headline-md text-headline-md text-primary">Surat Keterangan Tidak Mampu (SKTM)</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Penerbitan SKTM untuk keperluan pembebasan biaya rumah sakit, rekomendasi BPJS PBI daerah, serta beasiswa pendidikan.
                        </p>
                        <div class="bg-surface-container-low p-space-sm rounded-lg space-y-1 border border-surface-container">
                            <div class="flex items-center gap-space-xs font-label-sm text-label-sm text-primary font-semibold">
                                <span class="material-symbols-outlined text-[16px] text-secondary">checklist</span> Syarat Utama:
                            </div>
                            <ul class="text-body-sm font-body-sm text-on-surface-variant list-disc list-inside space-y-0.5">
                                <li>SKTM asli dari Kantor Desa / Kelurahan</li>
                                <li>Fotokopi Kartu Keluarga & KTP Pemohon</li>
                                <li>Surat Pengantar / Bukti dari Rumah Sakit / Sekolah</li>
                            </ul>
                        </div>
                    </div>
                    <div class="space-y-space-sm pt-space-xs">
                        <div class="flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-secondary">timer</span> 15 Menit (Langsung)</span>
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-secondary">domain</span> Seksi Kesos</span>
                        </div>
                        <button class="w-full bg-surface-container hover:bg-surface-container-high text-primary py-2 rounded-lg font-label-md text-label-md flex items-center justify-center gap-1 transition" onclick="window.showDetailModal('SKTM Sosial')">
                            <span>Prosedur Kilat (15 Menit)</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- SPECIAL CARD: DOWNLOAD PUSAT FORMULIR & BLANGKO -->
                <div class="bg-primary text-on-primary rounded-xl p-space-lg shadow-md flex flex-col justify-between space-y-space-md">
                    <div class="space-y-space-sm">
                        <div class="w-12 h-12 rounded-lg bg-surface-container-lowest/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary-fixed text-[28px]">download_for_offline</span>
                        </div>
                        <h3 class="font-headline-md text-headline-md text-on-primary">Unduh Blangko & Formulir Standar</h3>
                        <p class="font-body-sm text-body-sm text-surface-container-high leading-relaxed">
                            Unduh formulir format resmi (.PDF & .DOCX) untuk dibawa langsung ke kantor sebelum diajukan ke PATEN {{ $siteSetting->institution_name ?? 'Kecamatan' }}.
                        </p>
                        <div class="space-y-1.5 pt-2">
                            <a class="flex items-center justify-between p-2 rounded bg-primary-container hover:bg-secondary transition text-body-sm font-body-sm" href="#" onclick="alert('Mengunduh Formulir F-1.02 Pendaftaran Penduduk...'); return false;">
                                <span>Formulir Permohonan KTP/KK (F-1.02)</span>
                                <span class="material-symbols-outlined text-[16px]">download</span>
                            </a>
                            <a class="flex items-center justify-between p-2 rounded bg-primary-container hover:bg-secondary transition text-body-sm font-body-sm" href="#" onclick="alert('Mengunduh Format Silsilah Waris...'); return false;">
                                <span>Format Pernyataan Silsilah Waris</span>
                                <span class="material-symbols-outlined text-[16px]">download</span>
                            </a>
                            <a class="flex items-center justify-between p-2 rounded bg-primary-container hover:bg-secondary transition text-body-sm font-body-sm" href="#" onclick="alert('Mengunduh Format Permohonan PPID...'); return false;">
                                <span>Formulir Permohonan Informasi (F-PPID)</span>
                                <span class="material-symbols-outlined text-[16px]">download</span>
                            </a>
                        </div>
                    </div>
                    <div class="pt-space-xs font-label-sm text-label-sm text-secondary-fixed flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">info</span> Standar Format Pelayanan RI 2026
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAKLUMAT PELAYANAN & HELPDESK RESMI -->
    <section class="w-full py-space-md">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-stretch">
                <!-- MAKLUMAT PELAYANAN (CIVIC PLEDGE) -->
                <div class="lg:col-span-7 bg-surface-container-lowest p-space-lg rounded-xl shadow-2xs border border-surface-container flex flex-col justify-between">
                    <div class="space-y-space-md">
                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-secondary text-[32px]">military_tech</span>
                            <div>
                                <span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary font-bold">Janji Layanan Aparatur</span>
                                <h3 class="font-headline-lg text-headline-lg text-primary">Maklumat Standar Pelayanan Publik</h3>
                            </div>
                        </div>
                        <div class="p-space-md bg-surface-container-low rounded-xl space-y-space-xs italic font-headline-md text-[17px] text-primary leading-relaxed border-l-4 border-secondary">
                            "Dengan ini, kami seluruh aparatur Pemerintah {{ $siteSetting->institution_name ?? 'Kecamatan' }} menyatakan sanggup menyelenggarakan pelayanan publik sesuai standar pelayanan yang telah ditetapkan dan siap menerima sanksi sesuai peraturan perundang-undangan apabila tidak menepati janji layanan."
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm font-body-sm text-body-sm text-on-surface-variant">
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-secondary text-[20px] shrink-0">check_circle</span>
                                <span>Transparan: Informasi syarat, waktu, dan alur terpasang jelas di loket dan web.</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-secondary text-[20px] shrink-0">check_circle</span>
                                <span>Akuntabel: Seluruh penandatanganan teregistrasi TTE resmi dan berlandaskan hukum.</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-secondary text-[20px] shrink-0">check_circle</span>
                                <span>Nol Pungli: Tidak memungut biaya apapun di luar peraturan daerah yang sah.</span>
                            </div>
                            <div class="flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-secondary text-[20px] shrink-0">check_circle</span>
                                <span>Inklusif: Jalur kursi roda, loket ramah lansia, dan layanan prioritas tersedia.</span>
                            </div>
                        </div>
                    </div>
                    <div class="pt-space-md mt-space-md flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant border-t border-surface-container">
                        <span>Ditetapkan secara Resmi</span>
                        <span class="font-bold text-primary">Pimpinan beserta Seluruh Aparatur Instansi</span>
                    </div>
                </div>

                <!-- HELPDESK & KONSULTASI WHATSAPP -->
                <div class="lg:col-span-5 bg-primary-container text-on-primary p-space-lg rounded-xl shadow-sm flex flex-col justify-between space-y-space-md">
                    <div class="space-y-space-md">
                        <div class="inline-flex items-center gap-space-xs bg-surface-container-lowest/10 px-space-sm py-1 rounded-full text-secondary-fixed font-label-sm text-label-sm">
                            <span class="w-2 h-2 rounded-full bg-secondary-fixed animate-ping"></span>
                            Layanan Bantuan Warga
                        </div>
                        <h3 class="font-headline-lg text-headline-lg text-on-primary">
                            Butuh Konsultasi Syarat Sebelum Datang ke Kantor?
                        </h3>
                        <p class="font-body-md text-body-md text-surface-container-high leading-relaxed">
                            Petugas front office siap memeriksa kelengkapan berkas Anda terlebih dahulu lewat pesan WhatsApp agar meminimalisir kendala saat datang ke meja layanan.
                        </p>
                        <div class="space-y-space-xs">
                            <div class="flex items-center gap-space-sm p-space-sm rounded-lg bg-primary">
                                <span class="material-symbols-outlined text-secondary-fixed text-[24px]">chat</span>
                                <div>
                                    <div class="font-label-sm text-[11px] text-surface-container-high uppercase">WhatsApp Helpdesk</div>
                                    <div class="font-title-lg text-title-lg text-on-primary font-bold">{{ $siteSetting->phone ?? '0812-5555-7890' }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-space-sm p-space-sm rounded-lg bg-primary">
                                <span class="material-symbols-outlined text-secondary-fixed text-[24px]">schedule</span>
                                <div>
                                    <div class="font-label-sm text-[11px] text-surface-container-high uppercase">Jam Operasional</div>
                                    <div class="font-body-sm text-body-sm text-on-primary">{{ $siteSetting->service_hours ?? 'Senin - Jumat: 08.00 - 15.30 WITA' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-space-xs">
                        <a class="w-full bg-secondary-container hover:bg-secondary text-primary hover:text-on-secondary py-3 rounded-lg font-label-md text-label-md flex items-center justify-center gap-space-xs transition shadow-2xs font-bold" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSetting->phone ?? '6281255557890') }}?text=Halo%20Admin%20PATEN,%20saya%20ingin%20konsultasi%20syarat%20pengurusan%20surat." rel="noopener" target="_blank">
                            <span class="material-symbols-outlined text-[20px]">chat</span>
                            <span>Hubungi Petugas PATEN Sekarang</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL DETAIL SYARAT LENGKAP INTERAKTIF -->
    <div class="fixed inset-0 bg-primary/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4" id="serviceDetailModal">
        <div class="bg-surface-container-lowest w-full max-w-2xl rounded-xl shadow-xl overflow-hidden flex flex-col max-h-[90vh] border border-surface-container">
            <div class="bg-primary text-on-primary p-space-md flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-secondary-fixed">assignment</span>
                    <h3 class="font-title-lg text-title-lg text-on-primary" id="modalTitle">Detail Prosedur Layanan</h3>
                </div>
                <button class="text-on-primary/80 hover:text-on-primary p-1 rounded-full cursor-pointer" onclick="window.closeDetailModal()">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="p-space-lg overflow-y-auto space-y-space-md text-on-surface">
                <div class="bg-surface-container-low p-space-md rounded-lg space-y-1 border border-surface-container">
                    <span class="font-label-sm text-label-sm uppercase font-bold text-secondary">Ketentuan Resmi PATEN</span>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        Seluruh pemohon wajib membawa berkas asli untuk diverifikasi petugas dan melampirkan salinan fotokopi rangkap 1 sesuai ketentuan.
                    </p>
                </div>
                <div class="space-y-space-xs">
                    <h4 class="font-label-md text-label-md text-primary font-bold">1. Ketentuan Dokumen Persyaratan:</h4>
                    <ul class="text-body-sm font-body-sm text-on-surface-variant space-y-1 pl-4 list-disc">
                        <li>Surat pengantar resmi yang ditandatangani RT dan diketahui Kepala Desa/Lurah setempat.</li>
                        <li>Kartu Tanda Penduduk (KTP) dan Kartu Keluarga (KK) yang masih berlaku.</li>
                        <li>Mengisi formulir permohonan yang disediakan di Loket Informasi atau diunduh dari web.</li>
                        <li>Bagi permohonan yang diwakilkan, wajib menyertakan Surat Kuasa bermaterai disertai KTP penerima kuasa.</li>
                    </ul>
                </div>
                <div class="space-y-space-xs">
                    <h4 class="font-label-md text-label-md text-primary font-bold">2. Biaya & Durasi Pelayanan:</h4>
                    <div class="grid grid-cols-2 gap-space-sm font-body-sm text-body-sm">
                        <div class="bg-surface-container p-2.5 rounded text-center">
                            <span class="text-on-surface-variant text-[12px] block">Biaya Retribusi</span>
                            <strong class="text-secondary font-bold text-[16px]">Rp 0,- (GRATIS)</strong>
                        </div>
                        <div class="bg-surface-container p-2.5 rounded text-center">
                            <span class="text-on-surface-variant text-[12px] block">Durasi Penyelesaian</span>
                            <strong class="text-primary font-bold text-[16px]">1 - 2 Hari Kerja</strong>
                        </div>
                    </div>
                </div>
                <div class="space-y-space-xs">
                    <h4 class="font-label-md text-label-md text-primary font-bold">3. Pengaduan Layanan:</h4>
                    <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                        Jika mendapati indikasi pungutan liar, diskriminasi, atau kelambatan tanpa keterangan, silakan adukan via meja kontak resmi atau portal SP4N LAPOR!.
                    </p>
                </div>
            </div>
            <div class="bg-surface-container-low p-space-md flex items-center justify-end gap-space-sm border-t border-surface-container">
                <button class="px-space-md py-2 bg-surface-container hover:bg-surface-container-high rounded-lg font-label-md text-label-md text-on-surface cursor-pointer" onclick="window.closeDetailModal()">Tutup</button>
                <button class="px-space-md py-2 bg-primary hover:bg-primary-container text-on-primary rounded-lg font-label-md text-label-md flex items-center gap-1 cursor-pointer" onclick="alert('Lembar persyaratan telah disiapkan untuk dicetak.'); window.closeDetailModal();">
                    <span class="material-symbols-outlined text-[16px]">print</span> Cetak Lembar Persyaratan
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.filterCategory = function(cat) {
        const buttons = document.querySelectorAll('.cat-btn');
        buttons.forEach(btn => {
            if(btn.dataset.cat === cat) {
                btn.classList.add('bg-primary', 'text-on-primary');
                btn.classList.remove('bg-surface-container-lowest', 'text-on-surface-variant');
            } else {
                btn.classList.remove('bg-primary', 'text-on-primary');
                btn.classList.add('bg-surface-container-lowest', 'text-on-surface-variant');
            }
        });

        const cards = document.querySelectorAll('.service-card');
        cards.forEach(card => {
            if(cat === 'all') {
                card.classList.remove('hidden');
            } else {
                const categories = card.dataset.category || '';
                if(categories.includes(cat)) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            }
        });
    };

    window.filterBySearch = function() {
        const q = document.getElementById('serviceSearch').value.toLowerCase();
        const cards = document.querySelectorAll('.service-card');
        cards.forEach(card => {
            const keywords = card.dataset.keywords || '';
            const title = card.querySelector('h3') ? card.querySelector('h3').innerText.toLowerCase() : '';
            if(keywords.toLowerCase().includes(q) || title.includes(q)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    };

    window.lacakPermohonan = function() {
        const resi = document.getElementById('resiNumber')?.value;
        const resultBox = document.getElementById('trackingResult');
        if(resultBox) {
            resultBox.classList.remove('hidden');
            resultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    };

    window.showDetailModal = function(title) {
        const titleEl = document.getElementById('modalTitle');
        if(titleEl) titleEl.innerText = 'Standar Pelayanan: ' + title;
        document.getElementById('serviceDetailModal')?.classList.remove('hidden');
    };

    window.closeDetailModal = function() {
        document.getElementById('serviceDetailModal')?.classList.add('hidden');
    };
</script>
@endpush
@endsection
