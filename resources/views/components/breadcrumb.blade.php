@props([
    'items' => [],
])

<nav aria-label="Breadcrumb" class="w-full py-space-sm bg-surface-container-low/50 border-b border-surface-container px-margin-mobile lg:px-margin text-xs">
    <div class="max-w-7xl mx-auto flex items-center gap-2 flex-wrap text-on-surface-variant">
        <a href="{{ route('home') }}" class="flex items-center gap-1 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-[16px] text-secondary">home</span>
            <span>Beranda</span>
        </a>

        @foreach($items as $key => $val)
            @php
                if (is_array($val)) {
                    $itemLabel = $val['label'] ?? ($val['title'] ?? '');
                    $itemUrl = $val['url'] ?? null;
                } elseif (is_string($key)) {
                    $itemLabel = $key;
                    $itemUrl = $val;
                } else {
                    $itemLabel = (string) $val;
                    $itemUrl = null;
                }
            @endphp
            @if(!empty($itemLabel) && $itemLabel !== 'Beranda')
                <span class="material-symbols-outlined text-[14px] text-outline">chevron_right</span>
                @if(!$loop->last && !empty($itemUrl))
                    <a href="{{ $itemUrl }}" class="hover:text-primary transition-colors">
                        {{ $itemLabel }}
                    </a>
                @else
                    <span class="font-semibold text-primary truncate max-w-xs sm:max-w-md">
                        {{ $itemLabel }}
                    </span>
                @endif
            @endif
        @endforeach
    </div>
</nav>
