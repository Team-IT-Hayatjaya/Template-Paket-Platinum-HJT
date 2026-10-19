@php
    $package = config('packages.default', 'gold');
    $isGold = strtolower($package) === 'gold';
    $isPlatinum = strtolower($package) === 'platinum';
    $pkgUpper = strtoupper($package);
@endphp

<div class="fi-footer py-3 px-6 mt-8 border-t border-gray-200 dark:border-gray-800 text-xs text-gray-500 dark:text-gray-400">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 max-w-7xl mx-auto">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase border
                {{ $isGold 
                    ? 'bg-amber-50 text-amber-800 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-700/60' 
                    : ($isPlatinum 
                        ? 'bg-purple-50 text-purple-800 border-purple-300 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-700/60' 
                        : 'bg-slate-50 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700') }}">
                @if ($isGold)
                    <span>⭐</span>
                @elseif ($isPlatinum)
                    <span>💎</span>
                @else
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                @endif
                <span>PAKET {{ $pkgUpper }}</span>
            </span>
            <span class="font-medium text-gray-700 dark:text-gray-300">{{ config('app.name', 'Portal Resmi Instansi Pemerintah') }}</span>
        </div>
        <div class="flex items-center gap-3 text-gray-400 dark:text-gray-500 text-[11px]">
            <span>Lingkungan: <strong class="text-gray-600 dark:text-gray-300">Gold</strong> (Base: <code>silver-v1.0</code>)</span>
            <span>&bull;</span>
            <span>PT Hayat Jaya Teknologi (HJT)</span>
        </div>
    </div>
</div>
