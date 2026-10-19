@props([
    'pimpinanName' => 'Pimpinan Instansi',
    'pimpinanTitle' => 'Pimpinan Daerah',
    'portraitUrl' => null,
    'totalDesa' => 12,
    'totalPenduduk' => '39.750',
    'periodePenduduk' => 'Data Semester I',
    'indeksKepuasan' => '98,4%',
    'totalLayanan' => 5,
])

<section class="w-full px-margin-mobile lg:px-margin pt-space-lg pb-space-xl bg-gradient-to-b from-surface-container-low/40 to-surface">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
            <!-- Text & Search Column -->
            <div class="lg:col-span-7 flex flex-col gap-space-md">
                <div class="inline-flex items-center gap-space-xs font-label-sm text-label-sm text-secondary uppercase tracking-widest bg-secondary-container/40 px-space-sm py-1 rounded w-fit border border-secondary/20 font-bold">
                    <span class="material-symbols-outlined text-[16px]">verified</span>
                    <span>Pelayanan Terpadu {{ $siteSetting->institution_name ?? 'Kecamatan Loa Kulu' }}</span>
                </div>

                <h1 class="font-headline-xl text-3xl sm:text-4xl lg:text-display-lg text-primary leading-tight font-bold">
                    Pelayanan Publik Berintegritas, Cepat, dan Berkeadaban
                </h1>

                <!-- Kutipan Pimpinan / Sambutan Pimpinan -->
                <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-outline-variant/30">
                    <div class="flex items-center gap-space-xs font-label-sm text-xs text-secondary uppercase tracking-wider font-bold mb-1">
                        <span class="material-symbols-outlined text-[16px]">record_voice_over</span>
                        <span>Sambutan Pimpinan</span>
                    </div>
                    <blockquote class="font-body-md text-body-md text-on-surface-variant italic leading-relaxed">
                        “Komitmen kami adalah menghadirkan birokrasi yang dekat dengan rakyat, bersih dari pungutan liar, serta menjamin hak administrasi setiap warga terpenuhi secara adil dan bermartabat.”
                    </blockquote>
                    <div class="mt-space-xs flex items-center gap-space-sm pt-2 border-t border-outline-variant/20">
                        <span class="font-label-md text-label-md text-primary font-bold">{{ $pimpinanName }}</span>
                        <span class="text-outline-variant">•</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $pimpinanTitle }}</span>
                    </div>
                </div>

                <!-- Quick Service Search Bar -->
                <div class="mt-space-xs bg-surface-container-lowest p-space-xs rounded-xl shadow-md border border-outline-variant/30">
                    <form action="{{ route('berita.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-space-xs">
                        <div class="flex items-center gap-space-xs px-space-sm w-full">
                            <span class="material-symbols-outlined text-outline">search</span>
                            <input 
                                class="w-full bg-transparent font-body-md text-body-md text-on-surface placeholder:text-outline focus:outline-none py-space-xs text-sm" 
                                placeholder="Cari layanan KTP, IUMK, surat domisili, atau perizinan..." 
                                type="text"
                                name="q"
                            />
                        </div>
                        <button class="w-full sm:w-auto bg-primary hover:bg-primary/90 text-on-primary px-space-lg py-space-sm rounded-lg font-label-md text-label-md flex items-center justify-center gap-space-xs transition-colors shrink-0 shadow-sm" type="submit">
                            <span>Telusuri</span>
                            <span class="material-symbols-outlined text-[18px]">east</span>
                        </button>
                    </form>
                </div>

                <!-- Quick Search Tags -->
                <div class="flex items-center gap-space-xs flex-wrap font-body-sm text-body-sm text-on-surface-variant">
                    <span class="font-semibold text-primary">Paling dicari:</span>
                    <a class="bg-surface-container-high px-space-sm py-0.5 rounded text-primary hover:bg-secondary-fixed transition-colors text-xs" href="{{ route('layanan') }}#ktp">KTP Elektronik</a>
                    <a class="bg-surface-container-high px-space-sm py-0.5 rounded text-primary hover:bg-secondary-fixed transition-colors text-xs" href="{{ route('layanan') }}#iumk">Izin Usaha Mikro (IUMK)</a>
                    <a class="bg-surface-container-high px-space-sm py-0.5 rounded text-primary hover:bg-secondary-fixed transition-colors text-xs" href="{{ route('layanan') }}#domisili">Surat Pindah</a>
                    <a class="bg-surface-container-high px-space-sm py-0.5 rounded text-primary hover:bg-secondary-fixed transition-colors text-xs" href="{{ route('layanan') }}#bansos">Status Bansos</a>
                </div>
            </div>

            <!-- Camat / Pimpinan Portrait Column -->
            <div class="lg:col-span-5 relative flex justify-center">
                <div class="relative w-full max-w-sm">
                    <!-- Decorative Backdrop Shape -->
                    <div class="absolute -top-4 -left-4 w-full h-full bg-secondary-fixed-dim/40 rounded-xl"></div>
                    <div class="relative bg-surface-container-lowest rounded-xl overflow-hidden shadow-xl border border-outline-variant/30">
                        <img 
                            class="w-full h-96 object-cover object-top" 
                            alt="Potret resmi Pimpinan {{ $siteSetting->institution_name ?? 'Instansi' }}" 
                            src="{{ $portraitUrl ?: asset('images/camat-portrait.jpg') }}"
                            loading="lazy"
                        />
                        <div class="p-space-md bg-primary text-on-primary">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-title-lg text-title-lg text-secondary-fixed font-bold">{{ $pimpinanName }}</h3>
                                    <p class="font-body-sm text-body-sm text-on-primary-container">{{ $pimpinanTitle }}</p>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-secondary text-on-secondary flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[20px]">account_balance</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Badge Floating -->
                    <div class="absolute -bottom-4 -right-4 bg-surface-container-lowest p-space-sm rounded-lg shadow-lg flex items-center gap-space-sm max-w-xs border border-outline-variant/30">
                        <span class="material-symbols-outlined text-secondary text-[24px]">verified_user</span>
                        <div class="text-left">
                            <div class="font-label-sm text-label-sm text-primary font-bold">Pelayanan Ramah &amp; Bebas Pungli</div>
                            <div class="font-body-sm text-body-sm text-on-surface-variant">Sesuai Perbup Kukar No. 24/2023</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 ANGKA KUNCI KECAMATAN -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-space-md mt-space-xl">
            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex items-center gap-space-md border border-outline-variant/30 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-lg bg-surface-container-high text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">apartment</span>
                </div>
                <div>
                    <div class="font-headline-lg text-2xl font-bold text-primary">{{ $totalDesa }}</div>
                    <div class="font-label-md text-sm text-on-surface font-semibold">Desa &amp; Kelurahan</div>
                    <div class="font-body-sm text-xs text-on-surface-variant">Wilayah Administratif</div>
                </div>
            </div>

            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex items-center gap-space-md border border-outline-variant/30 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-lg bg-surface-container-high text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">groups</span>
                </div>
                <div>
                    <div class="font-headline-lg text-2xl font-bold text-primary">{{ $totalPenduduk }}</div>
                    <div class="font-label-md text-sm text-on-surface font-semibold">Jiwa Penduduk</div>
                    <div class="font-body-sm text-xs text-on-surface-variant">{{ $periodePenduduk }}</div>
                </div>
            </div>

            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex items-center gap-space-md border border-outline-variant/30 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-lg bg-secondary-fixed/50 text-secondary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">thumb_up</span>
                </div>
                <div>
                    <div class="font-headline-lg text-2xl font-bold text-secondary">{{ $indeksKepuasan }}</div>
                    <div class="font-label-md text-sm text-on-surface font-semibold">Indeks Kepuasan</div>
                    <div class="font-body-sm text-xs text-on-surface-variant">Kategori Sangat Baik</div>
                </div>
            </div>

            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex items-center gap-space-md border border-outline-variant/30 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-lg bg-surface-container-high text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">bolt</span>
                </div>
                <div>
                    <div class="font-headline-lg text-2xl font-bold text-primary">{{ $totalLayanan }} Standar</div>
                    <div class="font-label-md text-sm text-on-surface font-semibold">Layanan Publik</div>
                    <div class="font-body-sm text-xs text-on-surface-variant">PATEN Terpadu Cepat</div>
                </div>
            </div>
        </div>
    </div>
</section>
