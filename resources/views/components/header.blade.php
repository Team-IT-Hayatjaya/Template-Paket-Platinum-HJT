<header class="w-full z-50 shadow-[0_2px_8px_rgba(20,48,79,0.12)] sticky top-0" x-data="{ mobileOpen: false }">
    <!-- Main Header Instansi: Lambang Daerah & Identitas -->
    <div class="bg-primary text-on-primary">
        <div class="h-20 max-w-7xl mx-auto px-margin-mobile lg:px-margin flex items-center justify-between gap-4">
            <!-- Brand & Lambang -->
            <a href="{{ route('home') }}" class="flex items-center gap-space-md text-inherit no-underline min-w-0">
                @if(!empty($siteSetting->logo_path))
                    <img src="{{ asset('storage/' . $siteSetting->logo_path) }}" alt="Logo {{ $siteSetting->institution_name }}" class="h-12 w-auto object-contain shrink-0" />
                @elseif(file_exists(public_path('images/logo-kukar.png')))
                    <img src="{{ asset('images/logo-kukar.png') }}" alt="Lambang Kabupaten Kutai Kartanegara" class="h-12 w-12 object-contain shrink-0 rounded-full bg-white/10 p-0.5" />
                @else
                    <div class="w-11 h-11 rounded-lg bg-secondary flex items-center justify-center text-primary font-bold shadow-inner shrink-0">
                        <span class="material-symbols-outlined text-[28px] text-primary">account_balance</span>
                    </div>
                @endif
                <div class="flex flex-col min-w-0">
                    <span class="font-label-sm text-[11px] tracking-wider uppercase text-secondary-fixed truncate">
                        Pemerintah Daerah Republik Indonesia
                    </span>
                    <span class="font-headline-md text-headline-md tracking-tight leading-tight text-on-primary truncate">
                        {{ $siteSetting->institution_name ?? 'Kecamatan Loa Kulu' }}
                    </span>
                    <span class="font-body-sm text-[12px] text-on-primary-container hidden sm:block truncate">
                        Portal Pelayanan &amp; Informasi Publik Terpadu
                    </span>
                </div>
            </a>

            <!-- Right Controls: Quick Search & Mobile Toggle -->
            <div class="flex items-center gap-space-sm shrink-0">
                <form action="{{ route('berita.index') }}" method="GET" class="hidden md:flex items-center relative">
                    <input 
                        type="search" 
                        name="q" 
                        placeholder="Cari layanan / berita..." 
                        class="bg-primary-container/80 text-on-primary placeholder:text-on-primary-container/70 text-xs rounded-lg pl-8 pr-3 py-2 border border-white/10 focus:outline-none focus:ring-2 focus:ring-secondary w-44 lg:w-60"
                    />
                    <span class="material-symbols-outlined text-[18px] text-secondary-fixed absolute left-2 pointer-events-none">search</span>
                </form>

                <!-- Mobile Menu Button -->
                <button 
                    @click="mobileOpen = !mobileOpen" 
                    type="button" 
                    class="lg:hidden p-2 rounded-lg bg-primary-container text-on-primary hover:bg-secondary hover:text-on-secondary transition-colors"
                    aria-label="Buka Menu Navigasi"
                >
                    <span class="material-symbols-outlined text-[24px]" x-show="!mobileOpen">menu</span>
                    <span class="material-symbols-outlined text-[24px]" x-show="mobileOpen" x-cloak>close</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation Bar Desktop -->
    <nav id="main-navigation" class="bg-surface-container-lowest shadow-[0_1px_4px_rgba(20,48,79,0.06)] hidden lg:block border-b border-outline-variant/30" aria-label="Navigasi Utama">
        <div class="max-w-7xl mx-auto px-margin flex items-center justify-between">
            <div class="flex items-center gap-space-lg py-2 flex-wrap">
                @if(isset($navigationMenus) && $navigationMenus->isNotEmpty())
                    @foreach($navigationMenus as $nav)
                        @php
                            $currentUrl = request()->url();
                            $navPath = trim($nav->url, '/');
                            $isNavActive = ($currentUrl === url($nav->url) || ($navPath !== '' && request()->is($navPath . '*')));

                            if (!$isNavActive && $nav->children->isNotEmpty()) {
                                foreach ($nav->children as $childItem) {
                                    $childPath = trim($childItem->url, '/');
                                    if ($currentUrl === url($childItem->url) || ($childPath !== '' && request()->is($childPath . '*'))) {
                                        $isNavActive = true;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        @if($nav->children->isNotEmpty())
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" @click.outside="open = false" type="button" class="py-2 text-sm font-semibold flex items-center gap-1 transition-colors {{ $isNavActive ? 'text-secondary border-b-2 border-secondary font-bold' : 'text-on-surface-variant hover:text-primary' }}">
                                    <span>{{ $nav->nama }}</span>
                                    <span class="material-symbols-outlined text-[16px]">expand_more</span>
                                </button>
                                <div x-show="open" x-cloak class="absolute left-0 mt-1 w-56 bg-surface-container-lowest rounded-xl shadow-lg border border-outline-variant/30 py-1.5 z-50">
                                    @foreach($nav->children as $child)
                                        @php
                                            $childPath = trim($child->url, '/');
                                            $isChildActive = ($currentUrl === url($child->url) || ($childPath !== '' && request()->is($childPath . '*')));
                                        @endphp
                                        <a href="{{ $child->url }}" target="{{ $child->target }}" class="block px-4 py-2 text-xs font-medium transition-colors {{ $isChildActive ? 'bg-secondary/15 text-secondary font-bold' : 'text-on-surface hover:bg-surface-container-low hover:text-primary' }}">
                                            {{ $child->nama }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $nav->url }}" target="{{ $nav->target }}" class="py-2 text-sm font-semibold transition-colors {{ $isNavActive ? 'text-secondary border-b-2 border-secondary font-bold' : 'text-on-surface-variant hover:text-primary' }}">
                                {{ $nav->nama }}
                            </a>
                        @endif
                    @endforeach
                @else
                    <a href="{{ route('home') }}" class="py-2 text-sm font-semibold transition-colors {{ request()->routeIs('home') ? 'text-secondary border-b-2 border-secondary' : 'text-on-surface-variant hover:text-primary' }}">
                        Beranda
                    </a>
                    <a href="{{ route('profil') }}" class="py-2 text-sm font-semibold transition-colors {{ request()->routeIs('profil') ? 'text-secondary border-b-2 border-secondary' : 'text-on-surface-variant hover:text-primary' }}">
                        Profil &amp; Pemerintahan
                    </a>
                    <a href="{{ route('layanan') }}" class="py-2 text-sm font-semibold transition-colors {{ request()->routeIs('layanan') ? 'text-secondary border-b-2 border-secondary' : 'text-on-surface-variant hover:text-primary' }}">
                        Layanan Publik (PATEN)
                    </a>
                    <a href="{{ route('ppid.daftar-informasi') }}" class="py-2 text-sm font-semibold transition-colors {{ (request()->routeIs('ppid.*') || request()->routeIs('publikasi')) ? 'text-secondary border-b-2 border-secondary' : 'text-on-surface-variant hover:text-primary' }}">
                        Informasi Publik / PPID
                    </a>
                    <a href="{{ route('berita.index') }}" class="py-2 text-sm font-semibold transition-colors {{ request()->routeIs('berita.*') ? 'text-secondary border-b-2 border-secondary' : 'text-on-surface-variant hover:text-primary' }}">
                        Publikasi &amp; Berita
                    </a>
                    <a href="{{ route('galeri') }}" class="py-2 text-sm font-semibold transition-colors {{ request()->routeIs('galeri') ? 'text-secondary border-b-2 border-secondary' : 'text-on-surface-variant hover:text-primary' }}">
                        Galeri
                    </a>
                    <a href="{{ route('kontak') }}" class="py-2 text-sm font-semibold transition-colors {{ request()->routeIs('kontak') ? 'text-secondary border-b-2 border-secondary' : 'text-on-surface-variant hover:text-primary' }}">
                        Kontak &amp; Lokasi
                    </a>
                @endif
            </div>
            
            @if(\App\Support\Package::has('ppid_tracking'))
            <div class="flex items-center gap-2">
                <a href="{{ route('ppid.lacak') }}" class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg bg-surface-container-low text-primary hover:bg-secondary hover:text-on-secondary transition-colors">
                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                    <span>Lacak Dokumen</span>
                </a>
            </div>
            @endif
        </div>
        <div class="h-[2px] w-full bg-secondary-fixed-dim"></div>
    </nav>

    <!-- Mobile Navigation Drawer -->
    <div 
        x-show="mobileOpen" 
        x-cloak 
        @click.outside="mobileOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="lg:hidden bg-surface-container-lowest border-b border-outline-variant/40 shadow-xl px-margin-mobile py-space-md"
    >
        <div class="flex flex-col gap-1">
            @if(isset($navigationMenus) && $navigationMenus->isNotEmpty())
                @foreach($navigationMenus as $nav)
                    @php
                        $currentUrl = request()->url();
                        $navPath = trim($nav->url, '/');
                        $isNavActive = ($currentUrl === url($nav->url) || ($navPath !== '' && request()->is($navPath . '*')));

                        if (!$isNavActive && $nav->children->isNotEmpty()) {
                            foreach ($nav->children as $childItem) {
                                $childPath = trim($childItem->url, '/');
                                if ($currentUrl === url($childItem->url) || ($childPath !== '' && request()->is($childPath . '*'))) {
                                    $isNavActive = true;
                                    break;
                                }
                            }
                        }
                    @endphp
                    @if($nav->children->isNotEmpty())
                        <div x-data="{ subOpen: {{ $isNavActive ? 'true' : 'false' }} }">
                            <button @click="subOpen = !subOpen" type="button" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-semibold text-sm transition-colors {{ $isNavActive ? 'bg-secondary/15 text-secondary font-bold' : 'text-on-surface hover:bg-surface-container-low' }}">
                                <span>{{ $nav->nama }}</span>
                                <span class="material-symbols-outlined text-[18px]" :class="{ 'rotate-180': subOpen }">expand_more</span>
                            </button>
                            <div x-show="subOpen" class="pl-4 flex flex-col gap-1 py-1">
                                @foreach($nav->children as $child)
                                    @php
                                        $childPath = trim($child->url, '/');
                                        $isChildActive = ($currentUrl === url($child->url) || ($childPath !== '' && request()->is($childPath . '*')));
                                    @endphp
                                    <a href="{{ $child->url }}" target="{{ $child->target }}" @click="mobileOpen = false" class="px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ $isChildActive ? 'bg-secondary/20 text-secondary font-bold' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                                        {{ $child->nama }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $nav->url }}" target="{{ $nav->target }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-lg font-semibold text-sm transition-colors {{ $isNavActive ? 'bg-secondary/15 text-secondary font-bold' : 'text-on-surface hover:bg-surface-container-low' }}">
                            {{ $nav->nama }}
                        </a>
                    @endif
                @endforeach
            @else
                <a href="{{ route('home') }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-lg font-semibold text-sm {{ request()->routeIs('home') ? 'bg-secondary/10 text-secondary' : 'text-on-surface hover:bg-surface-container-low' }}">
                    🏠 Beranda
                </a>
                <a href="{{ route('profil') }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-lg font-semibold text-sm {{ request()->routeIs('profil') ? 'bg-secondary/10 text-secondary' : 'text-on-surface hover:bg-surface-container-low' }}">
                    🏛️ Profil &amp; Pemerintahan
                </a>
                <a href="{{ route('layanan') }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-lg font-semibold text-sm {{ request()->routeIs('layanan') ? 'bg-secondary/10 text-secondary' : 'text-on-surface hover:bg-surface-container-low' }}">
                    ⚡ Layanan Publik (PATEN)
                </a>
                <a href="{{ route('ppid.daftar-informasi') }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-lg font-semibold text-sm {{ (request()->routeIs('ppid.*') || request()->routeIs('publikasi')) ? 'bg-secondary/10 text-secondary' : 'text-on-surface hover:bg-surface-container-low' }}">
                    📄 Informasi Publik / PPID
                </a>
                <a href="{{ route('berita.index') }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-lg font-semibold text-sm {{ request()->routeIs('berita.*') ? 'bg-secondary/10 text-secondary' : 'text-on-surface hover:bg-surface-container-low' }}">
                    📰 Publikasi &amp; Berita
                </a>
                <a href="{{ route('galeri') }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-lg font-semibold text-sm {{ request()->routeIs('galeri') ? 'bg-secondary/10 text-secondary' : 'text-on-surface hover:bg-surface-container-low' }}">
                    📸 Galeri Kegiatan
                </a>
                <a href="{{ route('kontak') }}" @click="mobileOpen = false" class="px-3 py-2.5 rounded-lg font-semibold text-sm {{ request()->routeIs('kontak') ? 'bg-secondary/10 text-secondary' : 'text-on-surface hover:bg-surface-container-low' }}">
                    📍 Kontak &amp; Lokasi
                </a>
            @endif
        </div>
    </div>
</header>
