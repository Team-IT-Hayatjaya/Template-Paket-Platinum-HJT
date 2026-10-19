@extends('layouts.app')

@section('title', 'Formulir Permohonan Informasi Publik (PPID) - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))
@section('meta_title', 'Formulir Permohonan Informasi Publik Sesuai UU No. 14 Tahun 2008')
@section('meta_description', 'Ajukan permohonan informasi publik secara resmi kepada PPID. Dilengkapi nomor registrasi resmi dan perlindungan data pribadi.')

@section('content')
<div class="space-y-space-xl max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Informasi Publik / PPID', 'url' => route('ppid.daftar-informasi')],
        ['label' => 'Formulir Permohonan Informasi'],
    ]" />

    <!-- Notifikasi Berhasil -->
    @if(session('nomor_registrasi'))
        <div class="bg-emerald-50 border-2 border-emerald-500 rounded-2xl p-6 text-emerald-950 shadow-xs space-y-3">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[36px] text-emerald-600">check_circle</span>
                <div>
                    <h3 class="font-headline-md text-lg font-bold text-emerald-900">Permohonan Berhasil Diregistrasi!</h3>
                    <p class="text-xs text-emerald-700">Tanda bukti registrasi telah diterbitkan secara resmi.</p>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-emerald-200">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Nomor Registrasi Unik Anda:</p>
                <div class="font-mono text-2xl font-black text-primary">{{ session('nomor_registrasi') }}</div>
                <p class="text-xs text-gray-600 mt-2">
                    Bukti konfirmasi lengkap beserta tenggat respons 10 hari kerja telah dikirimkan ke email Anda. Mohon simpan nomor ini untuk keperluan tindak lanjut resmi.
                </p>
                @if(\App\Support\Package::has('ppid_tracking'))
                    <div class="mt-3 pt-3 border-t border-emerald-100 flex items-center justify-between flex-wrap gap-2">
                        <span class="text-xs text-emerald-800">Pantau proses tindak lanjut permohonan secara real-time:</span>
                        <a href="{{ route('ppid.lacak', ['nomor_registrasi' => session('nomor_registrasi')]) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-container text-on-primary text-xs font-bold rounded-xl transition shadow-xs">
                            <span class="material-symbols-outlined text-[16px]">travel_explore</span>
                            <span>Lacak Status Resi Sekarang &rarr;</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Kartu Formulir Permohonan -->
    <div class="bg-surface-container-lowest rounded-2xl border border-surface-container shadow-2xs overflow-hidden">
        <!-- Header Formulir -->
        <div class="bg-primary text-on-primary p-space-lg sm:p-space-xl">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-secondary-fixed text-xs font-bold uppercase tracking-wider mb-2">
                <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
                <span>Layanan Mandiri PPID Online</span>
            </div>
            <h1 class="font-headline-xl text-2xl sm:text-3xl font-bold tracking-tight text-on-primary">
                Formulir Permohonan Informasi Publik
            </h1>
            <p class="text-xs sm:text-sm text-on-primary-container max-w-2xl mt-1 leading-relaxed">
                Sesuai amanat Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik. Permohonan akan diproses maksimal 10 (sepuluh) hari kerja.
            </p>
        </div>

        <form action="{{ route('ppid.kirim-permohonan') }}" method="POST" enctype="multipart/form-data" class="p-space-lg sm:p-space-xl space-y-space-lg">
            @csrf

            <!-- Bagian 1: Identitas Pemohon -->
            <div class="space-y-4">
                <div class="border-b border-surface-container pb-2">
                    <h3 class="font-title-lg text-base font-bold text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-secondary">person</span>
                        <span>1. Data Identitas Pemohon</span>
                    </h3>
                    <p class="text-xs text-on-surface-variant">Lengkapi data diri pemohon perseorangan atau perwakilan badan publik.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="nama_pemohon" class="block font-label-md text-xs font-bold text-primary mb-1">
                            Nama Lengkap Sesuai KTP <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_pemohon" id="nama_pemohon" value="{{ old('nama_pemohon') }}" required placeholder="Contoh: Budi Santoso"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-surface-container bg-surface-container-low text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('nama_pemohon') border-red-500 @enderror">
                        @error('nama_pemohon')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nik" class="block font-label-md text-xs font-bold text-primary mb-1">
                            NIK KTP (Opsional)
                        </label>
                        <input type="text" name="nik" id="nik" value="{{ old('nik') }}" maxlength="20" placeholder="16 digit angka KTP"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-surface-container bg-surface-container-low text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('nik') border-red-500 @enderror">
                        <p class="text-[11px] text-on-surface-variant mt-0.5">Disimpan terenkripsi untuk perlindungan data pribadi.</p>
                        @error('nik')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pekerjaan" class="block font-label-md text-xs font-bold text-primary mb-1">
                            Pekerjaan / Profesi
                        </label>
                        <input type="text" name="pekerjaan" id="pekerjaan" value="{{ old('pekerjaan') }}" placeholder="Contoh: Karyawan Swasta, Wiraswasta, Mahasiswa"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-surface-container bg-surface-container-low text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('pekerjaan') border-red-500 @enderror">
                        @error('pekerjaan')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block font-label-md text-xs font-bold text-primary mb-1">
                            Alamat Email Aktif <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email.anda@domain.com"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-surface-container bg-surface-container-low text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('email') border-red-500 @enderror">
                        <p class="text-[11px] text-on-surface-variant mt-0.5">Bukti registrasi dan tanggapan resmi akan dikirim ke email ini.</p>
                        @error('email')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telepon" class="block font-label-md text-xs font-bold text-primary mb-1">
                            Nomor Telepon / WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="telepon" id="telepon" value="{{ old('telepon') }}" required placeholder="08xxxxxxxxxx"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-surface-container bg-surface-container-low text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('telepon') border-red-500 @enderror">
                        @error('telepon')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="alamat" class="block font-label-md text-xs font-bold text-primary mb-1">
                            Alamat Lengkap Tempat Tinggal <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alamat" id="alamat" rows="2" required placeholder="Jl. Nama Jalan, RT/RW, Kelurahan/Desa, Kecamatan, Kota/Kabupaten"
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-surface-container bg-surface-container-low text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('alamat') border-red-500 @enderror">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="identitas_file" class="block font-label-md text-xs font-bold text-primary mb-1">
                            Unggah Salinan KTP / Identitas Diri (Opsional)
                        </label>
                        <input type="file" name="identitas_file" id="identitas_file" accept=".pdf,.jpg,.jpeg,.png,.webp"
                               class="w-full text-xs text-on-surface file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-on-primary hover:file:bg-primary-container cursor-pointer border border-surface-container p-2 rounded-xl bg-surface-container-low">
                        <p class="text-[11px] text-gray-500 mt-1">
                            🔒 <strong>Penyimpanan Privat:</strong> Format PDF atau gambar (maks. 5 MB). Berkas identitas Anda disimpan di server terisolasi (private storage) dan <strong>TIDAK DAPAT</strong> diakses oleh publik.
                        </p>
                        @error('identitas_file')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Informasi yang Diminta -->
            <div class="space-y-4 pt-4 border-t border-surface-container">
                <div class="border-b border-surface-container pb-2">
                    <h3 class="font-title-lg text-base font-bold text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-secondary">description</span>
                        <span>2. Rincian Informasi yang Dibutuhkan</span>
                    </h3>
                    <p class="text-xs text-on-surface-variant">Jelaskan dokumen atau informasi publik yang ingin Anda peroleh.</p>
                </div>

                <div>
                    <label for="rincian_informasi" class="block font-label-md text-xs font-bold text-primary mb-1">
                        Rincian Informasi yang Diminta <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rincian_informasi" id="rincian_informasi" rows="4" required placeholder="Sebutkan secara rinci judul dokumen, tahun anggaran, atau data spesifik yang Anda perlukan..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-surface-container bg-surface-container-low text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('rincian_informasi') border-red-500 @enderror">{{ old('rincian_informasi') }}</textarea>
                    @error('rincian_informasi')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tujuan_penggunaan" class="block font-label-md text-xs font-bold text-primary mb-1">
                        Tujuan Penggunaan Informasi <span class="text-red-500">*</span>
                    </label>
                    <textarea name="tujuan_penggunaan" id="tujuan_penggunaan" rows="2" required placeholder="Contoh: Penelitian skripsi / karya ilmiah, pemenuhan data publik, dll."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-surface-container bg-surface-container-low text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('tujuan_penggunaan') border-red-500 @enderror">{{ old('tujuan_penggunaan') }}</textarea>
                    @error('tujuan_penggunaan')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block font-label-md text-xs font-bold text-primary mb-1.5">
                            Cara Memperoleh Informasi <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-xs font-medium text-on-surface cursor-pointer p-2 rounded-lg border border-surface-container hover:bg-surface-container-low">
                                <input type="radio" name="cara_memperoleh_informasi" value="melihat" {{ old('cara_memperoleh_informasi') === 'melihat' ? 'checked' : '' }} class="text-primary focus:ring-primary">
                                <span>Melihat / Membaca / Mendengarkan Langsung</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-medium text-on-surface cursor-pointer p-2 rounded-lg border border-surface-container hover:bg-surface-container-low">
                                <input type="radio" name="cara_memperoleh_informasi" value="salinan" {{ old('cara_memperoleh_informasi', 'salinan') === 'salinan' ? 'checked' : '' }} class="text-primary focus:ring-primary">
                                <span>Mendapatkan Salinan Fisik (Hardcopy)</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-medium text-on-surface cursor-pointer p-2 rounded-lg border border-surface-container hover:bg-surface-container-low">
                                <input type="radio" name="cara_memperoleh_informasi" value="soft_copy" {{ old('cara_memperoleh_informasi') === 'soft_copy' ? 'checked' : '' }} class="text-primary focus:ring-primary">
                                <span>Mendapatkan Salinan Elektronik (Soft Copy)</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-label-md text-xs font-bold text-primary mb-1.5">
                            Bentuk / Media Salinan yang Diinginkan <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-xs font-medium text-on-surface cursor-pointer p-2 rounded-lg border border-surface-container hover:bg-surface-container-low">
                                <input type="radio" name="cara_mendapat_salinan" value="email" {{ old('cara_mendapat_salinan', 'email') === 'email' ? 'checked' : '' }} class="text-primary focus:ring-primary">
                                <span>Surat Elektronik (Email / Soft Copy)</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-medium text-on-surface cursor-pointer p-2 rounded-lg border border-surface-container hover:bg-surface-container-low">
                                <input type="radio" name="cara_mendapat_salinan" value="langsung" {{ old('cara_mendapat_salinan') === 'langsung' ? 'checked' : '' }} class="text-primary focus:ring-primary">
                                <span>Mengambil Langsung di Meja Layanan PPID</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Persetujuan & Tombol Kirim -->
            <div class="pt-4 border-t border-surface-container flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-[11px] text-gray-500 max-w-md">
                    Dengan mengirimkan formulir ini, Anda menyatakan bahwa data yang diisikan adalah benar dan bertanggung jawab atas penggunaan informasi publik sesuai peraturan perundang-undangan.
                </div>

                <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-primary text-on-primary font-bold text-sm shadow-md hover:bg-primary-container transition flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">send</span>
                    <span>Kirim Permohonan Resmi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
