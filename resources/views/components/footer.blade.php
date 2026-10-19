<footer class="w-full bg-primary-container text-on-primary pt-space-xl pb-space-lg mt-auto border-t-2 border-secondary">
    <div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-space-xl pb-space-xl">
            <!-- Kolom 1: Profil & Identitas Instansi -->
            <div class="space-y-space-md">
                <div class="flex items-center gap-space-sm">
                    @if(!empty($siteSetting->logo_path))
                        <img src="{{ asset('storage/' . $siteSetting->logo_path) }}" alt="Logo {{ $siteSetting->institution_name }}" class="h-10 w-auto object-contain shrink-0" />
                    @elseif(file_exists(public_path('images/logo-kukar.png')))
                        <img src="{{ asset('images/logo-kukar.png') }}" alt="Lambang Daerah" class="h-10 w-10 object-contain shrink-0 rounded-full bg-white/10 p-0.5" />
                    @else
                        <div class="w-10 h-10 rounded-lg bg-secondary flex items-center justify-center text-primary font-bold shrink-0">
                            <span class="material-symbols-outlined text-[24px]">account_balance</span>
                        </div>
                    @endif
                    <div class="flex flex-col min-w-0">
                        <span class="font-headline-md text-headline-md text-on-primary leading-tight truncate">
                            {{ $siteSetting->institution_name ?? 'Kecamatan Loa Kulu' }}
                        </span>
                        <span class="font-label-sm text-label-sm text-secondary-fixed">
                            Pemerintah Republik Indonesia
                        </span>
                    </div>
                </div>
                <p class="font-body-sm text-body-sm text-on-primary-container leading-relaxed">
                    Penyelenggaraan tata kelola pemerintahan dan pelayanan masyarakat yang akuntabel, transparan, dan berkeadaban sesuai amanat UU No. 14 Tahun 2008.
                </p>
                <div class="flex items-center gap-space-sm pt-space-xs">
                    <span class="font-label-sm text-label-sm bg-primary px-space-sm py-space-xs rounded text-secondary-fixed border border-secondary/30">
                        Portal Resmi Terverifikasi
                    </span>
                </div>
            </div>

            <!-- Kolom 2: Kantor & Jam Kerja -->
            <div class="space-y-space-md">
                <h3 class="font-title-lg text-title-lg text-secondary-fixed border-b border-on-primary-container/20 pb-space-xs">
                    Kantor &amp; Jam Layanan
                </h3>
                <div class="space-y-space-sm font-body-sm text-body-sm text-on-primary-container">
                    <p class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[18px] text-secondary-fixed shrink-0">location_on</span>
                        <span>{{ $siteSetting->address ?? 'Jl. Yos Sudarso No. 1, Loa Kulu, Kab. Kutai Kartanegara, Kalimantan Timur' }}</span>
                    </p>
                    <p class="flex items-start gap-space-xs">
                        <span class="material-symbols-outlined text-[18px] text-secondary-fixed shrink-0">schedule</span>
                        <span>{!! nl2br(e($siteSetting->service_hours ?? "Senin - Kamis: 08.00 - 16.00 WITA\nJumat: 08.00 - 15.30 WITA")) !!}</span>
                    </p>
                    @if(!empty($siteSetting->phone))
                        <p class="flex items-start gap-space-xs">
                            <span class="material-symbols-outlined text-[18px] text-secondary-fixed shrink-0">call</span>
                            <span>{{ $siteSetting->phone }}</span>
                        </p>
                    @endif
                    @if(!empty($siteSetting->email))
                        <p class="flex items-start gap-space-xs">
                            <span class="material-symbols-outlined text-[18px] text-secondary-fixed shrink-0">mail</span>
                            <span>{{ $siteSetting->email }}</span>
                        </p>
                    @endif
                </div>
            </div>

            <!-- Kolom 3: Layanan Cepat -->
            <div class="space-y-space-md">
                <h3 class="font-title-lg text-title-lg text-secondary-fixed border-b border-on-primary-container/20 pb-space-xs">
                    Akses &amp; Layanan Cepat
                </h3>
                <ul class="space-y-space-xs font-body-sm text-body-sm">
                    <li>
                        <a href="{{ route('layanan') }}" class="flex items-center gap-space-xs text-on-primary-container hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            <span>Pelayanan Administrasi Terpadu (PATEN)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('ppid.daftar-informasi') }}" class="flex items-center gap-space-xs text-on-primary-container hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            <span>Daftar Informasi Publik (DIP / PPID)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('berita.index') }}" class="flex items-center gap-space-xs text-on-primary-container hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            <span>Warta Pembangunan &amp; Agenda Daerah</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('kontak') }}" class="flex items-center gap-space-xs text-on-primary-container hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            <span>Meja Pengaduan &amp; Aspirasi Warga</span>
                        </a>
                    </li>
                    <li>
                        <a href="https://lapor.go.id" target="_blank" rel="noopener" class="flex items-center gap-space-xs text-on-primary-container hover:text-on-primary transition-colors">
                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                            <span>Kanal Nasional SP4N LAPOR!</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Kolom 4: Media Sosial & Ekosistem Digital -->
            <div class="space-y-space-md">
                <h3 class="font-title-lg text-title-lg text-secondary-fixed border-b border-on-primary-container/20 pb-space-xs">
                    Media Sosial &amp; Jejaring
                </h3>
                <p class="font-body-sm text-body-sm text-on-primary-container">
                    Ikuti kanal komunikasi resmi instansi untuk berita terkini dan pengumuman darurat:
                </p>
                <div class="flex items-center gap-2 flex-wrap">
                    @php $social = $siteSetting->social_media ?? []; @endphp
                    @if(!empty($social['facebook']))
                        <a href="{{ $social['facebook'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-primary hover:bg-secondary text-on-primary hover:text-on-secondary flex items-center justify-center transition-colors shadow-sm" aria-label="Facebook">
                            <span class="text-xs font-bold">FB</span>
                        </a>
                    @endif
                    @if(!empty($social['instagram']))
                        <a href="{{ $social['instagram'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-primary hover:bg-secondary text-on-primary hover:text-on-secondary flex items-center justify-center transition-colors shadow-sm" aria-label="Instagram">
                            <span class="text-xs font-bold">IG</span>
                        </a>
                    @endif
                    @if(!empty($social['twitter']))
                        <a href="{{ $social['twitter'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-primary hover:bg-secondary text-on-primary hover:text-on-secondary flex items-center justify-center transition-colors shadow-sm" aria-label="Twitter">
                            <span class="text-xs font-bold">X</span>
                        </a>
                    @endif
                    @if(!empty($social['youtube']))
                        <a href="{{ $social['youtube'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-lg bg-primary hover:bg-secondary text-on-primary hover:text-on-secondary flex items-center justify-center transition-colors shadow-sm" aria-label="YouTube">
                            <span class="text-xs font-bold">YT</span>
                        </a>
                    @endif
                </div>
                <div class="pt-2">
                    <a href="{{ url('/admin') }}" class="inline-flex items-center gap-1.5 text-xs text-secondary-fixed hover:underline">
                        <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span>
                        <span>Portal Administrasi Masuk</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="h-[1px] w-full bg-on-primary-container/20 my-space-md"></div>

        <div class="flex flex-col md:flex-row items-center justify-between gap-space-sm text-center md:text-left font-body-sm text-body-sm text-on-primary-container">
            <div>
                &copy; {{ date('Y') }} {{ $siteSetting->institution_name ?? 'Pemerintah Kecamatan Loa Kulu' }}. Hak Cipta Dilindungi Undang-Undang.
            </div>
            <div class="flex items-center gap-space-md">
                <span class="font-label-sm text-label-sm text-secondary-fixed">
                    Standar Portal Resmi Pemerintah Republik Indonesia &bull; PT Hayat Jaya Teknologi
                </span>
            </div>
        </div>
    </div>
</footer>
