@extends('layouts.app')

@section('title', 'Portal Resmi ' . ($siteSetting->institution_name ?? 'Kecamatan Loa Kulu'))

@section('content')
<div class="flex flex-col w-full space-y-space-xl">
    <!-- 1. Pengumuman Darurat / Banner Penting -->
    @if(isset($emergencyBanner) && $emergencyBanner)
        <x-emergency-banner 
            :title="$emergencyBanner->judul"
            :message="$emergencyBanner->deskripsi ?? ''"
            :link="$emergencyBanner->tautan ?: route('berita.index')"
            :linkLabel="$emergencyBanner->label_tautan ?: 'Lihat Rincian'"
        />
    @else
        <x-emergency-banner 
            title="PENGUMUMAN PENTING"
            message="Penyaluran Bantuan Sosial & Jadwal Rekam KTP-el Jemput Bola di Desa Jembayan — Simak Persyaratan & Dokumen Wajib."
            link="{{ route('berita.index') }}"
            linkLabel="Lihat Rincian Jadwal"
        />
    @endif

@php
    $pimpinanName = 'H. Muhammad Rifki, S.STP, M.Si';
    $pimpinanTitle = 'Pimpinan Instansi / Camat';
    $portraitUrl = null;
    $totalPenduduk = '39.750';
    $periodePenduduk = 'Data Semester I';
    $totalLayanan = 5;

    if (\Illuminate\Support\Facades\Schema::hasTable('halaman')) {
        try {
            $halamanPejabat = \App\Models\Halaman::active()->where('slug', 'profil-pejabat')->first();
            if ($halamanPejabat && preg_match('/Nama:\s*<\/strong>\s*([^<]+)/i', $halamanPejabat->isi, $mName)) {
                $pimpinanName = trim($mName[1]);
            }
            if ($halamanPejabat && preg_match('/Jabatan:\s*<\/strong>\s*([^<]+)/i', $halamanPejabat->isi, $mTitle)) {
                $pimpinanTitle = trim($mTitle[1]);
            }
            if ($halamanPejabat?->gambar_utama) {
                $portraitUrl = asset('storage/' . $halamanPejabat->gambar_utama);
            }
        } catch (\Throwable $e) {}
    }

    if (\Illuminate\Support\Facades\Schema::hasTable('statistik')) {
        try {
            $statKependudukan = \App\Models\Statistik::active()->where('kategori', 'Kependudukan')->first();
            if ($statKependudukan) {
                $periodePenduduk = $statKependudukan->periode ?: ('Tahun ' . $statKependudukan->tahun);
                if (preg_match('/Total\s+Kecamatan.*?(\d+[\.,]\d+)/is', $statKependudukan->isi, $mTotal)) {
                    $totalPenduduk = $mTotal[1];
                }
            }
        } catch (\Throwable $e) {}
    }

    if (\Illuminate\Support\Facades\Schema::hasTable('layanan')) {
        try {
            $totalLayananCount = \App\Models\Layanan::where('is_active', true)->count();
            if ($totalLayananCount > 0) {
                $totalLayanan = $totalLayananCount;
            }
        } catch (\Throwable $e) {}
    }
