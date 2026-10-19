@extends('layouts.app')

@section('title', $layanan->nama . ' - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))
@section('meta_title', 'Standar Pelayanan: ' . $layanan->nama)
@section('meta_description', Str::limit(strip_tags($layanan->ringkasan ?: $layanan->persyaratan), 160))

@section('content')
<div class="space-y-space-xl max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Layanan Publik', 'url' => route('layanan')],
        ['label' => $layanan->nama],
    ]" />

    <!-- Header Layanan -->
    <section class="bg-surface-container-lowest p-space-lg lg:p-space-xl rounded-2xl shadow-2xs border border-surface-container space-y-space-md">
        <div class="inline-flex items-center gap-space-xs bg-surface-container px-space-sm py-space-xs rounded-full text-secondary font-label-sm text-label-sm font-bold uppercase tracking-wider">
            <span class="material-symbols-outlined text-[16px]">verified</span>
            <span>Standar Pelayanan Publik Terpadu (PATEN)</span>
        </div>

        <h1 class="font-headline-xl text-headline-xl text-primary font-bold tracking-tight">
            {{ $layanan->nama }}
        </h1>

        @if($layanan->ringkasan)
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-3xl leading-relaxed">
                {{ $layanan->ringkasan }}
            </p>
        @endif

        <!-- KPI Ketentuan Layanan (Biaya, Waktu, Penanggung Jawab) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md pt-space-xs">
            <div class="bg-surface-container-low p-space-md rounded-xl border border-surface-container">
                <div class="flex items-center gap-2 text-xs text-on-surface-variant font-bold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[18px] text-emerald-600">payments</span>
                    <span>Biaya / Retribusi</span>
                </div>
                <div class="font-headline-md text-base sm:text-lg font-bold text-emerald-700 mt-1">
                    {{ $layanan->biaya }}
                </div>
            </div>

            <div class="bg-surface-container-low p-space-md rounded-xl border border-surface-container">
                <div class="flex items-center gap-2 text-xs text-on-surface-variant font-bold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[18px] text-primary">schedule</span>
                    <span>Waktu Penyelesaian</span>
                </div>
                <div class="font-headline-md text-base sm:text-lg font-bold text-primary mt-1">
                    {{ $layanan->waktu_penyelesaian }}
                </div>
            </div>

            <div class="bg-surface-container-low p-space-md rounded-xl border border-surface-container">
                <div class="flex items-center gap-2 text-xs text-on-surface-variant font-bold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[18px] text-secondary">badge</span>
                    <span>Unit Pengampu</span>
                </div>
                <div class="font-title-sm text-xs sm:text-sm font-bold text-primary mt-1 line-clamp-2">
                    {{ $layanan->penanggung_jawab }}
                </div>
            </div>
        </div>
    </section>

    <!-- Konten Utama: Persyaratan & Prosedur -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
        <div class="lg:col-span-8 space-y-space-lg">
            <!-- 1. Persyaratan Berkas -->
            <div class="bg-surface-container-lowest p-space-lg rounded-2xl border border-surface-container shadow-2xs space-y-space-md">
                <h2 class="font-title-lg text-lg font-bold text-primary flex items-center gap-2 border-b border-surface-container pb-3">
                    <span class="material-symbols-outlined text-secondary text-[22px]">checklist</span>
                    <span>Persyaratan Berkas &amp; Dokumen</span>
                </h2>
                <div class="prose max-w-none text-on-surface-variant text-sm leading-relaxed">
                    {!! $layanan->persyaratan !!}
                </div>
            </div>

            <!-- 2. Alur / Mekanisme Prosedur -->
            <div class="bg-surface-container-lowest p-space-lg rounded-2xl border border-surface-container shadow-2xs space-y-space-md">
                <h2 class="font-title-lg text-lg font-bold text-primary flex items-center gap-2 border-b border-surface-container pb-3">
                    <span class="material-symbols-outlined text-secondary text-[22px]">account_tree</span>
                    <span>Alur &amp; Prosedur Pengurusan</span>
                </h2>
                <div class="prose max-w-none text-on-surface-variant text-sm leading-relaxed">
                    {!! $layanan->alur !!}
                </div>
            </div>

            <!-- 3. Formulir Unduhan (Jika ada) -->
            @if(!empty($layanan->formulir_file))
                <div class="bg-primary/5 border border-primary/20 p-space-lg rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[32px] text-primary shrink-0">description</span>
                        <div>
                            <h4 class="font-bold text-primary text-sm sm:text-base">Formulir Pendaftaran Resmi (PDF)</h4>
                            <p class="text-xs text-on-surface-variant mt-0.5">Unduh dan isi formulir permohonan sebelum datang ke loket pelayanan.</p>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $layanan->formulir_file) }}" target="_blank" download class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-on-primary font-bold text-xs shadow-xs hover:bg-primary-container transition shrink-0">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Unduh Formulir</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Sidebar Kolom Kanan -->
        <div class="lg:col-span-4 space-y-space-md">
            <!-- Info Loket & Jam Kerja -->
            <div class="bg-surface-container-lowest p-space-md rounded-2xl border border-surface-container shadow-2xs space-y-space-sm">
                <h3 class="font-title-md text-sm font-bold text-primary border-b border-surface-container pb-2 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-secondary">storefront</span>
                    <span>Loket Pelayanan Langsung</span>
                </h3>
                <div class="space-y-2 text-xs text-on-surface-variant">
                    <p class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-[16px] text-primary shrink-0">location_on</span>
                        <span>{{ $siteSetting->address ?? 'Loket PATEN Kantor Camat' }}</span>
                    </p>
                    <p class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-[16px] text-primary shrink-0">schedule</span>
                        <span>{!! nl2br(e($siteSetting->service_hours ?? "Senin - Kamis: 08.00 - 16.00 WITA\nJumat: 08.00 - 15.30 WITA")) !!}</span>
                    </p>
                    @if(!empty($siteSetting->phone))
                        <p class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-primary shrink-0">call</span>
                            <span>Telp: {{ $siteSetting->phone }}</span>
                        </p>
                    @endif
                </div>

                <div class="pt-2">
                    <a href="{{ route('kontak') }}" class="w-full text-center block py-2 rounded-xl bg-surface-container text-primary font-bold text-xs hover:bg-surface-container-high transition">
                        Butuh Bantuan? Hubungi Kami
                    </a>
                </div>
            </div>

            <!-- Layanan Terkait Lainnya -->
            @if(isset($layananLain) && $layananLain->isNotEmpty())
                <div class="bg-surface-container-lowest p-space-md rounded-2xl border border-surface-container shadow-2xs space-y-space-sm">
                    <h3 class="font-title-md text-sm font-bold text-primary border-b border-surface-container pb-2">
                        Layanan Publik Lainnya
                    </h3>
                    <div class="divide-y divide-surface-container">
                        @foreach($layananLain as $item)
                            <a href="{{ route('layanan.detail', $item->slug) }}" class="block py-2.5 hover:bg-surface-container-low transition rounded-lg px-2 group">
                                <div class="text-xs font-bold text-primary group-hover:text-secondary transition-colors">{{ $item->nama }}</div>
                                <div class="text-[11px] text-on-surface-variant flex items-center gap-2 mt-0.5">
                                    <span>{{ $item->waktu_penyelesaian }}</span>
                                    <span>&bull;</span>
                                    <span class="text-emerald-700 font-semibold">{{ $item->biaya }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
