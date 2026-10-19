@extends('layouts.app')

@section('title', 'Galeri Kegiatan & Pelayanan Publik - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))

@section('content')
<div class="space-y-space-xl">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Publikasi', 'url' => route('publikasi')],
        ['label' => 'Galeri Foto & Dokumentasi'],
    ]" />

    <!-- Header Halaman Galeri -->
    <section class="bg-surface-container-lowest p-space-lg lg:p-space-xl rounded-2xl shadow-2xs border border-surface-container flex flex-col md:flex-row md:items-end justify-between gap-space-lg">
        <div class="space-y-space-xs max-w-3xl">
            <div class="inline-flex items-center gap-space-xs bg-surface-container px-space-sm py-space-xs rounded-full text-secondary font-label-sm text-label-sm font-bold uppercase tracking-wider">
                <span class="material-symbols-outlined text-[16px]">photo_camera</span>
                <span>Dokumentasi Foto Kedinasan</span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-primary tracking-tight">
                Galeri Kegiatan & Pelayanan Publik
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                Dokumentasi visual rangkaian kegiatan kedinasan, sosialisasi keterbukaan informasi publik, pembinaan aparatur, dan pelayanan langsung kepada warga {{ $siteSetting->institution_name ?? 'Pemerintah Instansi' }}.
            </p>
        </div>
        <div class="font-label-sm text-label-sm text-on-surface-variant bg-surface-container-low px-space-md py-space-sm rounded-xl border border-surface-container">
            Total Dokumentasi: <strong>{{ count($galeri) }} Album</strong>
        </div>
    </section>

    <!-- Grid Foto Dokumentasi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-space-lg">
        @foreach($galeri as $item)
            <div class="bg-surface-container-lowest rounded-2xl border border-surface-container overflow-hidden shadow-2xs hover:shadow-md transition-shadow group flex flex-col justify-between">
                <div class="aspect-4/3 w-full bg-linear-to-br from-primary to-primary-container relative overflow-hidden group-hover:scale-[1.01] transition-transform">
                    @if(!empty($item['gambar']))
                        <img src="{{ $item['gambar'] }}" alt="{{ $item['judul'] }}" class="w-full h-full object-cover" loading="lazy" />
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-on-primary p-6 text-center">
                            <span class="material-symbols-outlined text-[48px] text-secondary-fixed mb-2 select-none">photo_library</span>
                            <span class="font-label-sm text-label-sm text-surface-container-high font-semibold">Dokumentasi Foto</span>
                        </div>
                    @endif
                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded text-[11px] font-bold text-primary bg-secondary-fixed shadow-2xs">
                        {{ $item['kategori'] }}
                    </span>
                    @if(!empty($item['tipe']) && $item['tipe'] === 'infografis')
                        <span class="absolute top-3 right-3 px-2 py-0.5 rounded text-[10px] font-bold text-white bg-amber-600 shadow-2xs">
                            Infografis
                        </span>
                    @endif
                </div>
                <div class="p-space-md sm:p-space-lg flex-1 flex flex-col justify-between space-y-space-sm">
                    <div>
                        <div class="flex items-center gap-1 text-secondary font-label-sm text-[12px] font-semibold mb-1">
                            <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                            <span>{{ $item['tanggal'] }}</span>
                        </div>
                        <h3 class="font-title-lg text-[16px] font-bold text-primary leading-snug">
                            {{ $item['judul'] }}
                        </h3>
                    </div>
                    <div class="pt-space-sm border-t border-surface-container flex items-center justify-between text-body-sm font-body-sm text-on-surface-variant">
                        <span>Humas & Dokumentasi Foto</span>
                        <span class="text-secondary font-bold text-label-sm">Tersimpan di Arsip</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
