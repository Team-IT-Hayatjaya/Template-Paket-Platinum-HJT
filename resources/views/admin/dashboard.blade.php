@extends('layouts.admin')

@section('title', 'Dasbor Utama')
@section('header_title', 'Ringkasan Statistik & Operasional')

@section('content')
    <!-- Kartu Statistik Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
        <!-- Total Permohonan -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Permohonan Informasi</span>
                <span class="text-xl" aria-hidden="true">📥</span>
            </div>
            <div class="text-3xl font-extrabold text-blue-950" id="stat-total-permohonan">
                {{ $stats['total_permohonan'] }}
            </div>
            <div class="text-xs text-emerald-600 font-medium mt-1">
                Menunggu respon PPID
            </div>
        </div>

        <!-- Total Keberatan -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Pengajuan Keberatan</span>
                <span class="text-xl" aria-hidden="true">⚖️</span>
            </div>
            <div class="text-3xl font-extrabold text-red-700" id="stat-total-keberatan">
                {{ $stats['total_keberatan'] }}
            </div>
            <div class="text-xs text-slate-500 font-medium mt-1">
                Sengketa / tanggapan
            </div>
        </div>

        <!-- Total DIP -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Katalog DIP Aktif</span>
                <span class="text-xl" aria-hidden="true">📚</span>
            </div>
            <div class="text-3xl font-extrabold text-amber-600" id="stat-total-dip">
                {{ $stats['total_dip'] }}
            </div>
            <div class="text-xs text-slate-500 font-medium mt-1">
                Informasi berkala & setiap saat
            </div>
        </div>

        <!-- Status Backup & Cron -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Cadangan Bulanan</span>
                <span class="text-xl" aria-hidden="true">💾</span>
            </div>
            <div class="text-xl font-bold text-emerald-600 my-1" id="stat-status-backup">
                Siap (Cron Aktif)
            </div>
            <div class="text-xs text-slate-500">
                Tugas: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-700 font-mono text-[11px]">schedule:run</code>
            </div>
        </div>
    </div>

    <!-- Informasi Konfigurasi Lingkungan (Paket Silver) -->
    <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-7 shadow-sm">
        <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-1">Status Spesifikasi Sistem (Paket Silver)</h2>
        <p class="text-xs sm:text-sm text-slate-500 mb-5 leading-relaxed">
            Konfigurasi sistem disesuaikan untuk efisiensi cloud hosting standar / VPS kecil tanpa ketergantungan layanan berat eksternal (Redis / Docker).
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <span class="text-xs text-slate-500 block">Modul PPID Regulasi:</span>
                <strong class="text-sm text-slate-900 font-bold block mt-0.5">UU No. 14 Tahun 2008</strong>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <span class="text-xs text-slate-500 block">Driver Antrean (Queue):</span>
                <strong class="text-sm text-slate-900 font-bold block mt-0.5">Database / Sync</strong>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <span class="text-xs text-slate-500 block">Penyedia Pengembang:</span>
                <strong class="text-sm text-slate-900 font-bold block mt-0.5">PT Hayat Jaya Teknologi (HJT)</strong>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <span class="text-xs text-slate-500 block">Tingkat Layanan:</span>
                <strong class="text-sm text-amber-700 font-bold block mt-0.5">SILVER</strong>
            </div>
        </div>
    </div>
@endsection
