@extends('layouts.app')

@section('title', 'Daftar Informasi Publik (PPID) - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))
@section('meta_title', 'Daftar Informasi Publik (DIP) PPID Sesuai UU No. 14 Tahun 2008')
@section('meta_description', 'Akses Daftar Informasi Publik resmi: Informasi Berkala, Serta Merta, Setiap Saat, dan Dikecualikan.')

@section('content')
<div class="space-y-space-xl max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Informasi Publik / PPID', 'url' => route('ppid.daftar-informasi')],
        ['label' => 'Daftar Informasi Publik (DIP)'],
    ]" />

    <!-- Header Section -->
    <section class="bg-surface-container-lowest p-space-lg lg:p-space-xl rounded-2xl shadow-2xs border border-surface-container flex flex-col md:flex-row md:items-end justify-between gap-space-lg">
        <div class="space-y-space-xs max-w-3xl">
            <div class="inline-flex items-center gap-space-xs bg-surface-container px-space-sm py-space-xs rounded-full text-secondary font-label-sm text-label-sm font-bold uppercase tracking-wider">
                <span class="material-symbols-outlined text-[16px]">verified</span>
                <span>UU No. 14 Tahun 2008 Tentang Keterbukaan Informasi Publik</span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-primary font-bold tracking-tight">
                Daftar Informasi Publik (DIP)
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                Pejabat Pengelola Informasi dan Dokumentasi (PPID) {{ $siteSetting->institution_name ?? 'Pemerintah Instansi' }} menyediakan dokumen publik dalam 4 (empat) klasifikasi resmi sesuai amanat undang-undang.
            </p>
        </div>

        <div class="flex items-center gap-space-sm shrink-0 flex-wrap">
            <a href="{{ route('ppid.permohonan') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-on-primary font-semibold text-sm shadow-xs hover:bg-primary-container transition">
                <span class="material-symbols-outlined text-[18px]">edit_document</span>
                <span>Ajukan Permohonan Informasi</span>
            </a>
            @if(\App\Support\Package::has('ppid_tracking'))
                <a href="{{ route('ppid.lacak') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container-high hover:bg-surface-container text-primary font-semibold text-sm border border-surface-container transition">
                    <span class="material-symbols-outlined text-[18px]">travel_explore</span>
                    <span>Lacak Resi Online</span>
                </a>
            @endif
        </div>
    </section>

    <!-- 3 Kartu Menu Cepat: Profil Pejabat, Laporan Keuangan, Rencana Strategis -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
        <!-- 1. Profil Pejabat -->
        <div class="bg-surface-container-lowest p-space-md rounded-xl border border-surface-container shadow-2xs flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">badge</span>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-title-md text-sm font-bold text-primary truncate">Profil Pejabat Pengelola</h4>
                <p class="text-xs text-on-surface-variant line-clamp-2 mt-0.5">Susunan pimpinan, atasan PPID, dan profil pejabat badan publik.</p>
                <a href="{{ route('halaman.show', 'profil-pejabat') }}" class="inline-flex items-center gap-1 text-xs font-bold text-secondary mt-2 hover:underline">
                    <span>Lihat Profil Pejabat</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- 2. Laporan Keuangan -->
        <div class="bg-surface-container-lowest p-space-md rounded-xl border border-surface-container shadow-2xs flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">account_balance_wallet</span>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-title-md text-sm font-bold text-primary truncate">Laporan Keuangan & DPA</h4>
                <p class="text-xs text-on-surface-variant line-clamp-2 mt-0.5">Transparansi neraca, realisasi anggaran, dan ringkasan audit BPK.</p>
                <a href="{{ route('halaman.show', 'laporan-keuangan') }}" class="inline-flex items-center gap-1 text-xs font-bold text-secondary mt-2 hover:underline">
                    <span>Lihat Laporan Keuangan</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- 3. Rencana Strategis -->
        <div class="bg-surface-container-lowest p-space-md rounded-xl border border-surface-container shadow-2xs flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">flag</span>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-title-md text-sm font-bold text-primary truncate">Rencana Strategis (Renstra)</h4>
                <p class="text-xs text-on-surface-variant line-clamp-2 mt-0.5">Dokumen arah pembangunan, target kinerja, dan indikator utama.</p>
                <a href="{{ route('halaman.show', 'rencana-strategis') }}" class="inline-flex items-center gap-1 text-xs font-bold text-secondary mt-2 hover:underline">
                    <span>Lihat Rencana Strategis</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 KLASIFIKASI INFORMASI PUBLIK (TABS & FILTER) -->
    <section class="bg-surface-container-lowest p-space-md lg:p-space-lg rounded-2xl border border-surface-container shadow-2xs space-y-space-md">
        <!-- Klasifikasi Navigasi Tabs -->
        <div class="border-b border-surface-container pb-3 flex flex-col md:flex-row md:items-center justify-between gap-space-sm">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 max-w-full">
                <a href="{{ route('ppid.daftar-informasi', array_merge(request()->except('klasifikasi', 'page'), ['klasifikasi' => 'semua'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition {{ ($kategoriSlug === 'semua' || empty($kategoriSlug)) ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}">
                    Semua Klasifikasi
                </a>
                @foreach($kategoriList as $kat)
                    <a href="{{ route('ppid.daftar-informasi', array_merge(request()->except('klasifikasi', 'page'), ['klasifikasi' => $kat->slug])) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition {{ ($kategoriSlug === $kat->slug) ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}">
                        {{ $kat->nama }}
                    </a>
                @endforeach
            </div>

            <!-- Filter Tahun & Pencarian -->
            <form action="{{ route('ppid.daftar-informasi') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="klasifikasi" value="{{ $kategoriSlug }}">
                
                <select name="tahun" onchange="this.form.submit()" class="bg-surface-container text-xs rounded-lg px-2.5 py-1.5 border border-surface-container focus:outline-none">
                    <option value="">Semua Tahun</option>
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ ($tahun == $y) ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>

                <div class="relative">
                    <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari nama dokumen..." class="bg-surface-container text-xs rounded-lg pl-7 pr-3 py-1.5 border border-surface-container w-40 sm:w-52 focus:outline-none">
                    <span class="material-symbols-outlined text-[16px] text-on-surface-variant absolute left-2 top-2">search</span>
                </div>
            </form>
        </div>

        <!-- Tabel Dokumen PDF -->
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-surface-container text-xs uppercase tracking-wider text-on-surface-variant font-bold bg-surface-container-low/50">
                        <th class="py-3 px-4">Nama Dokumen Informasi</th>
                        <th class="py-3 px-3">Klasifikasi UU KIP</th>
                        <th class="py-3 px-3">Tahun</th>
                        <th class="py-3 px-3">Ukuran PDF</th>
                        <th class="py-3 px-4 text-center">Unduh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container">
                    @forelse($dokumenList as $doc)
                        <tr class="hover:bg-surface-container-low/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-primary text-sm">{{ $doc->judul }}</div>
                                @if($doc->ringkasan)
                                    <div class="text-xs text-on-surface-variant line-clamp-1 mt-0.5">{{ $doc->ringkasan }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-secondary-fixed text-primary whitespace-nowrap">
                                    {{ $doc->kategori?->nama ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 font-semibold text-xs text-primary">
                                {{ $doc->tahun }}
                            </td>
                            <td class="py-3.5 px-3 text-xs text-on-surface-variant whitespace-nowrap">
                                {{ $doc->ukuran_file ?: 'PDF' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if(!empty($doc->file_path))
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" download class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary text-on-primary text-xs font-bold hover:bg-primary-container transition shadow-2xs">
                                        <span class="material-symbols-outlined text-[16px]">download</span>
                                        <span>PDF</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 italic">Tersedia di Meja Layanan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-on-surface-variant text-sm">
                                <span class="material-symbols-outlined text-[36px] text-gray-400 mb-1 block">folder_off</span>
                                Belum ada dokumen publik yang terdaftar pada klasifikasi ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginasi -->
        <div class="pt-2">
            {{ $dokumenList->links() }}
        </div>
    </section>
</div>
@endsection
