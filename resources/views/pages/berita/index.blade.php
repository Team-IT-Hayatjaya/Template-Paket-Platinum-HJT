@extends('layouts.app')

@section('title', 'Berita & Siaran Pers Resmi - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))

@section('content')
<div class="space-y-space-xl">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Publikasi', 'url' => route('publikasi')],
        ['label' => 'Berita & Siaran Pers'],
    ]" />

    <!-- Header Halaman Berita -->
    <section class="bg-surface-container-lowest p-space-lg lg:p-space-xl rounded-2xl shadow-2xs border border-surface-container flex flex-col md:flex-row md:items-end justify-between gap-space-lg">
        <div class="space-y-space-xs max-w-2xl">
            <div class="inline-flex items-center gap-space-xs bg-surface-container px-space-sm py-space-xs rounded-full text-secondary font-label-sm text-label-sm font-bold uppercase tracking-wider">
                <span class="material-symbols-outlined text-[16px]">newspaper</span>
                <span>Pusat Informasi & Publikasi</span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-primary tracking-tight">
                Berita & Siaran Pers Resmi
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                Kumpulan rilis berita kedinasan, agenda pembangunan, pengumuman publik, serta liputan kegiatan terverifikasi {{ $siteSetting->institution_name ?? 'Pemerintah Instansi' }}.
            </p>
        </div>

        <!-- Formulir Pencarian Berita -->
        <form action="{{ route('berita.index') }}" method="GET" class="w-full md:w-80 relative" role="search">
            <label for="search-news" class="sr-only">Cari judul berita</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-[20px] text-on-surface-variant">search</span>
                <input type="search" 
                       id="search-news" 
                       name="q" 
                       value="{{ $q ?? '' }}" 
                       placeholder="Cari topik berita..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-surface-container bg-surface-container-low text-on-surface font-body-sm text-body-sm focus:outline-none focus:ring-2 focus:ring-secondary/50 shadow-2xs transition">
            </div>
        </form>
    </section>

    <!-- Filter Tab Kategori -->
    <div class="flex items-center gap-space-xs overflow-x-auto pb-2 border-b-0">
        <a href="{{ route('berita.index') }}" 
           class="px-space-md py-space-xs rounded-lg font-label-md text-label-md whitespace-nowrap transition {{ empty($kategori) ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container-lowest hover:bg-surface-container text-on-surface-variant border border-surface-container' }}">
            Semua Berita
        </a>
        @foreach($kategoriList as $kat)
            <a href="{{ route('berita.index', ['kategori' => $kat]) }}" 
               class="px-space-md py-space-xs rounded-lg font-label-md text-label-md whitespace-nowrap transition {{ (strtolower($kategori ?? '') === strtolower($kat)) ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container-lowest hover:bg-surface-container text-on-surface-variant border border-surface-container' }}">
                {{ $kat }}
            </a>
        @endforeach
    </div>

    <!-- Grid Berita -->
    @if(count($daftarBerita) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-space-lg">
            @foreach($daftarBerita as $item)
                <x-news-card 
                    :title="$item['judul']"
                    :date="$item['tanggal']"
                    :category="$item['kategori']"
                    :excerpt="$item['ringkasan']"
                    :image="$item['gambar'] ?? null"
                    :url="route('berita.detail', $item['slug'])" />
            @endforeach
        </div>

        <!-- Paginasi -->
        <x-pagination :currentPage="1" :totalPages="1" baseUrl="{{ route('berita.index') }}" />
    @else
        <div class="bg-surface-container-lowest rounded-2xl border border-surface-container p-12 text-center shadow-2xs">
            <span class="material-symbols-outlined text-[48px] text-on-surface-variant mb-2 block">search_off</span>
            <h3 class="font-title-lg text-title-lg text-primary font-bold mb-1">Berita Tidak Ditemukan</h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant max-w-md mx-auto mb-4">
                Tidak ada berita yang cocok dengan kata kunci "{{ $q ?? '' }}" atau kategori yang dipilih.
            </p>
            <a href="{{ route('berita.index') }}" class="inline-flex items-center gap-1.5 px-space-md py-2 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container transition">
                <span>Reset Pencarian & Kategori</span>
            </a>
        </div>
    @endif
</div>
@endsection
