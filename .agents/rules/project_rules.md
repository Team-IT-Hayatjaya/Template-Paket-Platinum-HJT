# Aturan & Panduan Pengembangan Proyek (Paket Silver)

- Proyek: Website instansi pemerintah buatan PT Hayat Jaya Teknologi (HJT), paket SILVER.
- Bahasa antarmuka: Bahasa Indonesia formal & baku.
- Modul PPID mengacu pada UU No. 14 Tahun 2008.

## Front End Rules
- Stack: Blade, Tailwind CSS, Alpine.js, Vite.
- SATU template standar untuk semua klien Silver. Tema hanya diatur lewat variabel dinamis: logo, nama instansi, warna utama, favicon (dari pengaturan admin / file config / database), bukan desain kustom per klien.
- Mobile-first dan responsif.
- Aksesibilitas standar: HTML semantik, label formulir, alt gambar, fokus terlihat, kontras memadai (tanpa widget TTS/contrast switcher).
- Kinerja: WebP, lazy loading, hindari library berat.
- Komponen Blade reusable untuk template Gold dan Platinum kelak.

## Back End Rules
- Stack: Laravel (PHP) dan MySQL.
- Tabel Silver: users, roles (spatie/laravel-permission), kategori_informasi, dokumen_informasi, permohonan_informasi, status_permohonan, keberatan, pengaturan_ppid, log_aktivitas.
- Tabel DILARANG / BELUM dibuat (fitur Gold): riwayat_permohonan, jawaban_permohonan, tanggapan_keberatan, hari_libur. Kolom tenggat_jawab dan tenggat_diperpanjang boleh ada tetapi dibiarkan kosong (nullable). Rancang agar migrasi tabel Gold kelak bersifat non-destruktif.
- Pemrosesan jawaban permohonan dan keberatan dilakukan admin DI LUAR SISTEM. Sistem hanya mencatat, menyimpan status manual internal (tidak tampil di publik), dan mengirim email konfirmasi.
- Keamanan: Form Request, Eloquent query binding, CSRF, rate limit form publik, NIK encrypted cast, berkas identitas di storage non-publik, catat log aktivitas.
- Struktur: Service class, Policy, test wajib, migration bisa di-rollback.
