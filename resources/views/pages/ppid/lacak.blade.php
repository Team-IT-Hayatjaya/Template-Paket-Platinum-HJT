@extends('layouts.app')

@section('title', 'Lacak Permohonan Informasi Publik (PPID) - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))
@section('meta_title', 'Pelacakan Status Permohonan PPID Mandiri Secara Real-Time')
@section('meta_description', 'Lacak progres dan status permohonan informasi publik secara online menggunakan nomor registrasi resmi dan email pemohon sesuai UU No. 14 Tahun 2008.')

@section('content')
<div class="space-y-space-xl max-w-4xl mx-auto px-margin-mobile lg:px-0 py-space-lg">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Informasi Publik / PPID', 'url' => route('ppid.daftar-informasi')],
        ['label' => 'Lacak Resi Permohonan'],
    ]" />

    <!-- Kartu Formulir Pelacakan -->
    <div class="bg-surface-container-lowest rounded-2xl border border-surface-container shadow-2xs overflow-hidden">
        <!-- Header Formulir -->
        <div class="bg-primary text-on-primary p-space-lg sm:p-space-xl relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 opacity-10 pointer-events-none">
                <span class="material-symbols-outlined text-[160px]">travel_explore</span>
            </div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-secondary-fixed text-xs font-bold uppercase tracking-wider mb-2">
                <span class="material-symbols-outlined text-[16px]">track_changes</span>
                <span>Layanan Mandiri PPID Online</span>
            </div>
            <h1 class="font-headline-xl text-2xl sm:text-3xl font-bold tracking-tight text-on-primary">
                Pelacakan Status Permohonan Informasi
            </h1>
            <p class="text-xs sm:text-sm text-on-primary-container max-w-2xl mt-1 leading-relaxed">
                Pantau perkembangan tindak lanjut permohonan informasi publik secara mandiri dan real-time menggunakan nomor registrasi resmi dan alamat email pemohon.
            </p>
        </div>

        <!-- Formulir Pencarian Resi -->
        <form action="{{ route('ppid.lacak') }}" method="POST" class="p-space-lg sm:p-space-xl space-y-4 bg-surface">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nomor_registrasi" class="block font-label-md text-xs font-bold text-primary mb-1">
                        Nomor Registrasi Permohonan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">tag</span>
                        <input type="text"
                               name="nomor_registrasi"
                               id="nomor_registrasi"
                               value="{{ old('nomor_registrasi', $nomor) }}"
                               required
                               placeholder="Contoh: PPID/KEC-LK/2026/0001"
                               class="w-full pl-10 pr-3 py-2.5 bg-surface-container-lowest rounded-xl border border-surface-container text-sm font-mono font-medium focus:ring-2 focus:ring-primary focus:outline-none uppercase">
                    </div>
                    @error('nomor_registrasi')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block font-label-md text-xs font-bold text-primary mb-1">
                        Alamat Email Pemohon <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">mail</span>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', $email) }}"
                               required
                               placeholder="Alamat email saat mendaftar"
                               class="w-full pl-10 pr-3 py-2.5 bg-surface-container-lowest rounded-xl border border-surface-container text-sm font-medium focus:ring-2 focus:ring-primary focus:outline-none">
                    </div>
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 flex-wrap gap-2">
                <span class="text-[11px] text-gray-500 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">lock</span>
                    Verifikasi identitas resi terproteksi rate limiting
                </span>
                <button type="submit"
                        class="px-6 py-2.5 bg-primary hover:bg-primary-container text-on-primary font-bold text-sm rounded-xl transition shadow-xs flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    <span>Lacak Status Resi</span>
                </button>
            </div>
        </form>
    </div>

    @if($searched && $errorMessage)
        <!-- Pesan Data Tidak Ditemukan -->
        <div class="bg-amber-50 border border-amber-300 rounded-2xl p-6 text-amber-950 shadow-xs flex items-start gap-4">
            <span class="material-symbols-outlined text-amber-600 text-[32px] shrink-0 mt-0.5">error_outline</span>
            <div class="space-y-1 flex-1">
                <h3 class="font-bold text-sm text-amber-900">Data Permohonan Tidak Ditemukan</h3>
                <p class="text-xs text-amber-800 leading-relaxed">{{ $errorMessage }}</p>
                <div class="text-[11px] text-amber-700 pt-2 border-t border-amber-200 mt-2">
                    <strong>Petunjuk:</strong> Periksa kembali email konfirmasi yang dikirimkan saat Anda mengajukan permohonan. Pastikan penulisan nomor registrasi mencakup tanda garis miring (<code>/</code>) dan email sama persis dengan yang Anda daftarkan.
                </div>
            </div>
        </div>
    @endif

    @if($searched && $permohonan)
        @php
            $status = $permohonan->status;
            $statusSlug = strtolower($status?->slug ?? 'diterima');
            $sisaHari = $permohonan->sisa_hari_kerja;
            $isFinished = in_array($statusSlug, ['dijawab', 'selesai', 'ditolak']);

            // Nama pemohon disamarkan sebagian untuk privasi
            $namaArr = explode(' ', (string) $permohonan->nama_pemohon);
            $namaMasked = $namaArr[0] . (isset($namaArr[1]) ? ' ' . substr($namaArr[1], 0, 1) . '***' : '');

            // Indeks urutan status aktif (1: Diterima, 2: Diverifikasi, 3: Diproses, 4: Dijawab/Selesai)
            $stepIndex = match($statusSlug) {
                'diterima' => 1,
                'diverifikasi' => 2,
                'diproses' => 3,
                'dijawab', 'selesai', 'ditolak' => 4,
                default => 1,
            };
        @endphp

        <!-- Hasil Pelacakan Permohonan -->
        <div class="bg-surface-container-lowest rounded-2xl border border-surface-container shadow-xs p-space-lg sm:p-space-xl space-y-6">
            <!-- Header Kartu Detail -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-surface-container">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nomor Registrasi:</span>
                        <span class="font-mono text-lg font-black text-primary">{{ $permohonan->nomor_registrasi }}</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        Diajukan pada: <strong class="text-gray-700">{{ $permohonan->created_at->translatedFormat('d F Y, H:i') }} WIB</strong>
                    </div>
                </div>

                <!-- Badge Status Utama -->
                <div class="shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-extrabold tracking-wide uppercase border
                        {{ $statusSlug === 'selesai' || $statusSlug === 'dijawab'
                            ? 'bg-emerald-50 text-emerald-800 border-emerald-300'
                            : ($statusSlug === 'ditolak'
                                ? 'bg-rose-50 text-rose-800 border-rose-300'
                                : 'bg-sky-50 text-sky-800 border-sky-300') }}">
                        <span class="w-2 h-2 rounded-full {{ $isFinished ? 'bg-emerald-500' : 'bg-sky-500 animate-pulse' }}"></span>
                        <span>{{ $status?->nama ?? 'Diproses' }}</span>
                    </span>
                </div>
            </div>

            <!-- Sisa Hari Tenggat Waktu (UU KIP 10 Hari Kerja) -->
            <div class="rounded-xl p-4 border
                {{ $isFinished
                    ? 'bg-slate-50 border-slate-200 text-slate-800'
                    : ($sisaHari > 3
                        ? 'bg-emerald-50/70 border-emerald-200 text-emerald-950'
                        : ($sisaHari >= 1
                            ? 'bg-amber-50/70 border-amber-200 text-amber-950'
                            : 'bg-rose-50/70 border-rose-200 text-rose-950')) }}">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[24px]
                            {{ $isFinished ? 'text-slate-500' : ($sisaHari > 3 ? 'text-emerald-600' : ($sisaHari >= 1 ? 'text-amber-600' : 'text-rose-600')) }}">
                            {{ $isFinished ? 'task_alt' : 'schedule' }}
                        </span>
                        <div>
                            <div class="font-bold text-xs uppercase tracking-wider text-gray-600">Batas Waktu Respons (UU No. 14/2008):</div>
                            <div class="text-sm font-bold mt-0.5">
                                @if($isFinished)
                                    Permohonan Telah Ditanggapi Resmi pada {{ $permohonan->tanggal_dijawab?->translatedFormat('d F Y') ?? $permohonan->updated_at->translatedFormat('d F Y') }}
                                @elseif($sisaHari > 3)
                                    Tersisa {{ $sisaHari }} Hari Kerja Normal (Tenggat: {{ $permohonan->tenggat_jawab?->translatedFormat('d F Y') }})
                                @elseif($sisaHari >= 1)
                                    Mendekati Tenggat: Sisa {{ $sisaHari }} Hari Kerja Lagi (Tenggat: {{ $permohonan->tenggat_jawab?->translatedFormat('d F Y') }})
                                @else
                                    Melewati Batas Tenggat Normal (Tenggat: {{ $permohonan->tenggat_jawab?->translatedFormat('d F Y') }})
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-xs sm:text-right text-gray-500">
                        Standar: 10 Hari Kerja (+ 7 hari perpanjangan)
                    </div>
                </div>
            </div>

            <!-- Linimasa Status (Stepped Timeline) -->
            <div class="space-y-3 pt-2">
                <h4 class="font-title-md text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-primary">timeline</span>
                    <span>Linimasa Tindak Lanjut Permohonan</span>
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <!-- Step 1: Diterima -->
                    <div class="p-3.5 rounded-xl border relative
                        {{ $stepIndex >= 1 ? 'bg-sky-50/40 border-sky-300' : 'bg-gray-50 border-gray-200' }}">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $stepIndex > 1 ? 'bg-emerald-500 text-white' : ($stepIndex === 1 ? 'bg-sky-600 text-white animate-pulse' : 'bg-gray-300 text-gray-700') }}">
                                {{ $stepIndex > 1 ? '✓' : '1' }}
                            </span>
                            <span class="font-bold text-xs text-gray-800">1. Diterima</span>
                        </div>
                        <p class="text-[11px] text-gray-600 leading-tight">Permohonan masuk & tercatat resmi di sistem PPID.</p>
                        <div class="text-[10px] text-gray-400 mt-2 font-mono">{{ $permohonan->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <!-- Step 2: Diverifikasi -->
                    <div class="p-3.5 rounded-xl border relative
                        {{ $stepIndex >= 2 ? 'bg-sky-50/40 border-sky-300' : 'bg-gray-50 border-gray-200 opacity-75' }}">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $stepIndex > 2 ? 'bg-emerald-500 text-white' : ($stepIndex === 2 ? 'bg-sky-600 text-white animate-pulse' : 'bg-gray-300 text-gray-700') }}">
                                {{ $stepIndex > 2 ? '✓' : '2' }}
                            </span>
                            <span class="font-bold text-xs text-gray-800">2. Diverifikasi</span>
                        </div>
                        <p class="text-[11px] text-gray-600 leading-tight">Verifikasi kelengkapan berkas KTP dan rincian informasi.</p>
                    </div>

                    <!-- Step 3: Diproses -->
                    <div class="p-3.5 rounded-xl border relative
                        {{ $stepIndex >= 3 ? 'bg-sky-50/40 border-sky-300' : 'bg-gray-50 border-gray-200 opacity-75' }}">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $stepIndex > 3 ? 'bg-emerald-500 text-white' : ($stepIndex === 3 ? 'bg-sky-600 text-white animate-pulse' : 'bg-gray-300 text-gray-700') }}">
                                {{ $stepIndex > 3 ? '✓' : '3' }}
                            </span>
                            <span class="font-bold text-xs text-gray-800">3. Diproses</span>
                        </div>
                        <p class="text-[11px] text-gray-600 leading-tight">Koordinasi dengan unit perangkat daerah pengampu.</p>
                    </div>

                    <!-- Step 4: Dijawab / Selesai -->
                    <div class="p-3.5 rounded-xl border relative
                        {{ $stepIndex === 4
                            ? ($statusSlug === 'ditolak' ? 'bg-rose-50/40 border-rose-300' : 'bg-emerald-50/40 border-emerald-300')
                            : 'bg-gray-50 border-gray-200 opacity-75' }}">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                {{ $stepIndex === 4
                                    ? ($statusSlug === 'ditolak' ? 'bg-rose-500 text-white' : 'bg-emerald-600 text-white')
                                    : 'bg-gray-300 text-gray-700' }}">
                                {{ $stepIndex === 4 ? ($statusSlug === 'ditolak' ? '✕' : '✓') : '4' }}
                            </span>
                            <span class="font-bold text-xs text-gray-800">
                                {{ $statusSlug === 'ditolak' ? '4. Ditolak' : '4. Selesai' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-600 leading-tight">
                            {{ $statusSlug === 'ditolak' ? 'Tanggapan penolakan resmi diterbitkan.' : 'Jawaban resmi & dokumen diserahkan.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Detail Permohonan & Tanggapan Resmi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div class="bg-surface p-4 rounded-xl border border-surface-container space-y-2">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Informasi yang Dimohon:</span>
                    <p class="text-xs text-gray-800 leading-relaxed font-medium">"{{ $permohonan->rincian_informasi }}"</p>
                    <div class="text-[11px] text-gray-500 pt-2 border-t border-gray-200">
                        <strong>Tujuan Penggunaan:</strong> {{ $permohonan->tujuan_penggunaan }}
                    </div>
                </div>

                <div class="bg-surface p-4 rounded-xl border border-surface-container space-y-2">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Pemohon:</span>
                    <div class="text-xs text-gray-800 font-bold">{{ $namaMasked }}</div>
                    <div class="text-[11px] text-gray-500">Kategori: <strong class="capitalize">{{ $permohonan->kategori_pemohon ?? 'Perorangan' }}</strong></div>
                    <div class="text-[11px] text-gray-500">Bentuk Salinan: <strong class="capitalize">{{ $permohonan->cara_memperoleh_informasi }} (via {{ $permohonan->cara_mendapat_salinan }})</strong></div>
                </div>
            </div>

            <!-- Hasil Jawaban Resmi Petugas PPID (Jika Sudah Dijawab) -->
            @if(!empty($permohonan->jawaban_teks) || !empty($permohonan->jawaban_file_path))
                <div class="bg-emerald-50/60 border border-emerald-300 rounded-2xl p-5 space-y-4">
                    <div class="flex items-center gap-2 text-emerald-900 font-bold text-sm">
                        <span class="material-symbols-outlined text-[20px] text-emerald-600">verified</span>
                        <span>Tanggapan Tertulis Resmi Pejabat PPID</span>
                    </div>

                    @if(!empty($permohonan->jawaban_teks))
                        <div class="bg-white p-4 rounded-xl border border-emerald-200 text-xs text-gray-800 leading-relaxed">
                            {!! nl2br(e($permohonan->jawaban_teks)) !!}
                        </div>
                    @endif

                    <!-- Tombol Unduh Jawaban Lewat Tautan Bertanda Tangan (Signed URL) -->
                    @if(!empty($signedDownloadUrl))
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                            <div class="text-[11px] text-emerald-800">
                                <strong>Berkas Dokumen Resmi:</strong> Dokumen jawaban telah dilampirkan oleh petugas PPID.
                            </div>
                            <a href="{{ $signedDownloadUrl }}"
                               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shadow-xs">
                                <span class="material-symbols-outlined text-[18px]">download</span>
                                <span>Unduh Berkas Jawaban (PDF)</span>
                            </a>
                        </div>
                        <p class="text-[10px] text-emerald-700 mt-1">
                            * Tautan unduhan dilindungi tanda tangan kriptografis digital resmi (Signed URL) dan berlaku selama 24 jam.
                        </p>
                    @endif
                </div>
            @endif

            <!-- Edukasi Hak Keberatan Informasi Publik -->
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 text-xs text-gray-600 flex items-start gap-3">
                <span class="material-symbols-outlined text-[20px] text-gray-500 shrink-0 mt-0.5">gavel</span>
                <div class="space-y-1">
                    <strong class="text-gray-800">Hak Pengajuan Keberatan Informasi Publik (Pasal 35 UU KIP):</strong>
                    <p class="text-[11px] text-gray-600 leading-relaxed">
                        Apabila permohonan Anda ditolak, tidak ditanggapi dalam kurun waktu 10+7 hari kerja, dikenakan biaya tidak wajar, atau tidak dipenuhi sebagaimana mestinya, Anda berhak mengajukan keberatan tertulis kepada Atasan PPID paling lambat 30 (tiga puluh) hari kerja sejak diterimanya tanggapan atau berakhirnya tenggat waktu.
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(!$searched)
        <!-- Panduan Bantuan Pelacakan -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-container shadow-2xs space-y-2">
                <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">mark_email_read</span>
                </div>
                <h4 class="font-title-md text-sm font-bold text-primary">1. Cek Email Konfirmasi</h4>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Nomor registrasi unik diterbitkan otomatis dan dikirimkan ke email Anda sesaat setelah formulir diajukan.
                </p>
            </div>

            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-container shadow-2xs space-y-2">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">hourglass_top</span>
                </div>
                <h4 class="font-title-md text-sm font-bold text-primary">2. Tenggat 10 Hari Kerja</h4>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Sesuai amanat UU KIP, PPID wajib memberikan tanggapan resmi maksimal dalam 10 hari kerja (+ perpanjangan 7 hari).
                </p>
            </div>

            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-container shadow-2xs space-y-2">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">download_for_offline</span>
                </div>
                <h4 class="font-title-md text-sm font-bold text-primary">3. Unduh Jawaban Resmi</h4>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Saat permohonan selesai diproses, dokumen jawaban resmi dapat diunduh langsung lewat tautan bertanda tangan aman.
                </p>
            </div>
        </div>
    @endif
</div>
@endsection
