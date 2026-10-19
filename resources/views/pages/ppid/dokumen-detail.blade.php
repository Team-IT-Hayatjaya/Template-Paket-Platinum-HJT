@extends('layouts.app')

@section('title', $dokumen->judul . ' - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))
@section('meta_title', $dokumen->judul . ' - Dokumen Resmi PPID')
@section('meta_description', $dokumen->ringkasan ?: 'Dokumen resmi PPID ' . $dokumen->judul . ' tahun ' . $dokumen->tahun . ' sesuai UU No. 14 Tahun 2008.')

@section('content')
<div class="space-y-space-xl max-w-6xl mx-auto px-margin-mobile lg:px-0 py-space-md">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Informasi Publik / PPID', 'url' => route('ppid.daftar-informasi')],
        ['label' => $dokumen->judul],
    ]" />

    <!-- Header Dokumen (Sesuai Contoh Gambar: Judul Besar Huruf Kapital, Subtitle di Bawah) -->
    <header class="text-center space-y-2 py-space-sm border-b border-surface-container">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-fixed text-primary text-xs font-bold uppercase tracking-wider">
            <span class="material-symbols-outlined text-[16px]">verified</span>
            <span>Dokumen Informasi Publik Resmi (UU No. 14 Tahun 2008)</span>
        </div>
        <h1 class="font-headline-xl text-2xl sm:text-3xl md:text-4xl font-extrabold text-primary tracking-tight uppercase">
            {{ $dokumen->judul }}
        </h1>
        @if($dokumen->ringkasan)
            <p class="font-body-md text-sm sm:text-base text-on-surface-variant max-w-2xl mx-auto">
                {{ $dokumen->ringkasan }}
            </p>
        @endif
        <div class="flex items-center justify-center gap-4 text-xs text-on-surface-variant pt-1 font-medium">
            <span>Klasifikasi: <strong>{{ $dokumen->kategori?->nama ?? 'Umum' }}</strong></span>
            <span>•</span>
            <span>Tahun: <strong>{{ $dokumen->tahun }}</strong></span>
            @if($dokumen->ukuran_file)
                <span>•</span>
                <span>Ukuran: <strong>{{ $dokumen->ukuran_file }}</strong></span>
            @endif
        </div>
    </header>

    <!-- Toolbar Interaktif Dokumen PDF -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-space-sm bg-surface-container-lowest p-space-md rounded-2xl border border-surface-container shadow-2xs">
        <div class="flex items-center gap-2">
            <span class="w-9 h-9 rounded-xl bg-primary text-on-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
            </span>
            <div>
                <div class="text-xs font-bold text-primary">Pratinjau Berkas Dokumen Digital</div>
                <div class="text-[11px] text-on-surface-variant">Dapat dibaca langsung di peramban, diunduh, atau dicetak</div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if(!empty($dokumen->file_path))
                <!-- Tombol Unduh PDF Utama -->
                <a href="{{ asset('storage/' . $dokumen->file_path) }}" 
                   target="_blank" 
                   download 
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-primary text-on-primary hover:bg-primary-container text-xs font-bold shadow-xs transition transform hover:scale-[1.02]">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    <span>Unduh Dokumen PDF</span>
                </a>

                <!-- Buka di Jendela Baru -->
                <a href="{{ asset('storage/' . $dokumen->file_path) }}" 
                   target="_blank" 
                   rel="noopener"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-surface-container-high hover:bg-surface-container text-primary text-xs font-bold transition border border-surface-container">
                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                    <span>Buka Tab Penuh</span>
                </a>

                <!-- Cetak Langsung -->
                <button type="button" 
                        onclick="window.printPdf('pdfViewerFrame')" 
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-surface-container-high hover:bg-surface-container text-primary text-xs font-bold transition border border-surface-container">
                    <span class="material-symbols-outlined text-[16px]">print</span>
                    <span>Cetak</span>
                </button>
            @endif

            <a href="{{ route('ppid.daftar-informasi') }}" 
               class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-on-surface-variant hover:text-primary text-xs font-medium transition">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Daftar Dokumen</span>
            </a>
        </div>
    </div>

    <!-- FRAME INTERAKTIF TAMPILAN ISI PDF (EMBEDDED PDF VIEWER) -->
    <div class="bg-surface-container-lowest p-2 sm:p-4 rounded-2xl border border-surface-container shadow-md">
        @if(!empty($dokumen->file_path))
            <div class="relative w-full rounded-xl overflow-hidden border border-surface-container bg-slate-100">
                <object data="{{ asset('storage/' . $dokumen->file_path) }}#toolbar=1&navpanes=0&view=FitH" 
                        type="application/pdf" 
                        class="w-full h-[750px] sm:h-[880px] md:h-[980px] rounded-xl"
                        id="pdfViewerObject">
                    <iframe src="{{ asset('storage/' . $dokumen->file_path) }}#toolbar=1&navpanes=0&view=FitH" 
                            class="w-full h-[750px] sm:h-[880px] md:h-[980px] border-0 rounded-xl"
                            id="pdfViewerFrame"
                            title="{{ $dokumen->judul }}">
                        <!-- Fallback jika peramban memblokir iframe PDF -->
                        <div class="p-8 text-center space-y-4 bg-surface-container-low rounded-xl">
                            <span class="material-symbols-outlined text-[54px] text-primary">picture_as_pdf</span>
                            <h3 class="font-bold text-base text-primary">Pratinjau PDF Tersedia</h3>
                            <p class="text-xs text-on-surface-variant max-w-md mx-auto">
                                Peramban Anda tidak mendukung penampil PDF langsung. Anda dapat mengunduh berkas resmi ini untuk membacanya di perangkat Anda.
                            </p>
                            <a href="{{ asset('storage/' . $dokumen->file_path) }}" download class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-on-primary font-bold text-sm shadow-xs hover:bg-primary-container transition">
                                <span class="material-symbols-outlined text-[18px]">download</span>
                                <span>Unduh Berkas PDF Resmi</span>
                            </a>
                        </div>
                    </iframe>
                </object>
            </div>
        @else
            <div class="p-12 text-center text-on-surface-variant bg-surface-container-low rounded-xl space-y-2">
                <span class="material-symbols-outlined text-[48px] text-gray-400">file_copy</span>
                <p class="text-sm font-semibold">Berkas digital belum diunggah atau sedang dalam pemutakhiran.</p>
                <p class="text-xs text-gray-500">Silakan hubungi Meja Layanan PPID untuk permohonan salinan cetak.</p>
            </div>
        @endif
    </div>

    <!-- Dokumen Informasi Terkait -->
    @if(isset($dokumenTerkait) && $dokumenTerkait->isNotEmpty())
        <section class="bg-surface-container-lowest p-space-lg rounded-2xl border border-surface-container shadow-2xs space-y-space-md">
            <h3 class="font-title-lg text-base font-bold text-primary flex items-center gap-2 border-b border-surface-container pb-2">
                <span class="material-symbols-outlined text-[20px] text-secondary">folder</span>
                <span>Dokumen PPID Terkait Lainnya</span>
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-space-sm">
                @foreach($dokumenTerkait as $item)
                    <div class="p-3 rounded-xl bg-surface-container-low border border-surface-container flex flex-col justify-between hover:border-secondary/40 transition">
                        <div>
                            <span class="text-[10px] font-bold text-secondary uppercase">{{ $item->kategori?->nama ?? 'PPID' }}</span>
                            <h4 class="font-title-md text-xs font-bold text-primary mt-1 line-clamp-2 hover:text-secondary">
                                <a href="{{ route('ppid.dokumen.detail', $item->slug) }}">{{ $item->judul }}</a>
                            </h4>
                        </div>
                        <div class="pt-2 mt-2 border-t border-surface-container flex items-center justify-between text-[11px]">
                            <span class="text-on-surface-variant">Tahun {{ $item->tahun }}</span>
                            <a href="{{ route('ppid.dokumen.detail', $item->slug) }}" class="font-bold text-primary hover:text-secondary inline-flex items-center gap-0.5">
                                <span>Lihat PDF</span>
                                <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>

<script>
function printPdf(frameId) {
    const frame = document.getElementById(frameId);
    if (frame && frame.contentWindow) {
        frame.contentWindow.focus();
        frame.contentWindow.print();
    } else {
        window.print();
    }
}
</script>
@endsection
