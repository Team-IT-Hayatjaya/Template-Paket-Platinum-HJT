<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dasbor') - Panel Administrasi PPID</title>
    
    <!-- CSS Fallback Instansi -->
    <link rel="stylesheet" href="{{ asset('css/instansi.css') }}">

    <!-- Vite Assets (Tailwind CSS & Alpine.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-theme-styles />
    @stack('styles')
</head>
<body class="min-h-full font-sans antialiased text-slate-800 bg-slate-100" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Backdrop Mobile -->
        <div x-show="sidebarOpen" 
             x-cloak 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs md:hidden"
             aria-hidden="true"></div>

        <!-- Bilah Samping (Sidebar) Desktop & Mobile Drawer -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-100 flex flex-col transition-transform duration-300 ease-in-out md:static md:translate-x-0 shrink-0 shadow-xl md:shadow-none"
               id="admin-sidebar-nav">
            
            <!-- Header Sidebar -->
            <div class="p-5 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-white tracking-wide">Panel Admin PPID</h2>
                    <p class="text-xs text-slate-400">Paket Silver &bull; PT Hayat Jaya Teknologi</p>
                </div>
                <!-- Tombol Tutup Mobile -->
                <button type="button" 
                        @click="sidebarOpen = false" 
                        class="md:hidden text-slate-400 hover:text-white p-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-400"
                        aria-label="Tutup menu navigasi admin">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigasi Menu Admin -->
            <nav class="p-3 space-y-1 flex-1 overflow-y-auto admin-nav">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-900/50 text-amber-400 font-semibold border-l-4 border-amber-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" 
                   id="nav-admin-dashboard">
                    <span>📊</span> Dasbor Ringkasan
                </a>
                <a href="#permohonan" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" 
                   id="nav-admin-permohonan">
                    <span>📥</span> Permohonan Informasi
                </a>
                <a href="#keberatan" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" 
                   id="nav-admin-keberatan">
                    <span>⚖️</span> Pengajuan Keberatan
                </a>
                <a href="#dip" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" 
                   id="nav-admin-dip">
                    <span>📚</span> Kelola DIP (Katalog)
                </a>
                <a href="#berita" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" 
                   id="nav-admin-berita">
                    <span>📰</span> Berita & Pengumuman
                </a>
                <a href="{{ route('admin.pengaturan.edit') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.pengaturan.*') ? 'bg-blue-900/50 text-amber-400 font-semibold border-l-4 border-amber-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}" 
                   id="nav-admin-pengaturan">
                    <span>⚙️</span> Pengaturan & Tema Instansi
                </a>
                <a href="#backup" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" 
                   id="nav-admin-backup">
                    <span>💾</span> Cadangan Data (Backup)
                </a>

                <div class="pt-4 mt-4 border-t border-slate-800">
                    <a href="{{ route('home') }}" 
                       target="_blank" 
                       class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-sky-400 hover:text-sky-300 transition-colors" 
                       id="nav-admin-view-site">
                        <span>🌐</span> Buka Halaman Publik &nearr;
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Area Konten Utama -->
        <div class="flex-1 min-w-0 flex flex-col">
            <!-- Topbar Panel Admin -->
            <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3.5 flex items-center justify-between gap-4 sticky top-0 z-30 shadow-2xs">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Tombol Hamburger Mobile -->
                    <button type="button" 
                            @click="sidebarOpen = true" 
                            class="md:hidden p-2 rounded-lg text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-600 cursor-pointer"
                            aria-label="Buka menu navigasi admin">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 truncate">
                        @yield('header_title', 'Dasbor Administrator')
                    </h1>
                </div>

                <div class="flex items-center gap-3 sm:gap-4 shrink-0">
                    <div class="hidden sm:block text-right">
                        <span class="text-xs text-slate-500 block">Masuk sebagai</span>
                        <strong class="text-xs sm:text-sm font-semibold text-slate-900 leading-tight block">
                            {{ auth()->user()->name }}
                        </strong>
                    </div>

                    <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-2xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 cursor-pointer" 
                                id="btn-admin-logout">
                            <span>🚪</span> <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Isi Halaman -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                    <div class="p-4 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-900 text-sm shadow-sm mb-6 flex items-start gap-3" role="alert" id="admin-alert-success">
                        <span class="text-base select-none shrink-0" aria-hidden="true">✅</span>
                        <div class="flex-1 leading-relaxed">
                            <strong class="font-bold block mb-0.5">Berhasil</strong>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="p-4 rounded-xl border border-blue-300 bg-blue-50 text-blue-900 text-sm shadow-sm mb-6 flex items-start gap-3" role="alert" id="admin-alert-info">
                        <span class="text-base select-none shrink-0" aria-hidden="true">ℹ️</span>
                        <div class="flex-1 leading-relaxed">
                            <strong class="font-bold block mb-0.5">Informasi</strong>
                            {{ session('info') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-xl border border-red-300 bg-red-50 text-red-900 text-sm shadow-sm mb-6 flex items-start gap-3" role="alert" id="admin-alert-error">
                        <span class="text-base select-none shrink-0" aria-hidden="true">❌</span>
                        <div class="flex-1 leading-relaxed">
                            <strong class="font-bold block mb-0.5">Kesalahan</strong>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