@endphp

    <!-- 2. Hero Section Megah & 4 Angka Kunci -->
    <x-hero 
        :pimpinanName="$pimpinanName"
        :pimpinanTitle="$pimpinanTitle"
        :portraitUrl="$portraitUrl"
        :totalDesa="12"
        :totalPenduduk="$totalPenduduk"
        :periodePenduduk="$periodePenduduk"
        indeksKepuasan="98,4%"
        :totalLayanan="$totalLayanan"
    />

    <!-- 3. Menu Pelayanan Kilat Warga (PATEN) -->
    <section class="w-full bg-surface-container-low/60 px-margin-mobile lg:px-margin py-space-xl rounded-2xl border border-surface-container" id="layanan">
        <div class="max-w-7xl mx-auto flex flex-col gap-space-lg">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-sm">
                <div>
                    <span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest font-bold">
                        Pelayanan Administrasi Terpadu (PATEN)
                    </span>
                    <h2 class="font-headline-lg text-2xl sm:text-3xl font-bold text-primary">
                        Portal Pelayanan Kilat Warga
                    </h2>
                    <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl mt-1">
                        Akses mandiri pengurusan dokumen kependudukan, perizinan berusaha, rekomendasi sosial, hingga pelaporan resmi kecamatan.
                    </p>
                </div>
                <a href="{{ route('layanan') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:text-secondary transition-colors shrink-0">
                    <span>Lihat Semua Layanan ({{ $totalLayanan }})</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            </div>

            <!-- Grid Kartu Layanan Unggulan (6 Cards, 3 Columns) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-md">
                <!-- 1. Kependudukan & Catcapil -->
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between group border border-outline-variant/30">
                    <div>
                        <div class="flex items-center justify-between mb-space-sm">
                            <div class="w-12 h-12 rounded-lg bg-surface-container text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-colors">
                                <span class="material-symbols-outlined text-[24px]">badge</span>
                            </div>
                            <span class="font-label-sm text-xs bg-surface-container-high px-2 py-0.5 rounded text-primary font-semibold">Gratis</span>
                        </div>
                        <h3 class="font-headline-md text-lg font-bold text-primary group-hover:text-secondary transition-colors">Kependudukan &amp; Catcapil</h3>
                        <p class="font-body-sm text-sm text-on-surface-variant mt-1.5 leading-relaxed">
                            Penerbitan KTP-el pemula, Kartu Identitas Anak (KIA), Kartu Keluarga baru/perubahan, dan kutipan akta kelahiran bekerja sama dengan Disdukcapil.
                        </p>
                    </div>
                    <div class="mt-space-md pt-space-sm border-t border-surface-container flex items-center justify-between text-xs">
                        <span class="text-on-surface-variant">Estimasi: 1 - 2 Hari</span>
                        <a class="font-semibold text-secondary hover:text-primary flex items-center gap-1" href="{{ route('layanan') }}#ktp">
                            <span>Buka Layanan</span>
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                </div>

                <!-- 2. Izin Usaha Mikro -->
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between group border border-outline-variant/30">
                    <div>
                        <div class="flex items-center justify-between mb-space-sm">
                            <div class="w-12 h-12 rounded-lg bg-surface-container text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-colors">
                                <span class="material-symbols-outlined text-[24px]">storefront</span>
                            </div>
                            <span class="font-label-sm text-xs bg-secondary-fixed/50 text-secondary px-2 py-0.5 rounded font-bold">Integrasi OSS</span>
                        </div>
                        <h3 class="font-headline-md text-lg font-bold text-primary group-hover:text-secondary transition-colors">Izin Usaha Mikro &amp; Kecil</h3>
                        <p class="font-body-sm text-sm text-on-surface-variant mt-1.5 leading-relaxed">
                            Penerbitan Rekomendasi IUMK, pendampingan legalitas NIB (Nomor Induk Berusaha), serta fasilitasi sertifikasi halal UMKM binaan.
                        </p>
                    </div>
                    <div class="mt-space-md pt-space-sm border-t border-surface-container flex items-center justify-between text-xs">
                        <span class="text-on-surface-variant">Estimasi: 1 Hari Kerja</span>
                        <a class="font-semibold text-secondary hover:text-primary flex items-center gap-1" href="{{ route('layanan') }}#iumk">
                            <span>Buka Layanan</span>
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                </div>

                <!-- 3. Surat Pengantar & Keterangan -->
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between group border border-outline-variant/30">
                    <div>
                        <div class="flex items-center justify-between mb-space-sm">
                            <div class="w-12 h-12 rounded-lg bg-surface-container text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-colors">
                                <span class="material-symbols-outlined text-[24px]">description</span>
                            </div>
                            <span class="font-label-sm text-xs bg-surface-container-high px-2 py-0.5 rounded text-primary font-semibold">Reguler</span>
                        </div>
                        <h3 class="font-headline-md text-lg font-bold text-primary group-hover:text-secondary transition-colors">Surat Pengantar &amp; Keterangan</h3>
                        <p class="font-body-sm text-sm text-on-surface-variant mt-1.5 leading-relaxed">
                            Legalisasi Surat Keterangan Tidak Mampu (SKTM) untuk beasiswa &amp; KIS, Surat Keterangan Domisili Organisasi, dan Surat Pengantar Nikah.
                        </p>
                    </div>
                    <div class="mt-space-md pt-space-sm border-t border-surface-container flex items-center justify-between text-xs">
                        <span class="text-on-surface-variant">Estimasi: 30 Menit</span>
                        <a class="font-semibold text-secondary hover:text-primary flex items-center gap-1" href="{{ route('layanan') }}#sktm">
                            <span>Buka Layanan</span>
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                </div>

                <!-- 4. Pertanahan & Registrasi Desa -->
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between group border border-outline-variant/30">
                    <div>
                        <div class="flex items-center justify-between mb-space-sm">
                            <div class="w-12 h-12 rounded-lg bg-surface-container text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-on-primary transition-colors">
                                <span class="material-symbols-outlined text-[24px]">landscape</span>
                            </div>
                            <span class="font-label-sm text-xs bg-surface-container-high px-2 py-0.5 rounded text-primary font-semibold">Seksi Trantib</span>
                        </div>
                        <h3 class="font-headline-md text-lg font-bold text-primary group-hover:text-secondary transition-colors">Pertanahan &amp; Registrasi Desa</h3>
                        <p class="font-body-sm text-sm text-on-surface-variant mt-1.5 leading-relaxed">
                            Verifikasi dan registrasi Surat Pernyataan Penguasaan Fisik Bidang Tanah (SPORADIK), rekomendasi alih fungsi lahan, dan mediasi batas desa.
                        </p>
                    </div>
                    <div class="mt-space-md pt-space-sm border-t border-surface-container flex items-center justify-between text-xs">
                        <span class="text-on-surface-variant">Estimasi: 3 - 5 Hari</span>
                        <a class="font-semibold text-secondary hover:text-primary flex items-center gap-1" href="{{ route('layanan') }}#tanah">
                            <span>Buka Layanan</span>
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </a>
                    </div>
                </div>

                <!-- 5. Pengaduan Online SP4N LAPOR -->
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between group border border-outline-variant/30">
                    <div>
                        <div class="flex items-center justify-between mb-space-sm">
                            <div class="w-12 h-12 rounded-lg bg-red-50 text-red-600 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[24px]">report</span>
                            </div>
                            <span class="font-label-sm text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded font-bold">Tindak Cepat</span>
                        </div>
                        <h3 class="font-headline-md text-lg font-bold text-primary group-hover:text-red-700 transition-colors">Pengaduan SP4N LAPOR!</h3>
                        <p class="font-body-sm text-sm text-on-surface-variant mt-1.5 leading-relaxed">
                            Sampaikan keluhan terkait pelayanan pamong, infrastruktur jalan, penerangan umum, dan ketertiban umum langsung ke Camat dan instansi terkait.
                        </p>
                    </div>
                    <div class="mt-space-md pt-space-sm border-t border-surface-container flex items-center justify-between text-xs">
                        <span class="text-on-surface-variant">Respon: &lt; 24 Jam Kerja</span>
                        <a class="font-semibold text-red-700 hover:underline flex items-center gap-1" href="https://lapor.go.id" rel="noopener" target="_blank">
                            <span>Kirim Aduan</span>
                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                        </a>
                    </div>
                </div>

                <!-- 6. Cek Status Pengajuan Dokumen PPID (Lacak Mandiri) -->
                <div class="bg-gradient-to-br from-primary via-primary to-primary-container text-on-primary p-space-md rounded-xl shadow-md flex flex-col justify-between border-2 border-secondary/40">
                    <div>
                        <div class="flex items-center justify-between mb-space-sm">
                            <div class="w-12 h-12 rounded-lg bg-secondary text-primary flex items-center justify-center font-bold">
                                <span class="material-symbols-outlined text-[24px]">policy</span>
                            </div>
                            <span class="font-label-sm text-xs bg-secondary text-primary px-2 py-0.5 rounded font-extrabold uppercase tracking-wide">Lacak Mandiri</span>
                        </div>
                        <h3 class="font-headline-md text-lg font-bold text-on-primary">Cek Status Pengajuan Dokumen</h3>
                        <p class="font-body-sm text-xs text-on-primary-container mt-1.5 leading-relaxed">
                            Masukkan Nomor Registrasi / Resi berkas permohonan informasi PPID yang Anda terima saat mendaftar.
                        </p>
                        <form action="{{ route('ppid.lacak') }}" method="GET" class="mt-3 flex gap-1.5">
                            <input 
                                class="w-full bg-white text-slate-800 px-3 py-2 rounded-lg text-xs font-mono font-bold focus:outline-none focus:ring-2 focus:ring-secondary uppercase placeholder:normal-case placeholder:font-normal placeholder:text-slate-400" 
                                placeholder="Contoh: PPID/KEC-LK/..." 
                                type="text"
                                name="nomor"
                            />
                            <button class="bg-secondary hover:bg-secondary-fixed text-primary px-3.5 py-2 rounded-lg font-bold text-xs shrink-0 transition-colors shadow-xs" type="submit">
                                Cek
                            </button>
                        </form>
                    </div>
                    <div class="mt-space-md pt-space-xs flex items-center gap-1.5 text-on-primary-container text-[11px] border-t border-white/10">
                        <span class="material-symbols-outlined text-[16px] text-secondary-fixed">verified</span>
                        <span>Pelacakan real-time sistem PPID resmi.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Berita & Publikasi Terkini -->
    <section class="w-full px-margin-mobile lg:px-margin py-space-xl bg-surface" id="berita">
        <div class="max-w-7xl mx-auto flex flex-col gap-space-lg">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-sm">
                <div>
                    <span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest font-bold">
                        Warta Pemerintahan &amp; Pembangunan
                    </span>
                    <h2 class="font-headline-lg text-2xl sm:text-3xl font-bold text-primary">
                        Kabar Terkini {{ $siteSetting->institution_acronym ?? 'Loa Kulu' }}
                    </h2>
                </div>
                <a href="{{ route('berita.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:text-secondary transition-colors shrink-0">
                    <span>Arsip Berita Lengkap</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            </div>

            @php
                $firstBerita = $beritaTerbaru[0] ?? null;
                $otherBerita = array_slice($beritaTerbaru, 1, 2);
            @endphp

            @if($firstBerita)
                <!-- Featured News Item (Large Horizontal Card) -->
                <div class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow grid grid-cols-1 md:grid-cols-12 border border-outline-variant/30 group">
                    <div class="md:col-span-5 h-56 md:h-auto overflow-hidden">
                        <img 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
                            src="{{ $firstBerita['gambar'] ?: asset('images/berita-musrenbang.jpg') }}" 
                            alt="{{ $firstBerita['judul'] }}"
                            loading="lazy"
                        />
                    </div>
                    <div class="md:col-span-7 p-space-md flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-space-sm mb-space-xs text-xs">
                                <span class="font-label-sm text-[11px] bg-secondary-fixed text-primary px-space-xs py-0.5 rounded font-bold uppercase">{{ $firstBerita['kategori'] }}</span>
                                <span class="font-body-sm text-on-surface-variant flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px] text-secondary">calendar_today</span>
                                    {{ $firstBerita['tanggal'] }}
                                </span>
                            </div>
                            <h3 class="font-headline-md text-lg sm:text-xl font-bold text-primary leading-snug group-hover:text-secondary transition-colors">
                                <a href="{{ route('berita.detail', $firstBerita['slug']) }}">{{ $firstBerita['judul'] }}</a>
                            </h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-space-xs line-clamp-3 leading-relaxed">
                                {{ $firstBerita['ringkasan'] }}
                            </p>
                        </div>
                        <div class="mt-space-md pt-space-xs border-t border-surface-container flex items-center justify-between text-xs">
                            <span class="text-on-surface-variant">Oleh: {{ $firstBerita['penulis'] }}</span>
                            <a class="font-semibold text-primary hover:text-secondary flex items-center gap-1 group-hover:translate-x-1 transition-transform" href="{{ route('berita.detail', $firstBerita['slug']) }}">
                                <span>Baca Selengkapnya</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </a>
                        </div>
                    </div>
                </div>

                @if(!empty($otherBerita))
                    <!-- Sub-grid (2 Cards) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
                        @foreach($otherBerita as $subItem)
                            <x-kartu-berita :berita="$subItem" />
                        @endforeach
                    </div>
                @endif
            @else
                <div class="col-span-full p-8 text-center bg-surface-container-low rounded-xl">
                    <p class="text-on-surface-variant text-sm">Belum ada berita yang dipublikasikan.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- 6. Agenda Kegiatan & Pengumuman -->
    <section class="w-full bg-surface-container-low px-margin-mobile lg:px-margin py-space-xl rounded-2xl border border-surface-container">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
            <!-- Agenda Kegiatan -->
            <div class="lg:col-span-6 space-y-space-md">
                <div class="flex items-center justify-between border-b border-surface-container pb-2">
                    <div>
                        <span class="font-label-sm text-label-sm text-secondary uppercase tracking-wider font-bold">Kalender Kedinasan</span>
                        <h2 class="font-headline-lg text-xl sm:text-2xl font-bold text-primary">Agenda Kegiatan</h2>
                    </div>
                    <span class="material-symbols-outlined text-secondary text-[24px]">event</span>
                </div>
                <div class="space-y-space-xs">
                    @forelse($agenda as $ag)
                        <div class="p-space-md bg-surface-container-lowest rounded-xl border border-surface-container shadow-2xs flex items-start gap-space-sm">
                            <div class="bg-primary text-on-primary rounded-lg p-2 text-center shrink-0 min-w-16">
                                <span class="material-symbols-outlined text-[18px] text-secondary-fixed block">calendar_today</span>
                                <span class="text-[10px] uppercase font-bold block mt-0.5">{{ explode(' ', $ag['tanggal'])[1] ?? 'Bln' }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-title-lg text-sm font-bold text-primary leading-snug">{{ $ag['acara'] }}</h3>
                                <p class="text-xs text-on-surface-variant mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px] text-secondary">schedule</span> {{ $ag['waktu'] }} &bull; {{ $ag['lokasi'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-on-surface-variant">Belum ada agenda kegiatan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Pengumuman Resmi -->
            <div class="lg:col-span-6 space-y-space-md">
                <div class="flex items-center justify-between border-b border-surface-container pb-2">
                    <div>
                        <span class="font-label-sm text-label-sm text-secondary uppercase tracking-wider font-bold">Warta Resmi</span>
                        <h2 class="font-headline-lg text-xl sm:text-2xl font-bold text-primary">Pengumuman</h2>
                    </div>
                    <span class="material-symbols-outlined text-secondary text-[24px]">campaign</span>
                </div>
                <div class="space-y-space-xs">
                    @forelse($pengumuman as $peng)
                        <div class="p-space-md bg-surface-container-lowest rounded-xl border border-surface-container shadow-2xs space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-label-sm text-[11px] font-bold text-primary bg-secondary-fixed px-2 py-0.5 rounded">Pengumuman Resmi</span>
                                <span class="text-on-surface-variant font-medium">{{ $peng['tanggal'] }}</span>
                            </div>
                            <h3 class="font-title-lg text-sm font-bold text-primary hover:text-secondary leading-snug">
                                <a href="{{ route('berita.detail', $peng['slug']) }}">{{ $peng['judul'] }}</a>
                            </h3>
                            <p class="text-xs text-on-surface-variant line-clamp-2 leading-relaxed">{{ $peng['ringkasan'] }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-on-surface-variant">Belum ada pengumuman.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- 7. Portal PPID & Statistik Transparansi Anggaran -->
    <section class="w-full bg-surface-container-low/40 px-margin-mobile lg:px-margin py-space-xl rounded-2xl border border-surface-container" id="ppid">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
                <!-- Left: Penjelasan PPID -->
                <div class="lg:col-span-6 space-y-space-md">
                    <div class="inline-flex items-center gap-space-xs font-label-sm text-label-sm text-secondary uppercase tracking-widest bg-secondary-container/40 px-space-sm py-1 rounded w-fit font-bold border border-secondary/20">
                        <span class="material-symbols-outlined text-[16px]">policy</span>
                        <span>UU No. 14 Tahun 2008</span>
                    </div>

                    <h2 class="font-headline-lg text-2xl sm:text-3xl font-bold text-primary">
                        Keterbukaan Informasi Publik (PPID)
                    </h2>

                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                        Kami menjamin hak setiap warga negara untuk memperoleh salinan informasi publik yang sah, akurat, dan dapat dipertanggungjawabkan melalui Pejabat Pengelola Informasi dan Dokumentasi (PPID).
                    </p>

                    <div class="grid grid-cols-2 gap-space-sm pt-2">
                        <div class="bg-surface-container-lowest p-space-sm rounded-lg border border-surface-container">
                            <span class="text-xl font-bold text-primary">100%</span>
                            <p class="text-xs text-on-surface-variant">Dokumen Terklasifikasi</p>
                        </div>
                        <div class="bg-surface-container-lowest p-space-sm rounded-lg border border-surface-container">
                            <span class="text-xl font-bold text-secondary">Maks. 10 Hari</span>
                            <p class="text-xs text-on-surface-variant">Batas Standar Respon</p>
                        </div>
                    </div>

                    <div class="pt-space-xs flex flex-wrap gap-space-sm">
                        <a href="{{ route('publikasi') }}" class="inline-flex items-center gap-1.5 px-space-md py-2.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-semibold text-xs shadow-xs transition-colors">
                            <span class="material-symbols-outlined text-[18px]">folder_open</span>
                            <span>Katalog Informasi Publik</span>
                        </a>
                        <a href="{{ route('publikasi') }}#klasifikasi-dokumen" class="inline-flex items-center gap-1.5 px-space-md py-2.5 rounded-lg bg-secondary hover:bg-secondary-fixed text-primary font-semibold text-xs shadow-xs transition-colors">
                            <span class="material-symbols-outlined text-[18px]">post_add</span>
                            <span>Ajukan Permohonan Online</span>
                        </a>
                    </div>
                </div>

                <!-- Right: Ringkasan Kategori Dokumen PPID -->
                <div class="lg:col-span-6 bg-surface-container-lowest p-space-lg rounded-xl shadow-2xs border border-surface-container space-y-space-sm">
                    <h3 class="font-title-lg text-lg font-bold text-primary border-b border-surface-container pb-2">
                        Klasifikasi Dokumen Publik Resmi
                    </h3>

                    <div class="space-y-3 pt-2">
                        <a href="{{ route('publikasi') }}?kategori=berkala" class="flex items-center justify-between p-3 rounded-lg bg-surface-container-low hover:bg-secondary/10 transition-colors group">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary text-[22px]">calendar_today</span>
                                <div>
                                    <h4 class="text-xs font-bold text-primary group-hover:text-secondary">Informasi Berkala</h4>
                                    <p class="text-[11px] text-on-surface-variant">Rencana Kerja, APBD, dan Laporan Kinerja Tahunan</p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-[18px] text-outline group-hover:translate-x-1 transition-transform">chevron_right</span>
                        </a>

                        <a href="{{ route('publikasi') }}?kategori=serta_merta" class="flex items-center justify-between p-3 rounded-lg bg-surface-container-low hover:bg-secondary/10 transition-colors group">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-error text-[22px]">warning</span>
                                <div>
                                    <h4 class="text-xs font-bold text-primary group-hover:text-secondary">Informasi Serta-Merta</h4>
                                    <p class="text-[11px] text-on-surface-variant">Peringatan Bencana, Kebencanaan Alam, dan Keadaan Darurat</p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-[18px] text-outline group-hover:translate-x-1 transition-transform">chevron_right</span>
                        </a>

                        <a href="{{ route('publikasi') }}?kategori=setiap_saat" class="flex items-center justify-between p-3 rounded-lg bg-surface-container-low hover:bg-secondary/10 transition-colors group">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-secondary text-[22px]">fact_check</span>
                                <div>
                                    <h4 class="text-xs font-bold text-primary group-hover:text-secondary">Informasi Setiap Saat</h4>
                                    <p class="text-[11px] text-on-surface-variant">SOP Layanan, Profil Pejabat, dan Peraturan Daerah</p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-[18px] text-outline group-hover:translate-x-1 transition-transform">chevron_right</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. Peta Wilayah Yurisdiksi & 12 Desa -->
    <section class="w-full bg-surface px-margin-mobile lg:px-margin py-space-xl">
        <div class="max-w-7xl mx-auto flex flex-col gap-space-lg">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-sm">
                <div>
                    <span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest font-bold">
                        Peta Wilayah Yurisdiksi
                    </span>
                    <h2 class="font-headline-lg text-2xl sm:text-3xl font-bold text-primary">
                        12 Desa &amp; Kelurahan di {{ $siteSetting->institution_name ?? 'Kecamatan Loa Kulu' }}
                    </h2>
                    <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl mt-1">
                        Terhubung secara sinergis melalui jaringan koordinasi kepala desa dalam mendukung pelayanan prima bagi masyarakat.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-md items-center">
                <!-- Peta Visual / Info Center -->
                <div class="lg:col-span-7">
                    <div class="w-full h-80 bg-cover bg-center rounded-xl shadow-md overflow-hidden relative border border-surface-container" style="background-image: url('{{ asset('images/placeholder-map.svg') }}')">
                        <div class="absolute inset-0 bg-primary/40"></div>
                        <div class="absolute bottom-space-sm left-space-sm right-space-sm sm:right-auto bg-surface-container-lowest/95 backdrop-blur-sm p-space-sm rounded-lg shadow-sm flex items-center gap-space-sm max-w-md border border-surface-container">
                            <span class="material-symbols-outlined text-secondary text-[24px]">location_on</span>
                            <div>
                                <div class="font-label-md text-xs sm:text-sm text-primary font-bold">Pusat Pemerintahan Kecamatan</div>
                                <div class="font-body-sm text-[11px] text-on-surface-variant">{{ $siteSetting->address ?? 'Jl. Yos Sudarso No. 1, Desa Loa Kulu Kota' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 12 Desa Grid Badges -->
                <div class="lg:col-span-5 grid grid-cols-2 gap-2">
                    @php
                        $desaList = [
                            ['nama' => 'Loa Kulu Kota', 'tipe' => 'Ibukota Kecamatan'],
                            ['nama' => 'Jembayan', 'tipe' => 'Desa'],
                            ['nama' => 'Jembayan Tengah', 'tipe' => 'Desa'],
                            ['nama' => 'Jembayan Dalam', 'tipe' => 'Desa'],
                            ['nama' => 'Rempanga', 'tipe' => 'Desa'],
                            ['nama' => 'Sepakat', 'tipe' => 'Desa'],
                            ['nama' => 'Ponoragan', 'tipe' => 'Desa'],
                            ['nama' => 'Lohan Dalam', 'tipe' => 'Desa'],
                            ['nama' => 'Sumber Sari', 'tipe' => 'Desa'],
                            ['nama' => 'Sungai Payang', 'tipe' => 'Desa'],
                            ['nama' => 'Lung Anai', 'tipe' => 'Desa Budaya'],
                            ['nama' => 'Margahayu', 'tipe' => 'Desa'],
                        ];
                    @endphp
                    @foreach($desaList as $desa)
                        <div class="bg-surface-container-lowest p-2.5 rounded-lg shadow-2xs flex items-center gap-2 border border-surface-container hover:border-secondary transition-colors">
                            <span class="material-symbols-outlined text-secondary text-[18px]">location_city</span>
                            <div class="truncate">
                                <div class="font-label-md text-xs text-primary font-bold truncate">{{ $desa['nama'] }}</div>
                                <div class="font-body-sm text-[10px] text-on-surface-variant truncate">{{ $desa['tipe'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
