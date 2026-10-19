@extends('layouts.admin')

@section('title', 'Pengaturan & Variabel Tema Instansi')
@section('header_title', 'Pengaturan Instansi & Variabel Tema')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 md:p-8 shadow-sm mb-8">
        <div class="mb-6 pb-4 border-b border-slate-200">
            <h3 class="text-base sm:text-xl font-bold text-slate-900 mb-1">
                Identitas Instansi & Pengendali Tema Standar (Paket Silver)
            </h3>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                Perubahan pada formulir ini akan langsung menyesuaikan identitas, logo, warna, dan kontak di seluruh halaman web tanpa mengubah struktur kode template.
            </p>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl border border-red-300 bg-red-50 text-red-900 text-sm shadow-sm mb-6" role="alert">
                <strong class="font-bold block mb-1">Terjadi kesalahan pengisian formulir:</strong>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.pengaturan.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Bagian 1: Identitas Nama Instansi -->
            <div>
                <h4 class="text-sm sm:text-base font-bold text-blue-900 mb-4 pl-3 border-l-3 border-blue-900">
                    1. Identitas Nama & Satuan Kerja
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-4">
                    <div class="form-group mb-0">
                        <label for="nama_instansi" class="form-label text-xs sm:text-sm font-semibold text-slate-800">
                            Nama Instansi Utama <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="nama_instansi" id="nama_instansi" class="form-control" value="{{ old('nama_instansi', $pengaturan->nama_instansi) }}" required placeholder="Contoh: Pemerintah Kabupaten / Kota">
                        <small class="text-xs text-slate-500 block mt-1">Ditampilkan pada judul situs dan kop header atas.</small>
                    </div>

                    <div class="form-group mb-0">
                        <label for="sub_nama_instansi" class="form-label text-xs sm:text-sm font-semibold text-slate-800">
                            Sub Nama Instansi / Organisasi
                        </label>
                        <input type="text" name="sub_nama_instansi" id="sub_nama_instansi" class="form-control" value="{{ old('sub_nama_instansi', $pengaturan->sub_nama_instansi) }}" placeholder="Contoh: Dinas Komunikasi dan Informatika">
                        <small class="text-xs text-slate-500 block mt-1">Ditampilkan di bawah nama instansi utama.</small>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label for="nama_ppid" class="form-label text-xs sm:text-sm font-semibold text-slate-800">
                        Nama Unit PPID <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="nama_ppid" id="nama_ppid" class="form-control" value="{{ old('nama_ppid', $pengaturan->nama_ppid) }}" required placeholder="Contoh: PPID Utama Pemerintah Daerah">
                </div>
            </div>

            <!-- Bagian 2: Variabel Warna & Tema Dinamis -->
            <div class="pt-2 border-t border-slate-100">
                <h4 class="text-sm sm:text-base font-bold text-blue-900 mb-4 pl-3 border-l-3 border-blue-900">
                    2. Variabel Tema Dinamis
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <div class="form-group mb-0">
                        <label for="warna_utama" class="form-label text-xs sm:text-sm font-semibold text-slate-800">
                            Warna Utama Tema (Hex) <span class="text-red-600">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="color" id="picker_warna_utama" value="{{ old('warna_utama', $pengaturan->warna_utama) }}" class="w-11 h-10 rounded-lg border border-slate-300 cursor-pointer shrink-0 p-0.5 bg-white" oninput="document.getElementById('warna_utama').value = this.value">
                            <input type="text" name="warna_utama" id="warna_utama" class="form-control" value="{{ old('warna_utama', $pengaturan->warna_utama) }}" required pattern="^#([a-fA-F0-9]{6})$" placeholder="#1e3a8a" oninput="if(/^#([a-fA-F0-9]{6})$/.test(this.value)) document.getElementById('picker_warna_utama').value = this.value">
                        </div>
                        <small class="text-xs text-slate-500 block mt-1">Mempengaruhi warna navbar, header, tombol utama, dan aksen latar.</small>
                    </div>

                    <div class="form-group mb-0">
                        <label for="warna_sekunder" class="form-label text-xs sm:text-sm font-semibold text-slate-800">
                            Warna Sekunder / Aksen (Hex)
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="color" id="picker_warna_sekunder" value="{{ old('warna_sekunder', $pengaturan->warna_sekunder ?? '#b91c1c') }}" class="w-11 h-10 rounded-lg border border-slate-300 cursor-pointer shrink-0 p-0.5 bg-white" oninput="document.getElementById('warna_sekunder').value = this.value">
                            <input type="text" name="warna_sekunder" id="warna_sekunder" class="form-control" value="{{ old('warna_sekunder', $pengaturan->warna_sekunder ?? '#b91c1c') }}" pattern="^#([a-fA-F0-9]{6})$" placeholder="#b91c1c" oninput="if(/^#([a-fA-F0-9]{6})$/.test(this.value)) document.getElementById('picker_warna_sekunder').value = this.value">
                        </div>
                        <small class="text-xs text-slate-500 block mt-1">Warna aksen tombol aksi khusus PPID dan garis tepi.</small>
                    </div>
                </div>
            </div>

            <!-- Bagian 3: Unggah Logo & Favicon -->
            <div class="pt-2 border-t border-slate-100">
                <h4 class="text-sm sm:text-base font-bold text-blue-900 mb-4 pl-3 border-l-3 border-blue-900">
                    3. Berkas Lambang / Logo & Favicon
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <div class="form-group mb-0">
                        <label for="logo" class="form-label text-xs sm:text-sm font-semibold text-slate-800">
                            Unggah Lambang / Logo Instansi
                        </label>
                        @if($pengaturan->logo_url)
                            <div class="mb-2 flex items-center gap-3 p-2 bg-slate-50 rounded-lg border border-slate-200">
                                <img src="{{ $pengaturan->logo_url }}" alt="Logo saat ini" class="max-h-10 w-auto object-contain p-1 bg-white rounded border border-slate-200">
                                <span class="text-xs text-emerald-600 font-semibold">✓ Logo aktif terpasang</span>
                            </div>
                        @endif
                        <input type="file" name="logo" id="logo" class="form-control" accept="image/webp,image/png,image/jpeg,image/svg+xml">
                        <small class="text-xs text-slate-500 block mt-1">Format disarankan WebP atau PNG transparan (maks. 2 MB).</small>
                    </div>

                    <div class="form-group mb-0">
                        <label for="favicon" class="form-label text-xs sm:text-sm font-semibold text-slate-800">
                            Unggah Favicon Peramban
                        </label>
                        @if($pengaturan->favicon_path)
                            <div class="mb-2 flex items-center gap-3 p-2 bg-slate-50 rounded-lg border border-slate-200">
                                <img src="{{ $pengaturan->favicon_url }}" alt="Favicon saat ini" class="w-6 h-6 object-contain rounded border border-slate-200">
                                <span class="text-xs text-emerald-600 font-semibold">✓ Favicon terpasang</span>
                            </div>
                        @endif
                        <input type="file" name="favicon" id="favicon" class="form-control" accept="image/x-icon,image/png,image/webp">
                        <small class="text-xs text-slate-500 block mt-1">Format ICO, PNG, atau WebP (maks. 1 MB).</small>
                    </div>
                </div>
            </div>

            <!-- Bagian 4: Kontak & Jam Layanan -->
            <div class="pt-2 border-t border-slate-100">
                <h4 class="text-sm sm:text-base font-bold text-blue-900 mb-4 pl-3 border-l-3 border-blue-900">
                    4. Informasi Kontak Meja Layanan PPID
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-4">
                    <div class="form-group mb-0">
                        <label for="telepon" class="form-label text-xs sm:text-sm font-semibold text-slate-800">Nomor Telepon Kantor</label>
                        <input type="text" name="telepon" id="telepon" class="form-control" value="{{ old('telepon', $pengaturan->telepon) }}" placeholder="(021) 1234-5678">
                    </div>

                    <div class="form-group mb-0">
                        <label for="email" class="form-label text-xs sm:text-sm font-semibold text-slate-800">Alamat Email Resmi</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $pengaturan->email) }}" placeholder="ppid@instansi.go.id">
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="alamat" class="form-label text-xs sm:text-sm font-semibold text-slate-800">Alamat Lengkap Kantor / Meja PPID</label>
                    <textarea name="alamat" id="alamat" rows="2" class="form-control">{{ old('alamat', $pengaturan->alamat) }}</textarea>
                </div>

                <div class="form-group mb-4">
                    <label for="jam_layanan" class="form-label text-xs sm:text-sm font-semibold text-slate-800">Jadwal Jam Kerja & Pelayanan</label>
                    <textarea name="jam_layanan" id="jam_layanan" rows="2" class="form-control">{{ old('jam_layanan', $pengaturan->jam_layanan) }}</textarea>
                </div>

                <div class="form-group mb-6">
                    <label for="maklumat_pelayanan" class="form-label text-xs sm:text-sm font-semibold text-slate-800">Teks Maklumat Pelayanan Keterbukaan Informasi</label>
                    <textarea name="maklumat_pelayanan" id="maklumat_pelayanan" rows="3" class="form-control">{{ old('maklumat_pelayanan', $pengaturan->maklumat_pelayanan) }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row gap-3">
                <button type="submit" class="btn btn-primary w-full sm:w-auto" id="btn-simpan-pengaturan">
                    💾 Simpan Pembaruan Pengaturan
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light w-full sm:w-auto text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
