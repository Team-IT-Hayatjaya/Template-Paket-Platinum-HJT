@extends('layouts.app')

@section('title', 'Profil Instansi & Struktur Pemerintahan - ' . ($siteSetting->institution_name ?? 'Kecamatan Loa Kulu'))

@section('content')
<div class="flex flex-col w-full space-y-space-xl">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="['Profil Instansi' => route('profil')]" />

    <!-- Header Section -->
    <section class="w-full bg-surface-container-low/60 px-margin-mobile lg:px-margin py-space-lg border-b border-outline-variant/20 rounded-2xl">
        <div class="max-w-7xl mx-auto space-y-space-xs">
            <span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest font-bold">
                Profil Instansi Resmi
            </span>
            <h1 class="font-headline-xl text-3xl sm:text-4xl font-bold text-primary">
                Profil Instansi {{ $siteSetting->institution_name ?? 'Kecamatan Loa Kulu' }}
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed">
                Penyelenggara urusan pemerintahan umum, koordinasi pembangunan kewilayahan, dan pelayanan administrasi terpadu bagi masyarakat.
            </p>
        </div>
    </section>

    <!-- Konten Utama Profil -->
    <div class="max-w-7xl mx-auto w-full space-y-space-xl">
        <!-- 1. Sejarah Singkat Instansi -->
        <section class="space-y-space-md">
            <div class="border-b border-outline-variant/30 pb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-secondary">Lintas Sejarah</span>
                <h2 class="font-headline-lg text-2xl font-bold text-primary">Sejarah Singkat Instansi</h2>
            </div>
            <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-2xs border border-surface-container text-body-md text-on-surface-variant leading-relaxed space-y-3">
                <p>
                    {{ $siteSetting->institution_name ?? 'Kecamatan Loa Kulu' }} memiliki rekam jejak panjang dalam sejarah peradaban dan tata kelola di Kalimantan Timur. Sebagai salah satu simpul administratif strategis, wilayah ini tumbuh dari sentra perniagaan dan pertanian hingga menjadi pusat pelayanan administrasi publik terpadu yang modern.
                </p>
                <p>
                    Dengan semangat reformasi birokrasi dan amanat Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik, {{ $siteSetting->institution_name ?? 'pemerintah instansi' }} terus bertransformasi memberikan pelayanan prima, transparan, dan akuntabel bagi seluruh masyarakat.
                </p>
            </div>
        </section>

        <!-- 2. Data Geografis & Batas Wilayah -->
        <section class="space-y-space-md">
            <div class="border-b border-outline-variant/30 pb-2 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-secondary">Karakteristik Daerah</span>
                    <h2 class="font-headline-lg text-2xl font-bold text-primary">Gambaran Geografis &amp; Demografi</h2>
                </div>
            </div>

            <!-- 4 Indikator Spasial -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-space-md">
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-2xs border border-surface-container">
                    <span class="font-label-sm text-xs text-on-surface-variant uppercase tracking-wide">Luas Wilayah</span>
                    <div class="font-headline-lg text-2xl font-bold text-primary mt-1">1.405,7</div>
                    <span class="font-body-sm text-xs text-secondary font-medium">Kilometer Persegi</span>
                </div>
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-2xs border border-surface-container">
                    <span class="font-label-sm text-xs text-on-surface-variant uppercase tracking-wide">Desa &amp; Kelurahan</span>
                    <div class="font-headline-lg text-2xl font-bold text-primary mt-1">12 Desa</div>
                    <span class="font-body-sm text-xs text-secondary font-medium">Wilayah Administratif</span>
                </div>
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-2xs border border-surface-container">
                    <span class="font-label-sm text-xs text-on-surface-variant uppercase tracking-wide">Jumlah Penduduk</span>
                    <div class="font-headline-lg text-2xl font-bold text-primary mt-1">54.218</div>
                    <span class="font-body-sm text-xs text-secondary font-medium">Jiwa Terdaftar</span>
                </div>
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-2xs border border-surface-container">
                    <span class="font-label-sm text-xs text-on-surface-variant uppercase tracking-wide">Kepadatan Rata-rata</span>
                    <div class="font-headline-lg text-2xl font-bold text-primary mt-1">38,5</div>
                    <span class="font-body-sm text-xs text-secondary font-medium">Jiwa / km²</span>
                </div>
            </div>

            <!-- Batas Wilayah & Peta -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-md">
                <!-- Batas Administratif -->
                <div class="lg:col-span-7 bg-surface-container-lowest p-space-lg rounded-xl shadow-2xs border border-surface-container space-y-space-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-secondary text-[22px]">explore</span>
                        <h3 class="font-title-lg text-base font-bold text-primary">Batas Administratif Wilayah</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
                        <div class="p-3 rounded-lg bg-surface-container-low flex items-start gap-2 border border-surface-container">
                            <span class="w-6 h-6 rounded bg-primary text-on-primary flex items-center justify-center text-xs font-bold shrink-0">U</span>
                            <div>
                                <div class="text-xs font-bold text-primary">Batas Utara</div>
                                <div class="text-[11px] text-on-surface-variant">Kecamatan Tenggarong &amp; Koridor Sungai Mahakam</div>
                            </div>
                        </div>
                        <div class="p-3 rounded-lg bg-surface-container-low flex items-start gap-2 border border-surface-container">
                            <span class="w-6 h-6 rounded bg-primary text-on-primary flex items-center justify-center text-xs font-bold shrink-0">S</span>
                            <div>
                                <div class="text-xs font-bold text-primary">Batas Selatan</div>
                                <div class="text-[11px] text-on-surface-variant">Kabupaten Penajam Paser Utara (Kawasan IKN)</div>
                            </div>
                        </div>
                        <div class="p-3 rounded-lg bg-surface-container-low flex items-start gap-2 border border-surface-container">
                            <span class="w-6 h-6 rounded bg-primary text-on-primary flex items-center justify-center text-xs font-bold shrink-0">T</span>
                            <div>
                                <div class="text-xs font-bold text-primary">Batas Timur</div>
                                <div class="text-[11px] text-on-surface-variant">Kecamatan Loa Janan &amp; Kota Samarinda</div>
                            </div>
                        </div>
                        <div class="p-3 rounded-lg bg-surface-container-low flex items-start gap-2 border border-surface-container">
                            <span class="w-6 h-6 rounded bg-primary text-on-primary flex items-center justify-center text-xs font-bold shrink-0">B</span>
                            <div>
                                <div class="text-xs font-bold text-primary">Batas Barat</div>
                                <div class="text-[11px] text-on-surface-variant">Kecamatan Muara Muntai &amp; Kota Bangun</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Peta Geografis Visual -->
                <div class="lg:col-span-5 bg-surface-container-lowest p-space-md rounded-xl shadow-2xs border border-surface-container flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-surface-container">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-secondary text-[20px]">map</span>
                                <span class="text-xs font-bold text-primary">Koordinat Pemerintahan</span>
                            </div>
                            <span class="text-[11px] bg-surface-container-high px-2 py-0.5 rounded font-mono">0°34' LS, 117°01' BT</span>
                        </div>
                        <div class="h-48 rounded-lg bg-cover bg-center relative overflow-hidden shadow-inner border border-surface-container" style="background-image: url('{{ asset('images/peta-wilayah.jpg') }}')">
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-primary/20 to-transparent"></div>
                            <div class="absolute bottom-2 left-2 right-2 bg-surface-container-lowest/95 backdrop-blur-xs text-primary p-2 rounded-lg text-[11px] font-medium border border-outline-variant/30 flex items-center justify-between">
                                <span>Wilayah Pemerintahan Terpadu</span>
                                <span class="text-secondary font-bold">Loa Kulu, Kukar</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-[11px] text-on-surface-variant mt-2 italic">
                        Alamat: {{ $siteSetting->address ?? 'Jl. Protokol No. 1' }}
                    </p>
                </div>
            </div>
        </section>

        <!-- 3. Visi, Misi & Maklumat Pelayanan -->
        <section class="space-y-space-md">
            <div class="border-b border-outline-variant/30 pb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-secondary">Arah Kebijakan</span>
                <h2 class="font-headline-lg text-2xl font-bold text-primary">Visi & Misi Instansi</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
                <!-- Visi -->
                <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-2xs border border-surface-container space-y-3">
                    <span class="font-label-sm text-xs font-bold uppercase text-secondary bg-secondary-container/30 px-2 py-1 rounded">
                        VISI UTAMA
                    </span>
                    <blockquote class="font-headline-md text-lg sm:text-xl font-bold text-primary italic leading-snug">
                        “Terwujudnya Masyarakat {{ $siteSetting->institution_acronym ?? 'Instansi' }} yang Sejahtera, Mandiri, dan Berakhlak Mulia Melalui Pelayanan Publik Terpadu yang Inklusif.”
                    </blockquote>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Merujuk pada Rencana Pembangunan Jangka Menengah Daerah (RPJMD) dan standar operasional pelayanan keterbukaan informasi publik.
                    </p>
                </div>

                <!-- Misi -->
                <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-2xs border border-surface-container space-y-3">
                    <span class="font-label-sm text-xs font-bold uppercase text-primary bg-surface-container-high px-2 py-1 rounded">
                        4 MISI STRATEGIS
                    </span>
                    <ol class="space-y-2 text-xs text-on-surface-variant list-decimal list-inside leading-relaxed">
                        <li>Meningkatkan kualitas tata kelola pelayanan publik berbasis transparansi dan keterbukaan informasi (UU No. 14/2008).</li>
                        <li>Mempercepat digitalisasi administrasi kependudukan dan perizinan mikro tingkat lokal.</li>
                        <li>Mendorong sinergi pembangunan infrastruktur antar wilayah dan peningkatan ekonomi kerakyatan.</li>
                        <li>Memelihara ketenteraman, ketertiban umum, dan pelestarian kearifan lokal masyarakat.</li>
                    </ol>
                </div>
            </div>
        </section>

        <!-- 4. Struktur Organisasi & Jajaran Pejabat -->
        <section class="space-y-space-md" id="pejabat">
            <div class="border-b border-outline-variant/30 pb-2 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-secondary">Aparatur Sipil Negara</span>
                    <h2 class="font-headline-lg text-2xl font-bold text-primary">Profil Pejabat Struktural</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md">
                @forelse($pejabat as $p)
                    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-xs border border-surface-container text-center flex flex-col items-center hover:shadow-md hover:border-secondary/40 transition-all">
                        <div class="w-28 h-28 rounded-full overflow-hidden mb-3 border-2 border-secondary shadow-sm bg-surface-container-high">
                            <img 
                                src="{{ !empty($p['foto']) ? (str_starts_with($p['foto'], 'images/') || str_starts_with($p['foto'], 'http') ? asset($p['foto']) : asset('storage/' . $p['foto'])) : asset('images/pejabat-camat.jpg') }}" 
                                alt="Foto {{ $p['nama'] }}" 
                                class="w-full h-full object-cover object-top hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                            />
                        </div>
                        <h3 class="font-title-lg text-sm font-bold text-primary leading-tight">{{ $p['nama'] }}</h3>
                        <p class="font-label-sm text-xs font-semibold text-secondary mt-1">{{ $p['jabatan'] }}</p>
                        @if(!empty($p['nip']))
                            <p class="text-[11px] text-on-surface-variant mt-1 font-mono">NIP: {{ $p['nip'] }}</p>
                        @endif
                        @if(!empty($p['pendidikan']))
                            <p class="text-[10px] text-on-surface-variant/80 mt-1 line-clamp-1" title="{{ $p['pendidikan'] }}">{{ $p['pendidikan'] }}</p>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full p-8 text-center bg-surface-container-low rounded-xl">
                        <p class="text-on-surface-variant text-sm">Data aparatur sedang diperbarui.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
