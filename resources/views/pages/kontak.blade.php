@extends('layouts.app')

@section('title', 'Kontak Kami & Meja Layanan PPID - ' . ($siteSetting->institution_name ?? 'Portal Resmi'))

@push('styles')
    <!-- Leaflet CSS untuk Peta OpenStreetMap (Ringan & Mandiri) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map-container {
            height: 380px;
            width: 100%;
            border-radius: 12px;
            z-index: 1;
        }
        @media (max-width: 640px) {
            #map-container {
                height: 280px;
            }
        }
    </style>
@endpush

@section('content')
<div class="space-y-space-xl">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Kontak Kami & Pengaduan'],
    ]" />

    <!-- Header Halaman Kontak -->
    <section class="bg-surface-container-lowest p-space-lg lg:p-space-xl rounded-2xl shadow-2xs border border-surface-container flex flex-col md:flex-row md:items-end justify-between gap-space-lg">
        <div class="space-y-space-xs max-w-3xl">
            <div class="inline-flex items-center gap-space-xs bg-surface-container px-space-sm py-space-xs rounded-full text-secondary font-label-sm text-label-sm font-bold uppercase tracking-wider">
                <span class="material-symbols-outlined text-[16px]">support_agent</span>
                <span>Saluran Komunikasi Resmi</span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-primary tracking-tight">
                Kontak Kami & Formulir Aspirasi Pengaduan
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                Hubungi Meja Layanan PPID atau sampaikan aspirasi, pertanyaan, serta pengaduan masyarakat secara langsung melalui saluran resmi {{ $siteSetting->institution_name ?? 'Pemerintah Instansi' }}.
            </p>
        </div>
    </section>

    <!-- Notifikasi Sukses Pengiriman -->
    @if(session('success'))
        <div class="p-space-md rounded-xl bg-surface-container-lowest border-l-4 border-secondary shadow-2xs flex items-start gap-space-sm">
            <span class="material-symbols-outlined text-secondary text-[28px] shrink-0">check_circle</span>
            <div class="space-y-1">
                <h4 class="font-title-lg text-title-lg text-primary font-bold">Pesan Terkirim</h4>
                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Kartu Informasi Kontak Cepat -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-space-md">
        <!-- Alamat -->
        <div class="p-space-lg rounded-xl bg-surface-container-lowest border border-surface-container shadow-2xs flex flex-col justify-between">
            <div class="space-y-space-sm">
                <div class="w-10 h-10 rounded-lg bg-surface-container text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]">location_on</span>
                </div>
                <h3 class="font-title-lg text-title-lg text-primary font-bold">Alamat Kantor Meja PPID</h3>
                <address class="not-italic font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                    {{ $siteSetting->address ?? 'Jl. Protokol Pusat Pemerintahan No. 1' }}
                </address>
            </div>
            <span class="font-label-sm text-label-sm text-secondary font-bold mt-space-md block">Meja Pelayanan di Lobi Utama</span>
        </div>

        <!-- Telepon & Email -->
        <div class="p-space-lg rounded-xl bg-surface-container-lowest border border-surface-container shadow-2xs flex flex-col justify-between">
            <div class="space-y-space-sm">
                <div class="w-10 h-10 rounded-lg bg-surface-container text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]">contact_phone</span>
                </div>
                <h3 class="font-title-lg text-title-lg text-primary font-bold">Telepon & Email</h3>
                <div class="font-body-sm text-body-sm text-on-surface-variant space-y-1 leading-relaxed">
                    <div>Telepon: <strong class="text-primary">{{ $siteSetting->phone ?? '(0541) 661001' }}</strong></div>
                    <div>Email: <a href="mailto:{{ $siteSetting->email ?? 'info@instansi.go.id' }}" class="text-secondary hover:underline">{{ $siteSetting->email ?? 'info@instansi.go.id' }}</a></div>
                </div>
            </div>
            <span class="font-label-sm text-label-sm text-secondary font-bold mt-space-md block">Respon Hari Kerja</span>
        </div>

        <!-- Jam Pelayanan -->
        <div class="p-space-lg rounded-xl bg-surface-container-lowest border border-surface-container shadow-2xs flex flex-col justify-between">
            <div class="space-y-space-sm">
                <div class="w-10 h-10 rounded-lg bg-surface-container text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]">schedule</span>
                </div>
                <h3 class="font-title-lg text-title-lg text-primary font-bold">Jam Pelayanan Langsung</h3>
                <p class="whitespace-pre-line font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                    {{ $siteSetting->service_hours ?? "Senin - Kamis: 08.00 - 15.30 WITA\nJumat: 08.00 - 11.30 & 13.30 - 15.30 WITA" }}
                </p>
            </div>
            <span class="font-label-sm text-[11px] text-on-surface-variant mt-space-md block">Sabtu, Minggu & Hari Libur Nasional Tutup</span>
        </div>
    </div>

    <!-- Bagian 2 Kolom: Formulir Pengaduan & Peta Leaflet -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
        <!-- Formulir Pengaduan & Pesan Masyarakat -->
        <section class="lg:col-span-6 bg-surface-container-lowest rounded-2xl border border-surface-container p-space-lg sm:p-space-xl shadow-2xs" aria-labelledby="heading-form-kontak">
            <h2 id="heading-form-kontak" class="font-headline-md text-headline-md text-primary mb-1 flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-[24px]">edit_note</span>
                <span>Formulir Aspirasi & Pengaduan</span>
            </h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant mb-space-md">
                Sampaikan aduan atau aspirasi Anda. Pada paket Silver, notifikasi diteruskan secara langsung ke kontak kedinasan.
            </p>

            <form action="{{ route('kontak.kirim') }}" method="POST" class="space-y-space-sm font-body-sm text-body-sm">
                @csrf

                <!-- Honeypot anti-spam (tersembunyi untuk manusia, bot akan mengisinya) -->
                <div style="display:none; visibility:hidden; position:absolute; left:-9999px;" aria-hidden="true">
                    <label for="_hp_security_check">Jangan isi kolom ini jika Anda bukan robot:</label>
                    <input type="text" name="_hp_security_check" id="_hp_security_check" value="" tabindex="-1" autocomplete="off" />
                </div>

                <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-1 font-semibold" for="nama">Nama Lengkap Sesuai KTP *</label>
                    <input class="w-full bg-surface-container-low text-on-surface px-3 py-2.5 rounded-lg border border-surface-container outline-none focus:ring-2 focus:ring-secondary/50 font-body-sm text-body-sm transition" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Muhammad Ilham" required type="text"/>
                    @error('nama')
                        <p class="text-xs text-error font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-1 font-semibold" for="email">Alamat Email *</label>
                        <input class="w-full bg-surface-container-low text-on-surface px-3 py-2.5 rounded-lg border border-surface-container outline-none focus:ring-2 focus:ring-secondary/50 font-body-sm text-body-sm transition" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required type="email"/>
                        @error('email')
                            <p class="text-xs text-error font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-label-md text-label-md text-on-surface mb-1 font-semibold" for="telepon">Nomor WhatsApp / HP</label>
                        <input class="w-full bg-surface-container-low text-on-surface px-3 py-2.5 rounded-lg border border-surface-container outline-none focus:ring-2 focus:ring-secondary/50 font-body-sm text-body-sm transition" id="telepon" name="telepon" value="{{ old('telepon') }}" placeholder="08xxxxxxxxxx" type="tel"/>
                        @error('telepon')
                            <p class="text-xs text-error font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-1 font-semibold" for="topik">Topik Pesan / Kategori Pengaduan *</label>
                    <select class="w-full bg-surface-container-low text-on-surface px-3 py-2.5 rounded-lg border border-surface-container outline-none focus:ring-2 focus:ring-secondary/50 font-body-sm text-body-sm transition" id="topik" name="topik" required>
                        <option value="">-- Pilih Topik Pesan --</option>
                        <option value="pelayanan_ppid" {{ old('topik') === 'pelayanan_ppid' ? 'selected' : '' }}>Pelayanan Informasi PPID</option>
                        <option value="aspirasi_kebijakan" {{ old('topik') === 'aspirasi_kebijakan' ? 'selected' : '' }}>Aspirasi & Saran Kebijakan</option>
                        <option value="infrastruktur" {{ old('topik') === 'infrastruktur' ? 'selected' : '' }}>Layanan Infrastruktur & Publik</option>
                        <option value="lainnya" {{ old('topik') === 'lainnya' ? 'selected' : '' }}>Pertanyaan & Informasi Umum</option>
                    </select>
                    @error('topik')
                        <p class="text-xs text-error font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-1 font-semibold" for="pesan">Uraian Aspirasi atau Pengaduan *</label>
                    <textarea class="w-full bg-surface-container-low text-on-surface px-3 py-2.5 rounded-lg border border-surface-container outline-none focus:ring-2 focus:ring-secondary/50 font-body-sm text-body-sm transition" id="pesan" name="pesan" rows="4" placeholder="Tuliskan secara lengkap pesan, pertanyaan, atau laporan Anda..." required>{{ old('pesan') }}</textarea>
                    @error('pesan')
                        <p class="text-xs text-error font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto px-space-lg py-2.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md font-bold transition flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        <span>Kirim Pesan / Pengaduan</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- Lokasi Kantor & Peta Interaktif Leaflet (OpenStreetMap) -->
        <section class="lg:col-span-6 bg-surface-container-lowest rounded-2xl border border-surface-container p-space-lg sm:p-space-xl shadow-2xs space-y-space-md" aria-labelledby="heading-peta">
            <div>
                <h2 id="heading-peta" class="font-headline-md text-headline-md text-primary mb-1 flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-[24px]">map</span>
                    <span>Lokasi Kantor & Meja Layanan</span>
                </h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Peta lokasi kantor instansi resmi terintegrasi OpenStreetMap bebas lisensi pihak ketiga.
                </p>
            </div>

            <div id="map-container" class="border border-surface-container overflow-hidden shadow-2xs" aria-label="Peta Lokasi Kantor Instansi"></div>
            
            <div class="p-space-md bg-surface-container-low rounded-xl border border-surface-container text-body-sm text-on-surface-variant leading-relaxed">
                <strong class="text-primary block font-semibold mb-0.5">Petunjuk Arah & Aksesibilitas:</strong>
                {{ $siteSetting->address ?? 'Kompleks Kantor Pemerintahan. Meja Layanan PPID berada tepat di Lobi Gedung Utama.' }} Tersedia fasilitas parkir, jalur landai kursi roda, dan ruang tunggu berpendingin udara.
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Script Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi peta Leaflet (Kutai Kartanegara / Loa Kulu center standar)
            var map = L.map('map-container').setView([-0.5475, 117.0267], 14);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            var marker = L.marker([-0.5475, 117.0267]).addTo(map);
            marker.bindPopup("<b>Meja Layanan PPID Utama</b><br>{{ addslashes($siteSetting->institution_name ?? 'Kantor Pemerintah') }}<br><small>{{ addslashes($siteSetting->service_hours ?? 'Senin-Jumat 08.00-15.30 WITA') }}</small>").openPopup();

            setTimeout(function() {
                map.invalidateSize();
            }, 300);

            window.addEventListener('resize', function() {
                map.invalidateSize();
            });
        });
    </script>
@endpush
