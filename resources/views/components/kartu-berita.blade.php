@props([
    'berita' => null,
    'title' => null,
    'date' => null,
    'category' => null,
    'excerpt' => null,
    'url' => null,
    'image' => null,
    'featured' => false,
])

@php
    $judul = $title ?? ($berita['judul'] ?? 'Judul Berita Resmi');
    $slug = $berita['slug'] ?? \Illuminate\Support\Str::slug($judul);
    $kategori = $category ?? ($berita['kategori'] ?? 'Pemerintahan');
    $tanggal = $date ?? ($berita['tanggal'] ?? now()->translatedFormat('d F Y'));
    $ringkasan = $excerpt ?? ($berita['ringkasan'] ?? '');
    $gambar = !empty($image) ? $image : (!empty($berita['gambar']) ? $berita['gambar'] : asset('images/berita-festival.jpg'));
    $linkUrl = !empty($url) && $url !== '#' ? $url : route('berita.detail', $slug);
@endphp

<article class="bg-surface-container-lowest rounded-xl shadow-2xs hover:shadow-md transition-all overflow-hidden flex flex-col border border-surface-container group">
    <!-- Thumbnail Image -->
    <a href="{{ $linkUrl }}" class="block aspect-video overflow-hidden relative bg-surface-container-low" aria-label="Baca berita: {{ $judul }}">
        <img 
            src="{{ $gambar }}" 
            alt="{{ $berita['alt_text'] ?? ('Foto artikel: ' . $judul) }}" 
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            loading="lazy"
        />
        <div class="absolute top-3 left-3 flex items-center gap-1.5 flex-wrap">
            <span class="bg-secondary-fixed text-primary font-label-sm text-[11px] font-bold px-2.5 py-0.5 rounded shadow-2xs uppercase tracking-wide">
                {{ $kategori }}
            </span>
            @if(!empty($berita['unit_singkatan']))
                <span class="bg-blue-600 text-white font-label-sm text-[10px] font-bold px-2 py-0.5 rounded shadow-2xs uppercase tracking-wider">
                    {{ $berita['unit_singkatan'] }}
                </span>
            @endif
        </div>
    </a>

    <!-- Content -->
    <div class="p-space-md flex-1 flex flex-col justify-between space-y-space-xs">
        <div class="space-y-space-xs">
            <!-- Metadata: Date -->
            <div class="flex items-center gap-space-xs text-on-surface-variant text-xs">
                <span class="material-symbols-outlined text-[16px] text-secondary">calendar_today</span>
                <span>{{ $tanggal }}</span>
            </div>

            <!-- Title -->
            <h3 class="font-headline-md text-base sm:text-lg font-bold text-primary group-hover:text-secondary transition-colors line-clamp-2 leading-snug">
                <a href="{{ $linkUrl }}">
                    {{ $judul }}
                </a>
            </h3>

            <!-- Excerpt -->
            <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2 leading-relaxed">
                {{ $ringkasan }}
            </p>
        </div>

        <!-- Read more link -->
        <div class="pt-space-xs border-t border-surface-container mt-space-sm flex items-center justify-between text-xs">
            <a href="{{ $linkUrl }}" class="inline-flex items-center gap-1 font-semibold text-primary group-hover:text-secondary transition-colors">
                <span>Baca Selengkapnya</span>
                <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </div>
    </div>
</article>
