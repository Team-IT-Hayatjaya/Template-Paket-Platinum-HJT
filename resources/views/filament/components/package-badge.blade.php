@php
    $package = config('packages.default', 'gold');
    $isGold = strtolower($package) === 'gold';
    $isPlatinum = strtolower($package) === 'platinum';
@endphp

<div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase shadow-sm border
    {{ $isGold 
        ? 'bg-amber-100 text-amber-900 dark:bg-amber-500/20 dark:text-amber-200 border-amber-400 dark:border-amber-500/50 ring-1 ring-amber-400/30' 
        : ($isPlatinum 
            ? 'bg-purple-100 text-purple-900 dark:bg-purple-500/20 dark:text-purple-200 border-purple-400 dark:border-purple-500/50' 
            : 'bg-slate-100 text-slate-800 dark:bg-slate-500/20 dark:text-slate-300 border-slate-400 dark:border-slate-500/40') }}"
    title="Paket Aktif: {{ strtoupper($package) }}">
    @if ($isGold)
        <span class="text-amber-500 text-sm leading-none select-none">⭐</span>
        <span class="font-extrabold text-amber-900 dark:text-amber-100">PAKET GOLD</span>
    @elseif ($isPlatinum)
        <span class="text-purple-500 text-sm leading-none select-none">💎</span>
        <span class="font-extrabold text-purple-900 dark:text-purple-100">PAKET PLATINUM</span>
    @else
        <span class="inline-block w-2 h-2 rounded-full bg-slate-400"></span>
        <span class="font-extrabold text-slate-800 dark:text-slate-200">PAKET SILVER</span>
    @endif
</div>
