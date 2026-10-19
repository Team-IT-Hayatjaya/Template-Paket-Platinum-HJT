<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Registrasi Permohonan Informasi Publik</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1e293b; background-color: #f8fafc; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
        <div style="background-color: #14304F; color: #ffffff; padding: 20px 24px; text-align: center;">
            <h2 style="margin: 0; font-size: 20px;">PEJABAT PENGELOLA INFORMASI DAN DOKUMENTASI (PPID)</h2>
            <p style="margin: 4px 0 0; font-size: 14px; color: #C9A227;">{{ $setting->institution_name ?? 'Pemerintah Instansi' }}</p>
        </div>

        <div style="padding: 24px;">
            <p>Yth. <strong>{{ $permohonan->nama_pemohon }}</strong>,</p>

            <p>Terima kasih. Permohonan informasi publik Anda telah berhasil kami terima dan diregistrasikan ke dalam sistem PPID sesuai dengan amanat <strong>Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik</strong>.</p>

            <div style="background-color: #f1f5f9; border-left: 4px solid #14304F; padding: 16px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0 0 8px;"><strong>Tanda Bukti Registrasi:</strong></p>
                <p style="margin: 4px 0;">Nomor Registrasi: <strong style="color: #14304F; font-size: 16px;">{{ $permohonan->nomor_registrasi }}</strong></p>
                <p style="margin: 4px 0;">Tanggal Pengajuan: <strong>{{ $permohonan->created_at->translatedFormat('d F Y, H:i') }} WIB</strong></p>
                <p style="margin: 4px 0;">Informasi yang Diminta: <em>"{{ $permohonan->rincian_informasi }}"</em></p>
                <p style="margin: 4px 0;">Tujuan Penggunaan: <em>"{{ $permohonan->tujuan_penggunaan }}"</em></p>
                <p style="margin: 4px 0;">Tenggat Waktu Respons: <strong>{{ $permohonan->tenggat_jawab?->translatedFormat('d F Y') }} (Maks. 10 Hari Kerja)</strong></p>
            </div>

            @if(\App\Support\Package::has('ppid_tracking'))
            <div style="margin: 24px 0; text-align: center;">
                <a href="{{ route('ppid.lacak', ['nomor_registrasi' => $permohonan->nomor_registrasi, 'email' => $permohonan->email]) }}"
                   style="background-color: #14304F; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; display: inline-block;">
                    Lacak Status Permohonan Online
                </a>
                <p style="font-size: 12px; color: #64748b; margin-top: 8px;">
                    Anda juga dapat memasukkan Nomor Registrasi <strong>{{ $permohonan->nomor_registrasi }}</strong> secara manual pada menu <em>Lacak Permohonan</em> di portal resmi.
                </p>
            </div>
            @endif

            <p>Petugas PPID kami akan memverifikasi dan memproses permohonan Anda dalam kurun waktu maksimal <strong>10 (sepuluh) hari kerja</strong>. Tanggapan tertulis resmi dan/atau dokumen yang diminta akan kami sampaikan secara langsung melalui email ini.</p>

            <p>Simpan email ini beserta nomor registrasi Anda sebagai bukti pengajuan resmi.</p>

            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">

            <p style="font-size: 12px; color: #64748b; margin: 0;">
                Sekretariat PPID &bull; {{ $setting->institution_name ?? 'Pemerintah Instansi' }}<br>
                Alamat: {{ $setting->address ?? 'Kantor Pelayanan Terpadu' }}<br>
                Email Resmi: {{ $setting->email ?? 'ppid@instansi.go.id' }} | Telp: {{ $setting->phone ?? '-' }}
            </p>
        </div>
    </div>
</body>
</html>
