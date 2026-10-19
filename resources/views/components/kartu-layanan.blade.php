@props([
    'title' => 'Layanan Administrasi',
    'desc' => 'Pengurusan dokumen dan rekomendasi resmi kantor kecamatan.',
    'icon' => 'assignment',
    'badge' => 'Standar 1 Hari',
    'link' => route('layanan'),
])

<div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm hover:shadow-md border border-outline-variant/30 hover:border-secondary transition-all flex flex-col justify-between group">
    <div class="space-y-space-xs">
        <div class="flex items-center justify-between mb-space-xs">
            <div class="w-12 h-12 rounded-xl bg-surface-container-high text-primary group-hover:bg-secondary group-hover:text-on-secondary transition-colors flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">{{ $icon }}</span>
            </div>
            <span class="font-label-sm text-[11px] bg-secondary-container/50 text-on-secondary-container px-2 py-0.5 rounded font-semibold border border-secondary/20">
                {{ $badge }}
            </span>
        </div>

        <h3 class="font-headline-md text-base sm:text-lg font-bold text-primary group-hover:text-secondary transition-colors leading-snug">
            {{ $title }}
        </h3>

        <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-3 leading-relaxed">
            {{ $desc }}
        </p>
    </div>

    <div class="pt-space-sm mt-space-sm border-t border-outline-variant/20 flex items-center justify-between text-xs">
        <a href="{{ $link }}" class="inline-flex items-center gap-1 font-semibold text-primary group-hover:text-secondary transition-colors">
            <span>Persyaratan &amp; Alur</span>
            <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </a>
    </div>
</div>
