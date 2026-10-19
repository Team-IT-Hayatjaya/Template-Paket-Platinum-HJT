@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $typeClasses = match($type) {
        'success' => 'bg-emerald-50 border-emerald-300 text-emerald-900',
        'danger', 'error' => 'bg-red-50 border-red-300 text-red-900',
        'warning' => 'bg-amber-50 border-amber-300 text-amber-900',
        default => 'bg-blue-50 border-blue-300 text-blue-900',
    };

    $icon = match($type) {
        'success' => '✅',
        'danger', 'error' => '❌',
        'warning' => '⚠️',
        default => 'ℹ️',
    };
@endphp

<div role="alert" 
     @if($dismissible) x-data="{ open: true }" x-show="open" @endif
     {{ $attributes->merge(['class' => "p-4 rounded-xl border flex items-start gap-3 text-sm shadow-sm transition-all $typeClasses"]) }}>
    <span class="text-base select-none shrink-0" aria-hidden="true">{{ $icon }}</span>
    <div class="flex-1">
        @if($title)
            <h4 class="font-bold text-sm mb-1 leading-snug">{{ $title }}</h4>
        @endif
        <div class="leading-relaxed">
            {{ $slot }}
        </div>
    </div>
    @if($dismissible)
        <button type="button" 
                @click="open = false" 
                class="text-slate-400 hover:text-slate-700 font-bold p-1 rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1"
                aria-label="Tutup pemberitahuan">
            &times;
        </button>
    @endif
</div>
