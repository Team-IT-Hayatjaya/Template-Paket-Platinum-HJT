@extends('layouts.app')

@section('title', ($halaman->meta_title ?: $halaman->judul) . ' - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))
@section('meta_title', $halaman->meta_title ?: $halaman->judul)
@section('meta_description', $halaman->meta_description ?: ($halaman->ringkasan ?: Str::limit(strip_tags($halaman->isi), 160)))

@if($halaman->gambar_utama)
    @section('og_image', asset('storage/' . $halaman->gambar_utama))
@endif

@section('content')
<div class="space-y-space-xl max-w-5xl mx-auto">
    @php
        $navItem = \App\Models\NavigationMenu::where('url', '/halaman/' . $halaman->slug)->first();
        $parentMenu = $navItem?->parent;
        $parentLabel = $parentMenu ? $parentMenu->nama : 'Profil Instansi';
        $parentUrl = $parentMenu ? url($parentMenu->url) : route('profil');
    @endphp
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => $parentLabel, 'url' => $parentUrl],
        ['label' => $halaman->judul],
    ]" />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
        <!-- Main Article Content -->
        <article class="lg:col-span-8 bg-surface-container-lowest rounded-2xl border border-surface-container p-space-lg sm:p-space-xl shadow-2xs space-y-space-lg">
            <header class="space-y-space-sm border-b border-surface-container pb-space-lg">
                <div class="inline-flex items-center gap-space-xs bg-surface-container px-space-sm py-space-xs rounded-full text-secondary font-label-sm text-label-sm font-bold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[16px]">account_balance</span>
                    <span>Informasi Resmi Instansi</span>
                </div>

                <h1 class="font-headline-xl text-3xl sm:text-4xl text-primary font-bold tracking-tight leading-tight">
                    {{ $halaman->judul }}
                </h1>

                <div class="flex items-center gap-4 text-xs text-on-surface-variant font-medium pt-1">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-secondary">update</span>
                        Diperbarui: {{ $halaman->updated_at->translatedFormat('d F Y') }}
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-secondary">verified</span>
                        {{ $siteSetting->institution_name ?? 'Pemerintah Instansi' }}
                    </span>
                </div>
            </header>

            @if($halaman->gambar_utama)
                <div class="rounded-xl overflow-hidden shadow-xs border border-surface-container max-h-[420px]">
                    <img src="{{ asset('storage/' . $halaman->gambar_utama) }}" alt="{{ $halaman->judul }}" class="w-full h-full object-cover" />
                </div>
            @endif

            @if($halaman->ringkasan)
                <div class="p-space-md sm:p-space-lg bg-surface-container-low rounded-xl border-l-4 border-secondary font-headline-md text-base sm:text-lg text-primary italic leading-relaxed">
                    {{ $halaman->ringkasan }}
                </div>
            @endif

            @if(!empty($halaman->berkas_pdf))
                <!-- Toolbar Aksi Berkas PDF -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-surface-container-low p-3.5 rounded-xl border border-surface-container">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-primary text-white flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                        </span>
                        <div>
                            <span class="text-xs font-bold text-primary block">Dokumen Resmi Digital</span>
                            <span class="text-[11px] text-on-surface-variant">Tersedia untuk dibaca langsung dan diunduh</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ asset('storage/' . $halaman->berkas_pdf) }}" 
                           target="_blank" 
                           download 
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-primary text-on-primary hover:bg-primary-container text-xs font-bold shadow-xs transition">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            <span>Unduh Dokumen PDF</span>
                        </a>
                        <a href="{{ asset('storage/' . $halaman->berkas_pdf) }}" 
                           target="_blank" 
                           rel="noopener"
                           class="inline-flex items-center gap-1 px-3 py-2 rounded-lg bg-surface-container-high hover:bg-surface-container text-primary text-xs font-bold transition border border-surface-container">
                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                            <span class="hidden sm:inline">Buka Tab Penuh</span>
                        </a>
                    </div>
                </div>

                <!-- Penampil Isi PDF Interaktif (Embedded PDF Viewer) -->
                <div class="rounded-xl overflow-hidden border-2 border-surface-container shadow-md bg-slate-100">
                    <object data="{{ asset('storage/' . $halaman->berkas_pdf) }}#toolbar=1&navpanes=0&view=FitH" 
                            type="application/pdf" 
                            class="w-full h-[750px] sm:h-[880px] rounded-xl">
                        <iframe src="{{ asset('storage/' . $halaman->berkas_pdf) }}#toolbar=1&navpanes=0&view=FitH" 
                                class="w-full h-[750px] sm:h-[880px] border-0 rounded-xl"
                                title="{{ $halaman->judul }}">
                            <div class="p-6 text-center text-xs text-on-surface-variant">
                                Penampil PDF tidak didukung pada peramban ini. <a href="{{ asset('storage/' . $halaman->berkas_pdf) }}" download class="text-primary font-bold underline">Klik di sini untuk mengunduh berkas PDF</a>.
                            </div>
                        </iframe>
                    </object>
                </div>
            @endif

            <!-- Body Text / Rich HTML -->
            <div class="prose prose-slate max-w-none text-on-surface leading-relaxed space-y-4 font-body-lg text-body-lg">
                {!! $halaman->isi !!}
            </div>
        </article>

        <!-- Sidebar Navigation to Other Pages -->
        <aside class="lg:col-span-4 space-y-space-md">
            @if(isset($halamanLain) && $halamanLain->isNotEmpty())
                <div class="bg-surface-container-lowest p-space-md rounded-2xl border border-surface-container shadow-2xs space-y-space-sm">
                    <h3 class="font-headline-md text-base font-bold text-primary flex items-center gap-2 border-b border-surface-container pb-2">
                        <span class="material-symbols-outlined text-[20px] text-secondary">menu_book</span>
                        <span>Menu Halaman Terkait</span>
                    </h3>
                    <ul class="space-y-1">
                        @foreach($halamanLain as $item)
                            <li>
                                <a href="{{ route('halaman.show', $item->slug) }}" class="flex items-center justify-between p-2 rounded-lg text-sm font-medium transition {{ $item->id === $halaman->id ? 'bg-primary text-on-primary font-bold' : 'text-on-surface hover:bg-surface-container-low' }}">
                                    <span>{{ $item->judul }}</span>
                                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Box Bantuan & PPID -->
            <div class="bg-primary text-on-primary p-space-lg rounded-2xl shadow-xs space-y-space-sm">
                <span class="material-symbols-outlined text-secondary-fixed text-[32px]">shield</span>
                <h4 class="font-headline-md text-lg font-bold text-on-primary">Layanan Keterbukaan Informasi</h4>
                <p class="text-xs text-on-primary-container leading-relaxed">
                    Perlu salinan dokumen atau klarifikasi lebih lanjut mengenai informasi di halaman ini? Hubungi Pejabat Pengelola Informasi dan Dokumentasi (PPID).
                </p>
                <div class="pt-2">
                    <a href="{{ route('publikasi') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-secondary text-on-secondary text-xs font-bold hover:brightness-105 transition">
                        <span>Daftar Informasi Publik</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
