@php
    $siteSetting = \App\Models\SiteSetting::current();
    $institutionName = $siteSetting->institution_name ?? 'Pemerintah Kabupaten Kutai Kartanegara';
@endphp

<div class="text-center mb-6 flex flex-col items-center">
    {{-- Emblem Instansi Berbingkai Emas (Gold) & Putih (White) --}}
    <div class="mb-4 relative group">
        <div class="absolute -inset-1.5 bg-gradient-to-r from-amber-300 via-amber-400 to-yellow-300 rounded-3xl blur-sm opacity-60 group-hover:opacity-80 transition duration-300"></div>
        <div class="relative inline-flex items-center justify-center p-3 rounded-2xl bg-white border-2 border-amber-400 shadow-md">
            @if(!empty($siteSetting->logo_path))
                <img src="{{ asset('storage/' . $siteSetting->logo_path) }}" alt="Logo Instansi" class="h-14 w-auto object-contain drop-shadow-sm" />
            @else
                <div class="w-12 h-12 rounded-xl bg-blue-50 border border-amber-300 flex items-center justify-center text-3xl">
                    🏛️
                </div>
            @endif
        </div>
    </div>

    {{-- Badge Paket Gold (Emas & Putih) --}}
    <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-extrabold tracking-wider uppercase bg-gradient-to-r from-amber-50 via-yellow-100 to-amber-50 text-amber-900 border border-amber-400 shadow-xs mb-3">
        <span class="text-amber-500 text-sm leading-none select-none">⭐</span>
        <span class="font-extrabold text-amber-900 tracking-wide">PAKET GOLD &bull; PANEL KENDALI</span>
    </div>

    {{-- Nama Instansi: Biru Navy Pemerintah (Blue) --}}
    <h1 class="text-xl font-extrabold tracking-tight text-[#14304F] sm:text-2xl">
        {{ $institutionName }}
    </h1>

    <p class="text-xs text-slate-600 mt-1.5 max-w-sm leading-relaxed">
        Sistem Informasi Manajemen Terpadu &amp; Pelayanan Keterbukaan Informasi Publik (PPID)
    </p>

    {{-- Indikator Keamanan Hijau (Green) --}}
    <div class="mt-3.5 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-300 text-emerald-800 text-[11px] font-semibold shadow-2xs">
        <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
        </span>
        <span>Autentikasi Terenkripsi &bull; Audit Trail Aktif</span>
    </div>
</div>
