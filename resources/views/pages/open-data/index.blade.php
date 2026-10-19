@extends('layouts.app')

@section('title', 'Portal Satu Data & Dataset Terbuka - ' . ($pengaturan->nama_instansi ?? 'Pemerintah'))
@section('meta_description', 'Portal Satu Data resmi instansi pemerintah menyajikan data statistik sektoral publik yang akurat, mutakhir, terpadu, dan dapat diakses dengan mudah.')

@section('content')
<main class="min-h-screen bg-slate-50 dark:bg-slate-900 pb-20">
    <!-- Hero Banner Satu Data -->
    <section class="bg-gradient-to-br from-blue-900 via-indigo-950 to-slate-900 text-white py-16 px-4 relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_30%,rgba(59,130,246,0.15),transparent)]"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex items-center gap-2 mb-4">
                <span class="px-3 py-1 bg-blue-500/20 text-blue-300 border border-blue-400/30 rounded-full text-xs font-bold uppercase tracking-wider">
                    📊 SATU DATA INDONESIA
                </span>
                <span class="px-3 py-1 bg-purple-500/20 text-purple-300 border border-purple-400/30 rounded-full text-xs font-bold uppercase tracking-wider">
                    💎 PAKET PLATINUM
                </span>
            </div>
            <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mb-4 leading-tight">
                Portal Satu Data & Dataset Terbuka
            </h1>
            <p class="text-slate-300 max-w-3xl text-base md:text-lg mb-8 leading-relaxed">
                Akses publik terhadap data sektoral terbuka pemerintah secara transparan, akuntabel, dan bebas digunakan kembali dalam format terbuka (CSV, JSON, dan API).
            </p>

            <!-- Search & Filters -->
            <form action="{{ route('open-data.index') }}" method="GET" class="bg-white/10 backdrop-blur-md p-3 md:p-4 rounded-2xl border border-white/20 shadow-xl max-w-4xl">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-6 relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ $q ?? '' }}" 
                            placeholder="Cari judul dataset, topik, atau sumber data..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border border-transparent"
                        />
                    </div>
                    <div class="md:col-span-4">
                        <select 
                            name="kategori" 
                            class="w-full px-4 py-2.5 rounded-xl bg-white text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border border-transparent"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat }}" {{ ($kategori ?? '') === $kat ? 'selected' : '' }}>
                                    {{ $kat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition-all shadow-md">
                            Cari
                        </button>
                        @if(!empty($q) || !empty($kategori))
                            <a href="{{ route('open-data.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white px-3 py-2.5 rounded-xl text-sm flex items-center justify-center" title="Reset filter">
                                <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Ringkasan Statistik -->
            <div class="flex flex-wrap items-center gap-6 mt-8 text-xs text-slate-300">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    <span>Total Dataset: <strong>{{ number_format($totalDatasets) }}</strong></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                    <span>Total Unduhan: <strong>{{ number_format($totalUnduhan) }} kali</strong></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                    <span>API Publik: <code class="bg-slate-800 px-1.5 py-0.5 rounded text-amber-300 font-mono">/api/v1/datasets</code></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Daftar Dataset -->
    <section class="max-w-7xl mx-auto px-4 mt-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">dataset</span>
                Katalog Dataset Terbuka
                <span class="text-sm font-normal text-slate-500">({{ $datasets->total() }} data)</span>
            </h2>
        </div>

        @if($datasets->isEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-12 text-center border border-slate-200 dark:border-slate-700 shadow-sm">
                <span class="material-symbols-outlined text-6xl text-slate-400 mb-3">folder_off</span>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 mb-1">Belum Ada Dataset Ditemukan</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm max-w-md mx-auto mb-6">
                    Tidak ada dataset yang sesuai dengan kriteria pencarian Anda. Coba kata kunci lain atau reset filter.
                </p>
                <a href="{{ route('open-data.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">
                    Lihat Semua Dataset
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($datasets as $ds)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between p-6 group">
                        <div>
                            <!-- Header Card: Kategori & Unit -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                    {{ $ds->kategori }}
                                </span>
                                @if($ds->unit)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                        {{ $ds->unit->singkatan ?? $ds->unit->nama }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                        Pemerintah Daerah
                                    </span>
                                @endif
                            </div>

                            <!-- Judul Dataset -->
                            <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-blue-600 transition-colors line-clamp-2 mb-2 leading-snug">
                                <a href="{{ route('open-data.detail', $ds->slug) }}">
                                    {{ $ds->judul }}
                                </a>
                            </h3>

                            <!-- Deskripsi Singkat -->
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-3 mb-4 leading-relaxed">
                                {{ $ds->deskripsi ?: 'Dataset resmi sektoral pemerintah daerah yang menyajikan rekapan berkala untuk keperluan publikasi dan analisis data.' }}
                            </p>

                            <!-- Metadata List -->
                            <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-slate-700/60 pt-3 mb-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Sumber:</span>
                                    <span class="font-medium truncate max-w-[180px]">{{ $ds->sumber }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Periode:</span>
                                    <span class="font-medium">{{ $ds->periode }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Jumlah Baris:</span>
                                    <span class="font-medium">{{ number_format($ds->row_count) }} baris</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Pembaruan:</span>
                                    <span class="font-medium">{{ $ds->tanggal_pembaruan?->translatedFormat('d M Y') ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Card: Formats & Action -->
                        <div class="border-t border-slate-100 dark:border-slate-700/60 pt-4 flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    CSV
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                    JSON
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                    CHART
                                </span>
                            </div>
                            <a 
                                href="{{ route('open-data.detail', $ds->slug) }}" 
                                class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700 group-hover:translate-x-0.5 transition-transform"
                            >
                                <span>Detail & Grafik</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $datasets->links() }}
            </div>
        @endif
    </section>
</main>
@endsection
