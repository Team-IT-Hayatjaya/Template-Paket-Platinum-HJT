# Aturan & Panduan Pengembangan Proyek

**Proyek:** Website Instansi Pemerintah — PT Hayat Jaya Teknologi (HJT)  
**Paket:** PLATINUM  
**Cabang Git:** `platinum`  
**Bahasa Antarmuka:** Bahasa Indonesia  
**Regulasi PPID:** Mengacu pada UU No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik  

---

## ⚠️ PERINGATAN ISOLASI REPOSITORI (SANGAT KRUSIAL)
- **AGENT DILARANG KERAS MENYENTUH FOLDER SILVER & GOLD:**  
  1. Folder `c:\Users\User\Documents\Template Website Paket silver 2` adalah rilis beku berlabel tag `silver-v1.0`.
  2. Folder `c:\Users\User\Documents\hjt-gold` adalah repositori khusus paket Gold.
  **DILARANG** melakukan modifikasi, penghapusan, atau eksekusi berkas di dalam kedua direktori tersebut.  
  Seluruh aktivitas pengembangan Platinum **HANYA** boleh dilakukan di dalam repositori ini: `c:\Users\User\Documents\Template Website Paket Platinum`.

---

## 1. Identitas & Lingkungan Paket Platinum
- **Nama Aplikasi:** `Portal Resmi Instansi Pemerintah - Paket Platinum` (klien: Pemprov, Setda, Dinas Besar)
- **Paket Aktif:** `SITE_PACKAGE=platinum`
- **Isolasi Sesi & Cache:**
  - `SESSION_COOKIE=hjt_platinum_session`
  - `CACHE_PREFIX=hjt_platinum_cache`
  - `REDIS_PREFIX=hjt_platinum_database_`
- **Database:** Terisolasi mandiri pada `database/database_platinum.sqlite` (atau database MySQL/PostgreSQL khusus klien Platinum).
- **Badge Admin:** Panel admin Filament menampilkan badge khusus **💎 PAKET PLATINUM** pada topbar dan judul panel.

---

## 2. Fitur & Kapasitas Paket Platinum
- **Fitur Unggulan Platinum:**
  1. **Multi Sub-Domain Bidang/UPTD:** Portal utama + hingga 5 sub-domain unit dalam satu aplikasi dengan deteksi host otomatis (`DetectUnit`), global scope `UnitScope`, otorisasi peran `Admin Unit`, serta agregasi konten ke portal utama.
  2. **Modul Open Data Publik:** Katalog dataset dengan validasi unggah CSV, pratinjau tabel, grafik visual interaktif (*Chart.js: line, bar, pie*), unduh CSV/JSON, riwayat versi dataset, dan API publik `/api/v1/datasets` dengan *rate limiting*.
  3. **WhatsApp Bot Informasi Publik:** Integrasi antarmuka `WhatsAppProvider` (WhatsApp Cloud API resmi Meta + Mock untuk CI/pengujian), webhook terverifikasi HMAC-SHA256, menu cek status permohonan PPID, berita terbaru, kontak PPID, FAQ, pencatatan percakapan aman privasi, dan eskalasi otomatis ke tiket admin.
  4. **Pelacakan & Layanan PPID Lengkap:** Pelacakan status permohonan mandiri, jawaban permohonan & keberatan, dan kalkulasi hari kerja otomatis dengan tabel hari libur.
  5. **Widget Aksesibilitas Publik:** Fitur Text-to-Speech (TTS), pengaturan kontras tinggi, dan penyesuaian ukuran font.
  6. **Kuota Pembaruan Konten:** 40 pembaruan per bulan (`content_updates_per_month => 40`).
  7. **Jadwal Backup Otomatis:** Harian (`backup_frequency => daily`) dilengkapi notifikasi kegagalan otomatis.
  8. **Laporan Bulanan Komprehensif:** Mengagregasi metrik konten, PPID, unit sub-domain, dataset open data, dan statistik interaksi bot WhatsApp.

---

## 3. Aturan Front End
- **Stack:** Blade, Tailwind CSS, Alpine.js, Chart.js, Vite.
- **Kustomisasi Tema:** Melalui variabel dinamis (logo, nama instansi, warna utama, favicon) bersumber dari tabel pengaturan / config per unit maupun portal utama.
- **Mobile-first & Responsif:** Desain responsif di semua perangkat.
- **Kinerja:** Format WebP, lazy loading (`loading="lazy"`), aset terkompilasi Vite.
- **Komponen Modular:** Menggunakan komponen Blade (`resources/views/components/`).
- **Bahasa:** Seluruh teks antarmuka wajib Bahasa Indonesia yang baku dan formal.

---

## 4. Aturan Back End & Database
- **Stack:** Laravel 11 / PHP 8.2+ dan SQLite / MySQL.
- **Keamanan:**
  - Validasi wajib melalui **Form Request**.
  - Query binding / Eloquent murni.
  - Proteksi CSRF di setiap formulir.
  - Rate limiting pada formulir & API publik (`throttle`).
  - NIK wajib menggunakan **encrypted cast** (`'nik' => 'encrypted'`).
  - Berkas identitas (KTP/SK) disimpan di **storage non-publik** (private storage).
  - Catat setiap perubahan penting ke tabel `activity_log` (Spatie Activitylog).
- **Struktur Kode & Rekayasa:**
  - Service class untuk seluruh logika bisnis (`app/Services/`).
  - Policy untuk kontrol otorisasi hak akses (`app/Policies/`).
  - Testing komprehensif menggunakan Pest PHP.
