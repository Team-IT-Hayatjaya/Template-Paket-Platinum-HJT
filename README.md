# Portal Resmi Instansi Pemerintah — Paket GOLD (HJT)

[![PHP Version](https://img.shields.io/badge/PHP-8.3%20%7C%208.4-777bb4.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-ff2d20.svg)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.x%20%2F%205.x-ea580c.svg)](https://filamentphp.com)
[![Paket](https://img.shields.io/badge/Tier-GOLD-amber.svg)]()
[![Base Tag](https://img.shields.io/badge/Base-silver--v1.0-blue.svg)]()
[![License](https://img.shields.io/badge/License-Proprietary-blue.svg)]()

> **Informasi Silsilah & Isolasi Proyek:**  
> Proyek repositori ini adalah varian **Paket GOLD** dari PT Hayat Jaya Teknologi (HJT) yang dikembangkan dari basis rilis beku **`silver-v1.0`** (Paket Silver).  
> Repositori ini berjalan pada cabang `gold` dan menggunakan basis data mandiri (`database_gold.sqlite`), konfigurasi sesi terisolasi (`SESSION_COOKIE=hjt_gold_session`), serta cache terpisah (`CACHE_PREFIX=hjt_gold_cache`) untuk menjamin tidak ada tumpang tindih data dengan repositori Silver bila berjalan bersamaan.

Portal resmi instansi pemerintah (Kecamatan, Dinas, Pemkab/Pemkot, Provinsi) Paket Gold yang dirancang untuk implementasi cepat, aman, memenuhi standar aksesibilitas, dilengkapi fitur pelacakan PPID mandiri, dan patuh penuh terhadap **Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik (UU KIP)**.

---

## Daftar Isi
1. [Fitur Utama & Arsitektur Paket](#1-fitur-utama--arsitektur-paket)
2. [Prasyarat Sistem](#2-prasyarat-sistem)
3. [Instalasi Klien Baru (Satu Perintah)](#3-instalasi-klien-baru-satu-perintah)
4. [Panduan Deployment ke Server Produksi](#4-panduan-deployment-ke-server-produksi)
5. [Manajemen Backup & Prosedur Restore](#5-manajemen-backup--prosedur-restore)
6. [Monitoring & Health Check](#6-monitoring--health-check)
7. [Pengujian (Testing)](#7-pengujian-testing)

---

## 1. Fitur Utama & Arsitektur Paket

Satu codebase terpadu digunakan untuk seluruh klien dengan konfigurasi fitur yang dikontrol melalui variabel lingkungan `SITE_PACKAGE` di berkas `.env` (`config/packages.php`):

| Fitur / Layanan | Paket SILVER | Paket GOLD | Paket PLATINUM |
|---|:---:|:---:|:---:|
| **CMS Publikasi** (Berita, Halaman, Galeri, Dokumen, Banner) | ✅ | ✅ | ✅ |
| **PPID Dasar** (DIP 4 Klasifikasi, Form Permohonan, Respon Email) | ✅ | ✅ | ✅ |
| **Pelacakan Publik PPID Mandiri** (`ppid_tracking`) | ❌ *(Via Email)* | ✅ | ✅ |
| **Widget Aksesibilitas** (TTS / High Contrast) | ❌ | ✅ | ✅ |
| **Integrasi Open Data & API Publik** | ❌ | ❌ | ✅ |
| **WhatsApp Bot Notifikasi** | ❌ | ❌ | ✅ |
| **Subdomain Desa / Kelurahan** | ❌ | ❌ | ✅ |
| **Batas Pembaruan Konten Bulanan** | 5 update | 20 update | Unlimited |
| **Frekuensi Backup Otomatis** | Bulanan | Mingguan | Harian |

### Keamanan & Privasi Data Pemohon
- **Enkripsi NIK:** NIK pemohon dienkripsi otomatis di level database menggunakan Eloquent encrypted cast (`'nik' => 'encrypted'`).
- **Private Storage:** Berkas KTP/identitas disimpan terisolasi di disk privat (`storage/app/private/`) tanpa symbolic link publik. Akses hanya diizinkan untuk akun berwenang (*Super Admin* & *Petugas PPID*).
- **Statistik Ramah Privasi:** Pelacakan pengunjung lokal internal tanpa pihak ketiga dan **tanpa menyimpan alamat IP mentah**.

---

## 2. Prasyarat Sistem

- **PHP:** Versi 8.3 atau 8.4
- **Ekstensi PHP Wajib:** `pdo`, `pdo_mysql` / `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` / `imagick`, `zip`
- **Database:** MySQL 8.0+ / MariaDB 10.5+ / SQLite 3.35+
- **Composer:** Versi 2.x
- **Node.js & NPM:** Node.js 18.x / 20.x LTS

---

## 3. Instalasi Klien Baru (Satu Perintah)

Setiap klien baru dideploy pada direktori atau server tersendiri dengan berkas `.env` masing-masing.

### Langkah 1: Kloning & Persiapan Dependensi
```bash
# Masuk ke direktori proyek
cd /var/www/portal-klien

# Salin berkas lingkungan
cp .env.example .env

# Instal paket dependensi PHP dan Node.js
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### Langkah 2: Jalankan Perintah Instalasi 1-Command
Jalankan perintah interaktif berikut:
```bash
php artisan site:install
```

Atau gunakan mode non-interaktif langsung dengan seluruh opsi parameter:
```bash
php artisan site:install \
  --package=silver \
  --institution="Pemerintah Kecamatan Loa Kulu" \
  --acronym="KEC-LK" \
  --domain="https://loakulu.kutaiterkini.go.id" \
  --admin-name="Administrator Utama" \
  --admin-email="admin@loakulu.go.id" \
  --admin-password="PasswordSuperKuat#2026!" \
  --force
```

**Perintah di atas secara otomatis akan:**
1. Mengonfigurasi variabel `SITE_PACKAGE`, `APP_NAME`, `APP_URL`, dan `SUPER_ADMIN_EMAIL` di `.env`.
2. Menghasilkan Application Encryption Key (`key:generate`).
3. Menjalankan seluruh migrasi skema tabel database.
4. Menyemai data awal (peran, klasifikasi informasi UU KIP, status permohonan, halaman statis awal).
5. Mengonfigurasi identitas instansi di tabel `site_settings` dan `pengaturan_ppid`.
6. Membuat akun Super Admin pertama dan mengaitkan hak akses.
7. Membuat tautan berkas publik (`storage:link`).
8. Membersihkan cache aplikasi.

---

## 4. Panduan Deployment ke Server Produksi

### A. Izin Folder (File Permissions)
Pastikan web server (misalnya user `www-data` atau `nginx`) memiliki izin tulis ke direktori `storage` dan `bootstrap/cache`:
```bash
sudo chown -R www-data:www-data /var/www/portal-klien/storage /var/www/portal-klien/bootstrap/cache
sudo chmod -R 775 /var/www/portal-klien/storage /var/www/portal-klien/bootstrap/cache
```

### B. Konfigurasi Nginx
Contoh konfigurasi Virtual Host Nginx:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name loakulu.kutaiterkini.go.id;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name loakulu.kutaiterkini.go.id;
    root /var/www/portal-klien/public;

    ssl_certificate /etc/letsencrypt/live/loakulu.kutaiterkini.go.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/loakulu.kutaiterkini.go.id/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    index index.php;
    charset utf-8;

    # Blokir akses langsung ke storage privat jika ada kesalahan konfigurasi
    location ^~ /storage/identitas/ {
        deny all;
        return 404;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### C. Konfigurasi Cron Job (Scheduler)
Fitur backup berkala, pembersihan cache, dan penghitungan tenggat bergantung pada scheduler Laravel. Tambahkan baris berikut ke crontab server:
```bash
crontab -e -u www-data
```
Tambahkan baris berikut:
```cron
* * * * * cd /var/www/portal-klien && php artisan schedule:run >> /dev/null 2>&1
```

### D. Optimasi Cache Produksi
Jalankan perintah optimasi setelah deployment kode baru:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5. Manajemen Backup & Prosedur Restore

### A. Eksekusi Backup
Backup otomatis dijalankan oleh cron job sesuai frekuensi paket klien:
- **Silver:** Bulanan (tanggal 1 pukul 02:00)
- **Gold:** Mingguan (hari Senin pukul 02:00)
- **Platinum:** Harian (setiap hari pukul 02:00)

Untuk menjalankan backup secara manual melalui terminal:
```bash
# Backup seluruh database dan berkas:
php artisan backup:run

# Backup hanya database:
php artisan backup:run --only-db
```
Arsip backup tersimpan dalam format `.zip` di direktori:
`storage/app/Laravel/*.zip`

Notifikasi email akan otomatis dikirimkan ke alamat `BACKUP_NOTIFICATION_EMAIL` atau `SUPER_ADMIN_EMAIL` jika proses backup mengalami kegagalan.

### B. Prosedur Pemulihan (Restore Backup)
Jika terjadi insiden kehilangan data, ikuti langkah pemulihan berikut:

1. **Ekstrak Berkas Arsip Backup:**
   ```bash
   # Masuk ke direktori cadangan
   cd /var/www/portal-klien/storage/app/Laravel
   
   # Ekstrak arsip zip backup ke folder sementara
   unzip 2026-10-01-02-00-00.zip -d /tmp/restore-ppid
   ```

2. **Restore Database:**
   - **Jika menggunakan MySQL/MariaDB:**
     ```bash
     mysql -u nama_user -p nama_database < /tmp/restore-ppid/db-dumps/mysql-nama_database.sql
     ```
   - **Jika menggunakan SQLite:**
     ```bash
     cp /tmp/restore-ppid/db-dumps/sqlite-database.sqlite /var/www/portal-klien/database/database.sqlite
     ```

3. **Restore Berkas Upload (Storage Publik & Privat):**
   ```bash
   # Salin kembali berkas storage publik
   cp -r /tmp/restore-ppid/storage/app/public/* /var/www/portal-klien/storage/app/public/
   
   # Salin kembali berkas storage privat (identitas dan jawaban PPID)
   cp -r /tmp/restore-ppid/storage/app/private/* /var/www/portal-klien/storage/app/private/
   ```

4. **Perbarui Cache & Verifikasi:**
   ```bash
   php artisan storage:link
   php artisan cache:clear
   curl -I https://loakulu.kutaiterkini.go.id/health
   ```

---

## 6. Monitoring & Health Check

Sistem dilengkapi endpoint `/health` untuk memeriksa kondisi infrastruktur utama:
- **URL Web Browser:** `https://domain-klien.go.id/health`
- **URL Monitoring API:** `https://domain-klien.go.id/health?format=json`

Endpoint ini menguji secara langsung:
1. **Database:** Koneksi aktif, kemampuan query, dan latensi (ms).
2. **Storage:** Izin tulis pada direktori `storage/app/public` dan `storage/app/private`.
3. **Queue:** Kesiapan driver antrean (`database` / `sync`).

Mengembalikan status **HTTP 200 OK** jika seluruh komponen normal, atau **HTTP 503** jika terdapat komponen kritis yang mengalami gangguan.

---

## 7. Pengujian (Testing)

Proyek ini menggunakan **Pest PHP** dengan cakupan pengujian menyeluruh pada seluruh alur kerja:

```bash
# Menjalankan seluruh test suite
php artisan test

# Menjalankan pengujian spesifik PPID & Kesiapan Produksi
php artisan test tests/Feature/PpidBasicWorkflowTest.php
php artisan test tests/Feature/ProductionReadinessTest.php
```

---

*Hak Cipta &copy; 2026 PT Hayat Jaya Teknologi (HJT). Seluruh Hak Dilindungi.*
