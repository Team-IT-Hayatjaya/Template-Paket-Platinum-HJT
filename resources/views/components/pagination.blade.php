@props([
    'currentPage' => 1,
    'totalPages' => 5,
    'baseUrl' => '#',
])

<nav class="flex items-center justify-between border-t border-outline-variant/30 px-4 py-4 mt-8" aria-label="Navigasi Halaman Data">
    <div class="hidden sm:block">
        <p class="text-xs text-on-surface-variant">
            Menampilkan halaman <span class="font-bold text-primary">{{ $currentPage }}</span> dari <span class="font-bold text-primary">{{ $totalPages }}</span>
        </p>
    </div>
    <div class="flex flex-1 justify-between sm:justify-end gap-2 items-center">
        @if($currentPage > 1)
            <a href="{{ $baseUrl }}?page={{ $currentPage - 1 }}" 
               class="inline-flex items-center gap-1 rounded-lg border border-outline-variant/40 bg-surface-container-lowest px-3 py-2 text-xs font-semibold text-primary hover:bg-secondary hover:text-on-secondary transition-colors">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Sebelumnya</span>
            </a>
        @else
            <span class="inline-flex items-center gap-1 rounded-lg border border-outline-variant/20 bg-surface-container-low px-3 py-2 text-xs font-medium text-outline cursor-not-allowed">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Sebelumnya</span>
            </span>
        @endif

        <div class="sm:hidden flex items-center text-xs font-bold text-primary px-2">
            {{ $currentPage }} / {{ $totalPages }}
        </div>

        <div class="hidden md:flex gap-1">
            @for($i = 1; $i <= $totalPages; $i++)
                @if($i == $currentPage)
                    <span aria-current="page" 
                          class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-on-primary bg-primary rounded-lg shadow-sm">
                        {{ $i }}
                    </span>
                @else
                    <a href="{{ $baseUrl }}?page={{ $i }}" 
                       class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-on-surface-variant bg-surface-container-lowest border border-outline-variant/30 rounded-lg hover:bg-secondary hover:text-on-secondary transition-colors"
                       aria-label="Pindah ke halaman {{ $i }}">
                        {{ $i }}
                    </a>
                @endif
            @endfor
        </div>

        @if($currentPage < $totalPages)
            <a href="{{ $baseUrl }}?page={{ $currentPage + 1 }}" 
               class="inline-flex items-center gap-1 rounded-lg border border-outline-variant/40 bg-surface-container-lowest px-3 py-2 text-xs font-semibold text-primary hover:bg-secondary hover:text-on-secondary transition-colors">
                <span>Berikutnya</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        @else
            <span class="inline-flex items-center gap-1 rounded-lg border border-outline-variant/20 bg-surface-container-low px-3 py-2 text-xs font-medium text-outline cursor-not-allowed">
                <span>Berikutnya</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </span>
        @endif
    </div>
</nav>
