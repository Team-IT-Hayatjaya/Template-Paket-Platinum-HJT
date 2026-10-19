<div class="mt-8 pt-5 border-t border-slate-200 text-center flex flex-col items-center gap-3">
    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-50 hover:bg-blue-50 text-xs font-semibold text-[#14304F] hover:text-blue-800 border border-slate-200 hover:border-amber-400 transition-all shadow-xs group">
        <span class="group-hover:-translate-x-0.5 transition-transform text-amber-600 font-bold">&larr;</span>
        <span>Kembali ke Beranda Portal Publik</span>
    </a>

    {{-- Catatan Keamanan Hijau (Green) & Legalitas --}}
    <div class="flex flex-col gap-1 text-[11px] text-slate-500">
        <div class="inline-flex items-center justify-center gap-1.5 font-medium text-emerald-800">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>Akses Dibatasi Khusus Aparatur &amp; Petugas PPID Berwenang</span>
        </div>
        <p class="text-[10px] text-slate-400">
            UU No. 11/2008 (ITE) &bull; UU No. 14/2008 (Keterbukaan Informasi Publik)
        </p>
    </div>

    <div class="pt-1 text-[10px] text-slate-400">
        &copy; {{ date('Y') }} PT Hayat Jaya Teknologi &bull; <span class="text-amber-700 font-bold">Paket Gold Edition</span>
    </div>
</div>
