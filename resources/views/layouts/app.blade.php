<!DOCTYPE html>
<html lang="id" class="h-full bg-background scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beranda') - {{ $siteSetting->institution_name ?? config('app.name', 'Portal Resmi Pemerintah') }}</title>
    <meta name="description" content="@yield('meta_description', 'Portal Resmi Pelayanan Publik Terpadu dan Keterbukaan Informasi Publik (PPID) sesuai UU No. 14 Tahun 2008.')">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph & Social Sharing -->
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('meta_title', $siteSetting->institution_name ?? config('app.name', 'Portal Resmi Pemerintah'))">
    <meta property="og:description" content="@yield('meta_description', 'Portal Resmi Pelayanan Publik Terpadu dan Keterbukaan Informasi Publik (PPID) sesuai UU No. 14 Tahun 2008.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $siteSetting->institution_name ?? config('app.name', 'Portal Resmi Pemerintah') }}">
    <meta property="og:image" content="@yield('og_image', !empty($siteSetting->logo_path) ? asset('storage/' . $siteSetting->logo_path) : asset('images/logo.png'))">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('meta_title', $siteSetting->institution_name ?? config('app.name', 'Portal Resmi Pemerintah'))">
    <meta name="twitter:description" content="@yield('meta_description', 'Portal Resmi Pelayanan Publik Terpadu dan Keterbukaan Informasi Publik (PPID) sesuai UU No. 14 Tahun 2008.')">
    <meta name="twitter:image" content="@yield('og_image', !empty($siteSetting->logo_path) ? asset('storage/' . $siteSetting->logo_path) : asset('images/logo.png'))">
    
    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,300..900;1,300..900&family=Source+Serif+4:ital,opsz,wght@0,8..60,200..900;1,8..60,200..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Dynamic Theme Variables from site_settings -->
    <x-theme-styles />

    <!-- Vite Assets (Tailwind CSS v4 & Alpine.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-background font-body-md text-on-surface antialiased flex flex-col min-h-full">
    <!-- Aksesibilitas: Tautan Lompat ke Konten Utama & Navigasi (WCAG 2.1 AA) -->
    <div class="sr-only focus-within:not-sr-only focus-within:fixed focus-within:top-3 focus-within:left-3 focus-within:z-50 focus-within:flex focus-within:gap-2">
        <a href="#main-content" class="px-4 py-2 bg-primary text-on-primary rounded-lg shadow-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-secondary">
            Lompat ke Konten Utama &darr;
        </a>
        <a href="#main-navigation" class="px-4 py-2 bg-primary text-on-primary rounded-lg shadow-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-secondary">
            Lompat ke Navigasi Menu &darr;
        </a>
        @if(\App\Support\Package::has('accessibility'))
            <a href="#accessibility-widget-toggle" class="px-4 py-2 bg-secondary text-primary rounded-lg shadow-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-primary">
                Lompat ke Alat Aksesibilitas &darr;
            </a>
        @endif
    </div>

    <!-- Header Instansi Modular (Top ribbon, Brand, Navigation, Mobile Menu) -->
    <x-header />

    <!-- Notifikasi Sistem / Alert Flash Messages -->
    @if(session('success') || session('error') || session('info'))
        <div class="max-w-7xl mx-auto w-full px-margin-mobile lg:px-margin pt-4">
            @if(session('success'))
                <x-alert type="success" title="Berhasil" :dismissible="true">
                    {{ session('success') }}
                </x-alert>
            @endif
            @if(session('error'))
                <x-alert type="danger" title="Kesalahan" :dismissible="true">
                    {{ session('error') }}
                </x-alert>
            @endif
            @if(session('info'))
                <x-alert type="info" title="Informasi" :dismissible="true">
                    {{ session('info') }}
                </x-alert>
            @endif
        </div>
    @endif

    <!-- Konten Utama Semantik -->
    <main id="main-content" class="w-full flex-1 bg-surface">
        @yield('content')
    </main>

    <!-- Footer Instansi Resmi Modular -->
    <x-footer />

    <!-- Widget Aksesibilitas Ramah Disabilitas (WCAG 2.1 AA) -->
    @if(\App\Support\Package::has('accessibility'))
        <x-accessibility-widget />
    @endif

    @stack('scripts')
</body>
</html>
