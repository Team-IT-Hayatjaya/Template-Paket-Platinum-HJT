@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
])

@php
    $baseStyles = "inline-flex items-center justify-center font-semibold rounded-lg transition-colors duration-150 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2";
    
    $sizeStyles = match($size) {
        'sm' => 'px-3 py-1.5 text-xs gap-1.5',
        'lg' => 'px-6 py-3 text-base gap-2.5',
        default => 'px-4 py-2 text-sm gap-2',
    };

    $variantStyles = match($variant) {
        'secondary' => 'bg-slate-700 text-white hover:bg-slate-800 focus-visible:ring-slate-600',
        'danger' => 'bg-red-700 text-white hover:bg-red-800 focus-visible:ring-red-600',
        'outline' => 'bg-transparent text-slate-800 border border-slate-300 hover:bg-slate-100 focus-visible:ring-slate-500',
        'light' => 'bg-slate-100 text-slate-800 hover:bg-slate-200 focus-visible:ring-slate-400',
        'ghost' => 'bg-transparent text-slate-700 hover:bg-slate-100 focus-visible:ring-slate-400',
        default => 'text-white hover:opacity-90',
    };

    $customStyle = $variant === 'primary' 
        ? "background-color: var(--gov-primary, #1e3a8a); color: #ffffff;" 
        : "";
@endphp

@if($href)
    <a href="{{ $href }}" 
       style="{{ $customStyle }}"
       {{ $attributes->merge(['class' => "$baseStyles $sizeStyles $variantStyles"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" 
            style="{{ $customStyle }}"
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge(['class' => "$baseStyles $sizeStyles $variantStyles"]) }}>
        {{ $slot }}
    </button>
@endif
