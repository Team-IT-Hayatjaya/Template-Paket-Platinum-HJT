@props([
    'title' => 'PENGUMUMAN PENTING',
    'message' => 'Penyaluran Bantuan Sosial & Jadwal Rekam KTP-el Jemput Bola di Seluruh Desa — Simak Persyaratan & Dokumen Wajib.',
    'link' => '#pengumuman',
    'linkLabel' => 'Lihat Rincian Jadwal'
])

<section class="w-full bg-error-container/40 border-y border-error/20 px-margin-mobile lg:px-margin py-space-sm" aria-label="Pengumuman Penting">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-space-sm">
        <div class="flex items-center gap-space-sm text-on-error-container">
            <span class="material-symbols-outlined text-error text-[22px] shrink-0 fill-1">campaign</span>
            <span class="bg-error text-on-error font-label-sm text-[11px] px-space-xs py-0.5 rounded uppercase tracking-wider font-semibold shrink-0">
                {{ $title }}
            </span>
            <p class="font-body-sm text-body-sm text-on-surface font-medium">
                {{ $message }}
            </p>
        </div>
        @if(!empty($link))
            <a class="inline-flex items-center gap-1 font-label-sm text-label-sm text-error font-semibold hover:underline shrink-0" href="{{ $link }}">
                <span>{{ $linkLabel }}</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        @endif
    </div>
</section>
