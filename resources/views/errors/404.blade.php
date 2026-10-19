@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan (404) - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))

@section('content')
<div class="min-h-[60vh] flex items-center justify-center py-space-xl">
    <div class="max-w-xl w-full bg-surface-container-lowest rounded-2xl border border-surface-container p-space-lg sm:p-space-xl text-center shadow-2xs space-y-space-md">
        <div class="w-20 h-20 mx-auto rounded-full bg-surface-container flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-[44px] text-secondary">wrong_location</span>
        </div>

        <div class="space-y-space-xs">
            <span class="inline-block px-3 py-1 rounded-full font-label-sm text-label-sm font-bold bg-secondary-fixed text-primary uppercase tracking-wider">
                Kesalahan 404
            </span>
            <h1 class="font-headline-xl text-headline-xl text-primary font-bold tracking-tight">
                Halaman Tidak Ditemukan
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                Mohon maaf, halaman atau berkas dokumen yang Anda cari tidak dapat ditemukan pada server {{ $siteSetting->institution_name ?? 'portal resmi' }}. Tautan mungkin telah diperbarui atau alamat tidak lengkap.
            </p>
        </div>

        <div class="pt-space-sm flex flex-col sm:flex-row items-center justify-center gap-space-xs">
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-space-lg py-2.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md font-bold transition flex items-center justify-center gap-2 shadow-xs">
                <span class="material-symbols-outlined text-[18px]">home</span>
                <span>Kembali ke Beranda</span>
            </a>
            <a href="{{ route('layanan') }}" class="w-full sm:w-auto px-space-lg py-2.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-primary font-label-md text-label-md transition flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">support</span>
                <span>Daftar Layanan Publik</span>
            </a>
        </div>

        <div class="pt-space-md border-t border-surface-container text-body-sm font-body-sm text-on-surface-variant">
            Butuh bantuan informasi kedinasan? Hubungi Helpdesk di <strong class="text-primary">{{ $siteSetting->phone ?? '(0541) 661001' }}</strong>.
        </div>
    </div>
</div>
@endsection
