@extends('layouts.app')

@section('title', 'Data & Statistik Wilayah - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))
@section('meta_title', 'Data & Statistik Resmi Wilayah')
@section('meta_description', 'Katalog data statistik sektoral, kependudukan, pendidikan, kesehatan, dan potensi wilayah.')

@section('content')
<div class="space-y-space-xl max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Data & Statistik'],
    ]" />

    <!-- Hero Header -->
    <section class="bg-surface-container-lowest p-space-lg lg:p-space-xl rounded-2xl shadow-2xs border border-surface-container flex flex-col md:flex-row md:items-end justify-between gap-space-lg">
        <div class="space-y-space-xs max-w-3xl">
            <div class="inline-flex items-center gap-space-xs bg-surface-container px-space-sm py-space-xs rounded-full text-secondary font-label-sm text-label-sm font-bold uppercase tracking-wider">
                <span class="material-symbols-outlined text-[16px]">bar_chart</span>
                <span>Statistik Sektoral &amp; Keterbukaan Data Wilayah</span>
            </div>

            <h1 class="font-headline-xl text-headline-xl text-primary font-bold tracking-tight">
                Katalog Data &amp; Statistik
            </h1>

            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                Penyajian data agregat kependudukan, sarana pendidikan, fasilitas kesehatan, ekonomi pertanian, dan bantuan sosial {{ $siteSetting->institution_name ?? 'Pemerintah Instansi' }}.
            </p>
        </div>
    </section>

    <!-- FILTER & DAFTAR STATISTIK -->
    <section class="bg-surface-container-lowest p-space-md lg:p-space-lg rounded-2xl border border-surface-container shadow-2xs space-y-space-md">
        <!-- Filter Kategori Tabs & Tahun -->
        <div class="border-b border-surface-container pb-3 flex flex-col md:flex-row md:items-center justify-between gap-space-sm">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 max-w-full">
                <a href="{{ route('statistik.index', array_merge(request()->except('kategori', 'page'), ['kategori' => 'semua'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition {{ ($kategoriPilihan === 'semua' || empty($kategoriPilihan)) ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}">
                    Semua Kategori
                </a>
                @foreach($kategoriList as $kat)
                    <a href="{{ route('statistik.index', array_merge(request()->except('kategori', 'page'), ['kategori' => $kat])) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition {{ ($kategoriPilihan === $kat) ? 'bg-primary text-on-primary shadow-xs' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}">
                        {{ $kat }}
                    </a>
                @endforeach
            </div>

            <!-- Filter Tahun & Pencarian -->
            <form action="{{ route('statistik.index') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="kategori" value="{{ $kategoriPilihan }}">

                <select name="tahun" onchange="this.form.submit()" class="bg-surface-container text-xs rounded-lg px-2.5 py-1.5 border border-surface-container focus:outline-none">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $y)
                        <option value="{{ $y }}" {{ ($tahunPilihan == $y) ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>

                <div class="relative">
                    <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari data statistik..." class="bg-surface-container text-xs rounded-lg pl-7 pr-3 py-1.5 border border-surface-container w-44 sm:w-52 focus:outline-none">
                    <span class="material-symbols-outlined text-[16px] text-on-surface-variant absolute left-2 top-2">search</span>
                </div>
            </form>
        </div>

        <!-- DAFTAR KARTU DATA STATISTIK -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
            @forelse($daftarStatistik as $stat)
                <article class="bg-surface-container-low p-space-md rounded-xl border border-surface-container hover:shadow-sm transition flex flex-col justify-between space-y-space-sm">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-secondary-fixed text-primary">
                                {{ $stat->kategori }}
                            </span>
                            <span class="text-xs font-semibold text-on-surface-variant">
                                Periode: {{ $stat->periode }}
                            </span>
                        </div>

                        <h3 class="font-title-md text-base font-bold text-primary hover:text-secondary transition-colors">
                            <a href="{{ route('statistik.detail', $stat->slug) }}">
                                {{ $stat->judul }}
                            </a>
                        </h3>

                        @if($stat->sumber)
                            <div class="text-[11px] text-on-surface-variant flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-secondary">source</span>
                                <span>Sumber: {{ $stat->sumber }}</span>
                            </div>
                        @endif

                        @if(!empty($stat->data_grafik))
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 text-amber-800 border border-amber-200/60 text-[11px] font-bold">
                                <span class="material-symbols-outlined text-[15px] text-amber-600">query_stats</span>
                                <span>Tersedia Grafik {{ strtoupper($stat->tipe_grafik ?? 'BAR') }}</span>
                            </div>
                        @endif

                        @if(!empty($stat->gambar_infografis))
                            <div class="pt-1">
                                <img src="{{ asset('storage/' . $stat->gambar_infografis) }}" alt="{{ $stat->judul }}" class="w-full h-36 object-cover rounded-lg border border-surface-container" loading="lazy">
                            </div>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-surface-container flex items-center justify-between text-xs flex-wrap gap-2">
                        <a href="{{ route('statistik.detail', $stat->slug) }}" class="inline-flex items-center gap-1 font-bold text-primary hover:text-secondary transition-colors">
                            <span>Lihat Grafik &amp; Tabel Data</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>

                        <div class="flex items-center gap-1.5">
                            @if(!empty($stat->berkas_spreadsheet))
                                <a href="{{ asset('storage/' . $stat->berkas_spreadsheet) }}" target="_blank" download class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-emerald-100 text-emerald-800 hover:bg-emerald-700 hover:text-white transition text-[11px] font-bold" title="Unduh Berkas Excel">
                                    <span class="material-symbols-outlined text-[14px]">table_view</span>
                                    <span>Excel</span>
                                </a>
                            @endif

                            @if(!empty($stat->berkas_pdf))
                                <a href="{{ asset('storage/' . $stat->berkas_pdf) }}" target="_blank" download class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-surface-container-high text-primary hover:bg-primary hover:text-on-primary transition text-[11px] font-bold" title="Unduh Berkas PDF">
                                    <span class="material-symbols-outlined text-[14px]">download</span>
                                    <span>PDF</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-2 py-12 text-center text-on-surface-variant text-sm">
                    <span class="material-symbols-outlined text-[48px] text-gray-400 mb-2 block">query_stats</span>
                    Belum ada data statistik yang sesuai dengan filter atau kata kunci pencarian.
                </div>
            @endforelse
        </div>

        <!-- Paginasi -->
        <div class="pt-2">
            {{ $daftarStatistik->links() }}
        </div>
    </section>
</div>
@endsection
