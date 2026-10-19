<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan PPID - {{ $period_label }}</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .kop-instansi {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .kop-sub {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #334155;
            margin: 2px 0;
        }
        .kop-alamat {
            font-size: 8.5pt;
            color: #64748b;
            margin: 2px 0 0 0;
        }
        .judul-laporan {
            text-align: center;
            margin-bottom: 24px;
        }
        .judul-laporan h2 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            text-decoration: underline;
            color: #0f172a;
        }
        .judul-laporan p {
            font-size: 9.5pt;
            color: #475569;
            margin: 4px 0 0 0;
            font-weight: 600;
        }
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            background-color: #f1f5f9;
            padding: 6px 10px;
            border-left: 4px solid #1e3a8a;
            margin-top: 18px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 14px;
        }
        table.data-table th {
            background-color: #f8fafc;
            color: #0f172a;
            font-weight: bold;
            text-align: left;
            padding: 7px 10px;
            border: 1px solid #cbd5e1;
            font-size: 9pt;
            text-transform: uppercase;
        }
        table.data-table td {
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #fbfcfe;
        }
        .kpi-grid {
            width: 100%;
            margin-bottom: 10px;
        }
        .kpi-card {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 8px 12px;
            border-radius: 4px;
            text-align: center;
        }
        .kpi-value {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
            margin: 2px 0;
        }
        .kpi-label {
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            color: #ffffff;
        }
        .ttd-table {
            width: 100%;
            margin-top: 35px;
            border: none;
            page-break-inside: avoid;
        }
        .ttd-table td {
            border: none;
            padding: 0;
            vertical-align: top;
            font-size: 9.5pt;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer-note {
            margin-top: 30px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
            font-size: 7.5pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI -->
    <div class="kop-surat">
        <h1 class="kop-instansi">{{ $instansi['nama'] }}</h1>
        <div class="kop-sub">Pejabat Pengelola Informasi dan Dokumentasi (PPID)</div>
        <div class="kop-alamat">
            {{ $instansi['alamat'] }} | Telp: {{ $instansi['telepon'] }} | Email: {{ $instansi['email'] }}
        </div>
    </div>

    <!-- JUDUL LAPORAN -->
    <div class="judul-laporan">
        <h2>Laporan Bulanan Akuntabilitas & Kinerja Keterbukaan Informasi</h2>
        <p>Periode Pelaporan: {{ $period_label }}</p>
    </div>

    <!-- 1. STATISTIK PENGUNJUNG -->
    <div class="section-title">1. Statistik Kunjungan Situs Resmi (Privasi Tanpa Pihak Ketiga)</div>
    <table class="kpi-grid">
        <tr>
            <td width="25%" style="padding-right: 6px;">
                <div class="kpi-card">
                    <div class="kpi-label">Total Kunjungan</div>
                    <div class="kpi-value">{{ number_format($total_views, 0, ',', '.') }}</div>
                    <div class="kpi-label">Halaman Dilihat</div>
                </div>
            </td>
            <td width="25%" style="padding: 0 4px;">
                <div class="kpi-card">
                    <div class="kpi-label">Perangkat Desktop</div>
                    <div class="kpi-value">{{ number_format($views_by_device['desktop'], 0, ',', '.') }}</div>
                    <div class="kpi-label">Komputer / Laptop</div>
                </div>
            </td>
            <td width="25%" style="padding: 0 4px;">
                <div class="kpi-card">
                    <div class="kpi-label">Perangkat Mobile</div>
                    <div class="kpi-value">{{ number_format($views_by_device['mobile'], 0, ',', '.') }}</div>
                    <div class="kpi-label">Smartphone</div>
                </div>
            </td>
            <td width="25%" style="padding-left: 6px;">
                <div class="kpi-card">
                    <div class="kpi-label">Perangkat Tablet</div>
                    <div class="kpi-value">{{ number_format($views_by_device['tablet'], 0, ',', '.') }}</div>
                    <div class="kpi-label">Layar Tablet</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. HALAMAN TERPOPULER -->
    <div class="section-title">2. Halaman Terpopuler Diakses Masyarakat</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="8%" class="text-center">No</th>
                <th>Alamat Halaman (URL / Path)</th>
                <th width="25%" class="text-right">Jumlah Kunjungan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($top_pages as $idx => $page)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><code>{{ $page['halaman'] }}</code></td>
                    <td class="text-right font-bold">{{ number_format($page['views'], 0, ',', '.') }} kali</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center" style="color: #64748b; padding: 12px;">Belum ada catatan kunjungan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 3. PEMBARUAN KONTEN -->
    <div class="section-title">3. Aktivitas Pembaruan Konten Publikasi</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Jenis Modul Konten</th>
                <th width="30%" class="text-right">Jumlah Dibuat / Diperbarui</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Warta Berita & Artikel Terbit</td>
                <td class="text-right">{{ $content_updates['berita'] }} kali</td>
            </tr>
            <tr>
                <td>Halaman Profil & Statis Instansi</td>
                <td class="text-right">{{ $content_updates['halaman'] }} kali</td>
            </tr>
            <tr>
                <td>Dokumen Informasi Publik & PDF PPID</td>
                <td class="text-right">{{ $content_updates['dokumen'] }} berkas</td>
            </tr>
            <tr>
                <td>Galeri Dokumentasi Foto & Infografis</td>
                <td class="text-right">{{ $content_updates['galeri'] }} foto</td>
            </tr>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td>Total Pembaruan Konten Bulan Ini</td>
                <td class="text-right">{{ $content_updates['total'] }} pembaruan
                    @if($content_updates['quota_limit'])
                        (Batas Kuota Paket: {{ $content_updates['quota_limit'] }})
                    @else
                        (Paket Tanpa Batas)
                    @endif
                </td>
            </tr>
            @if($content_updates['quota_limit'] && isset($content_updates['quota_used']))
                <tr>
                    <td>Pemakaian Kuota Pembaruan Paket (berdasarkan log aktivitas)</td>
                    <td class="text-right"><strong>{{ $content_updates['quota_used'] }} / {{ $content_updates['quota_limit'] }}</strong>
                        @if($content_updates['quota_used'] > $content_updates['quota_limit'])
                            <span style="color: #b91c1c;">(melebihi kuota)</span>
                        @endif
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- 4. PERMOHONAN INFORMASI PPID -->
    <div class="section-title">4. Layanan Permohonan Informasi Publik (UU No. 14 Tahun 2008)</div>
    <p style="font-size: 9pt; color: #475569; margin: 0 0 8px 0;">
        Total permohonan yang masuk pada bulan ini: <strong>{{ $permohonan['total'] }} permohonan</strong>, dengan jumlah yang telah dijawab resmi: <strong>{{ $permohonan['dijawab'] }} permohonan</strong>.
    </p>
    <table class="data-table">
        <thead>
            <tr>
                <th width="8%" class="text-center">No</th>
                <th>Status Pelayanan</th>
                <th width="22%" class="text-right">Jumlah Permohonan</th>
                <th width="22%" class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permohonan['breakdown'] as $idx => $st)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $st['nama'] }}</strong></td>
                    <td class="text-right">{{ $st['jumlah'] }} berkas</td>
                    <td class="text-right">{{ $st['persentase'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(!empty($security))
    <!-- 5. KEAMANAN SISTEM (PAKET GOLD) -->
    <div class="section-title">5. Keamanan Sistem &amp; Ketahanan Portal</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Indikator Keamanan</th>
                <th width="30%" class="text-right">Nilai Periode Ini</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Percobaan login gagal ke panel admin</td>
                <td class="text-right">{{ number_format($security['failed_logins'], 0, ',', '.') }} kali</td>
            </tr>
            <tr>
                <td>Akun dikunci otomatis (gagal login berturut-turut)</td>
                <td class="text-right">{{ $security['accounts_locked'] }} kali</td>
            </tr>
            <tr>
                <td>Upaya login ke akun terkunci yang ditolak</td>
                <td class="text-right">{{ $security['locked_login_blocked'] }} kali</td>
            </tr>
            <tr>
                <td>Alamat IP baru diblokir (otomatis: {{ $security['ip_blocked_auto'] }})</td>
                <td class="text-right">{{ $security['ip_blocked_new'] }} IP</td>
            </tr>
            <tr>
                <td>Total daftar blokir IP yang masih aktif</td>
                <td class="text-right">{{ $security['ip_blocked_active'] }} IP</td>
            </tr>
            <tr>
                <td>Pemeriksaan integritas berkas (pemantau defacement SHA-256)</td>
                <td class="text-right">{{ $security['integrity_checks'] }} kali</td>
            </tr>
            <tr>
                <td>Insiden perubahan berkas terdeteksi</td>
                <td class="text-right" style="{{ $security['integrity_incidents'] > 0 ? 'color:#b91c1c;font-weight:bold;' : '' }}">{{ $security['integrity_incidents'] }} insiden</td>
            </tr>
            <tr>
                <td>Status integritas terakhir</td>
                <td class="text-right">{{ $security['integrity_last_status'] }}{{ $security['integrity_last_checked_at'] ? ' ('.$security['integrity_last_checked_at'].')' : '' }}</td>
            </tr>
            <tr>
                <td>Jadwal pencadangan (backup) otomatis</td>
                <td class="text-right">{{ $security['backup_frequency'] }}</td>
            </tr>
        </tbody>
    </table>
    @if(count($security['top_failed_ips']))
        <p style="font-size: 8.5pt; color: #475569; margin: 0 0 8px 0;">
            Sumber IP dengan percobaan login gagal terbanyak:
            @foreach($security['top_failed_ips'] as $row)
                <code>{{ $row['ip'] }}</code> ({{ $row['jumlah'] }}x){{ !$loop->last ? ', ' : '.' }}
            @endforeach
        </p>
    @endif
    @endif

    <!-- TANDA TANGAN PENGESAHAN -->
    <table class="ttd-table">
        <tr>
            <td width="50%" class="text-center">
                Mengetahui,<br>
                <strong>Atasan PPID</strong>
                <br><br><br><br>
                <u><strong>{{ $instansi['atasan_ppid'] }}</strong></u>
            </td>
            <td width="50%" class="text-center">
                Ditetapkan pada: {{ date('d') }} {{ $nama_bulan }} {{ $year }}<br>
                <strong>Pejabat Pengelola Informasi & Dokumentasi</strong>
                <br><br><br><br>
                <u><strong>{{ $instansi['pejabat_ppid'] }}</strong></u>
            </td>
        </tr>
    </table>

    <!-- FOOTER CATATAN SISTEM -->
    <div class="footer-note">
        Dokumen ini dibuat otomatis oleh Sistem Portal Resmi Instansi Pemerintah &bull; Paket {{ strtoupper(\App\Support\Package::current()) }} &bull; PT Hayat Jaya Teknologi
    </div>

</body>
</html>
