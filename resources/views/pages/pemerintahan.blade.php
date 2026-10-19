@extends('layouts.app')

@section('title', 'Pemerintahan & Tata Kelola Wilayah - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))
@section('meta_title', 'Struktur Pemerintahan & Kebijakan Instansi Resmi')
@section('meta_description', 'Tata kelola pemerintahan daerah, susunan pimpinan, unit kerja pengampu, dasar hukum, dan rencana strategis pembangunan.')

@section('content')
<div class="space-y-space-xl max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Pemerintahan'],
    ]" />

    <!-- Hero Header -->
    <section class="bg-surface-container-lowest p-space-lg lg:p-space-xl rounded-2xl shadow-2xs border border-surface-container space-y-space-md">
        <div class="inline-flex items-center gap-space-xs bg-surface-container px-space-sm py-space-xs rounded-full text-secondary font-label-sm text-label-sm font-bold uppercase tracking-wider">
            <span class="material-symbols-outlined text-[16px]">account_balance</span>
            <span>Tata Kelola Pemerintahan Daerah</span>
        </div>

        <h1 class="font-headline-xl text-headline-xl text-primary font-bold tracking-tight">
            Pemerintahan &amp; Kebijakan Wilayah
        </h1>

        <p class="font-body-lg text-body-lg text-on-surface-variant max-w-3xl leading-relaxed">
            Penyelenggaraan urusan pemerintahan umum, pembinaan ketenteraman, koordinasi pembangunan desa/kelurahan, dan pelayanan prima kepada seluruh elemen masyarakat {{ $siteSetting->institution_name ?? 'Pemerintah Instansi' }}.
        </p>
    </section>

    <!-- 4 PILAR PEMERINTAHAN (SUBMENU GRID) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-space-lg">
        <!-- 1. Profil Pejabat -->
        <div class="bg-surface-container-lowest p-space-lg rounded-2xl border border-surface-container shadow-2xs hover:shadow-md transition flex flex-col justify-between space-y-space-md">
            <div class="space-y-space-xs">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined text-[28px]">badge</span>
                </div>
                <h3 class="font-title-lg text-lg font-bold text-primary">Profil Pejabat &amp; Pimpinan</h3>
                <p class="text-sm text-on-surface-variant leading-relaxed">
                    Susunan pimpinan wilayah, data riwayat pejabat struktural, dan tugas pembagian kewenangan pimpinan instansi.
                </p>
            </div>
            <div class="pt-2">
                <a href="{{ route('halaman.show', 'profil-pejabat') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-secondary hover:underline">
                    <span>Buka Profil Pejabat</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- 2. Unit Kerja -->
        <div class="bg-surface-container-lowest p-space-lg rounded-2xl border border-surface-container shadow-2xs hover:shadow-md transition flex flex-col justify-between space-y-space-md">
            <div class="space-y-space-xs">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined text-[28px]">apartment</span>
                </div>
                <h3 class="font-title-lg text-lg font-bold text-primary">Unit Kerja &amp; Seksi Wilayah</h3>
                <p class="text-sm text-on-surface-variant leading-relaxed">
                    Struktur seksi pelayanan, sub-bagian ketatausahaan, dan pembagian lingkup kerja teknis perangkat daerah.
                </p>
            </div>
            <div class="pt-2">
                <a href="{{ route('halaman.show', 'unit-kerja') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-secondary hover:underline">
                    <span>Lihat Daftar Unit Kerja</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- 3. Dasar Hukum -->
        <div class="bg-surface-container-lowest p-space-lg rounded-2xl border border-surface-container shadow-2xs hover:shadow-md transition flex flex-col justify-between space-y-space-md">
            <div class="space-y-space-xs">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined text-[28px]">gavel</span>
                </div>
                <h3 class="font-title-lg text-lg font-bold text-primary">Dasar Hukum &amp; Regulasi</h3>
                <p class="text-sm text-on-surface-variant leading-relaxed">
                    Landasan undang-undang, peraturan pemerintah, dan peraturan daerah yang menjadi payung hukum pelayanan publik.
                </p>
            </div>
            <div class="pt-2">
                <a href="{{ route('halaman.show', 'dasar-hukum') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-secondary hover:underline">
                    <span>Baca Dasar Hukum</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- 4. Rencana Strategis -->
        <div class="bg-surface-container-lowest p-space-lg rounded-2xl border border-surface-container shadow-2xs hover:shadow-md transition flex flex-col justify-between space-y-space-md">
            <div class="space-y-space-xs">
                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined text-[28px]">flag</span>
                </div>
                <h3 class="font-title-lg text-lg font-bold text-primary">Rencana Strategis (Renstra)</h3>
                <p class="text-sm text-on-surface-variant leading-relaxed">
                    Arah target pembangunan lima tahunan, sasaran kinerja utama instansi, dan indikator keberhasilan program.
                </p>
            </div>
            <div class="pt-2">
                <a href="{{ route('halaman.show', 'rencana-strategis') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-secondary hover:underline">
                    <span>Akses Rencana Strategis</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
