# Changelog Pengembangan — Paket GOLD

Dokumen ini mencatat riwayat versi, asal basis kode, dan peta jalan fitur untuk **Paket GOLD** Portal Resmi Instansi Pemerintah PT Hayat Jaya Teknologi (HJT).

---

## Informasi Repositori & Basis Rilis

- **Cabang Git:** `gold`
- **Basis Asal (Base Release):** `silver-v1.0`
- **Tanggal Mulai Pengembangan:** 12 Oktober 2026
- **Status Tahap:** Tahap Penyiapan & Isolasi Lingkungan (*Setup & Environment Isolation*)
- **Paket Aktif:** `SITE_PACKAGE=gold`

---

## 1. Tahap Penyiapan & Isolasi Lingkungan (12 Oktober 2026)

### Konfigurasi & Lingkungan
- [x] Pemisahan identitas aplikasi (`APP_NAME="Portal Resmi Instansi Pemerintah - Paket Gold"`).
- [x] Pemisahan port/URL lokal (`APP_URL=http://localhost:8001`) untuk menghindari konflik dengan Silver.
- [x] Isolasi database khusus Gold (`DB_DATABASE=database/database_gold.sqlite` / `portal_instansi_gold`).
- [x] Isolasi sesi dengan `SESSION_COOKIE=hjt_gold_session`.
- [x] Isolasi cache dengan `CACHE_PREFIX=hjt_gold_cache`.
- [x] Isolasi prefix Redis dengan `REDIS_PREFIX=hjt_gold_database_`.
- [x] Konfigurasi bawaan `config/packages.php` disetel ke tier `gold`.
- [x] Pembaharuan nama paket di `composer.json` menjadi `hjt/portal-instansi-gold`.
- [x] Dokumentasi silsilah kode di `README.md` merujuk rilis asal `silver-v1.0`.

### Tampilan & Navigasi Admin
- [x] Penambahan badge visual **⭐ PAKET GOLD** pada topbar panel admin Filament (`PanelsRenderHook::USER_MENU_BEFORE`).
- [x] Penambahan badge dan informasi lingkungan di footer panel admin Filament (`PanelsRenderHook::FOOTER`).
- [x] Penyesuaian judul merek panel admin: `Panel Admin Instansi — ⭐ Paket GOLD`.

---

## 2. Daftar Fitur Unggulan Gold (Peta Jalan Pengembangan)

Berikut adalah daftar modul dan fitur khusus Paket Gold yang akan diimplementasikan:

### A. Pelacakan Mandiri PPID (`ppid_tracking`)
- **Pelacakan Status Publik:** Pemohon informasi dapat memantau perkembangan permohonan secara mandiri menggunakan nomor tiket / kode registrasi tanpa harus login.
- **Linimasa Tahapan Real-time:** Visualisasi riwayat proses (Permohonan Masuk, Verifikasi Administrasi, Disposisi, Penyusunan Jawaban, Selesai / Keberatan).
- **Notifikasi Perubahan Status:** Pengiriman informasi pembaruan status permohonan kepada pemohon.

### B. Modul Respon & Tanggapan Keberatan Internal
- **Jawaban Permohonan (`jawaban_permohonan`):** Formulir dan penyimpanan berkas respon resmi permohonan informasi publik di dalam sistem.
- **Tanggapan Keberatan (`tanggapan_keberatan`):** Modul penanganan dan pencatatan tanggapan tertulis atas keberatan dari atasan PPID.
- **Manajemen Dokumen Hasil:** Unggah berkas keputusan atau dokumen publik yang diserahkan kepada pemohon.

### C. Perhitungan Hari Kerja Akurat (Kepatuhan UU KIP)
- **Tabel Hari Libur (`hari_libur`):** Manajemen kalender hari libur nasional dan cuti bersama resmi pemerintah.
- **Penghitungan Otomatis:** Perhitungan tenggat waktu respon 10 hari kerja (+ perpanjangan 7 hari kerja) dan respon keberatan 30 hari kerja tanpa memperhitungkan hari Sabtu, Minggu, atau libur nasional.

### D. Aksesibilitas Publik (Standar Web Pemerintah)
- **Text-to-Speech (TTS):** Pemutar audio sintetis untuk membacakan isi berita dan halaman informasi publik bagi penyandang disabilitas netra atau lansia.
- **Pengatur Kontras Tinggi (High Contrast Mode):** Penyesuaian tema kontras tinggi untuk memudahkan pembacaan teks.
- **Pengatur Ukuran Huruf (Font Scaling):** Tombol pembesar dan pengecil font dinamis di seluruh halaman publik.

### E. Keamanan Lanjutan & Privasi
- **Enkripsi NIK:** Kolom NIK pemohon dienkripsi menggunakan cast terenkripsi Eloquent (`'nik' => 'encrypted'`).
- **Private Storage:** Berkas KTP/SK pemohon serta berkas jawaban sensitif tersimpan aman di direktori non-publik (`storage/app/private/`).
- **Audit Trail Komprehensif:** Pencatatan otomatis setiap aksi penting ke tabel `activity_log`.

### F. Desain Kustom Eksklusif & Identitas Visual
- **Tema Visual Eksklusif Gold:** Aksen warna emas, tipografi modern, kartu interaktif bernuansa premium.
- **Kustomisasi Identitas Instansi:** Dukungan logo dinamis, favicon, nama instansi, serta tata letak responsif mobile-first.

### G. Kuota & Layanan Pemeliharaan
- **Batas Pembaruan Konten:** 20 pembaruan konten per bulan (`content_updates_per_month => 20`).
- **Jadwal Backup Otomatis:** Mingguan setiap Senin pukul 02:00 (`backup_frequency => weekly`).

---

*Hak Cipta &copy; 2026 PT Hayat Jaya Teknologi (HJT). Seluruh Hak Dilindungi.*
