<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanggapan Resmi Permohonan Informasi Publik</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1e293b; background-color: #f8fafc; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
        <div style="background-color: #14304F; color: #ffffff; padding: 20px 24px; text-align: center;">
            <h2 style="margin: 0; font-size: 20px;">PEJABAT PENGELOLA INFORMASI DAN DOKUMENTASI (PPID)</h2>
            <p style="margin: 4px 0 0; font-size: 14px; color: #C9A227;">{{ $setting->institution_name ?? 'Pemerintah Instansi' }}</p>
        </div>

        <div style="padding: 24px;">
            <p>Yth. <strong>{{ $permohonan->nama_pemohon }}</strong>,</p>

            <p>Sehubungan dengan permohonan informasi publik yang telah Anda ajukan dengan nomor registrasi <strong>{{ $permohonan->nomor_registrasi }}</strong>, bersama ini kami sampaikan tanggapan resmi dari Pejabat Pengelola Informasi dan Dokumentasi (PPID).</p>

            <div style="background-color: #f1f5f9; border-left: 4px solid #14304F; padding: 16px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0 0 8px;"><strong>Status Penyelesaian Permohonan:</strong></p>
                <p style="margin: 4px 0;">Nomor Registrasi: <strong style="color: #14304F;">{{ $permohonan->nomor_registrasi }}</strong></p>
                <p style="margin: 4px 0;">Status Terkini: <strong>{{ $permohonan->status?->nama ?? 'Dijawab' }}</strong></p>
                <p style="margin: 4px 0;">Tanggal Tanggapan: <strong>{{ ($permohonan->tanggal_dijawab ?? now())->translatedFormat('d F Y') }}</strong></p>
            </div>

            @if(!empty($permohonan->jawaban_teks))
                <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; padding: 16px; border-radius: 6px; margin-bottom: 20px;">
                    <p style="margin: 0 0 8px; font-weight: bold; color: #065f46;">Uraian Tanggapan / Keterangan Resmi PPID:</p>
                    <p style="margin: 0; color: #047857; white-space: pre-line;">{{ $permohonan->jawaban_teks }}</p>
                </div>
            @endif

            @if(!empty($permohonan->jawaban_file_path))
                <p style="background-color: #eff6ff; padding: 12px; border-radius: 4px; border: 1px solid #bfdbfe; font-size: 13px;">
                    📎 <strong>Lampiran Dokumen:</strong> Berkas dokumen informasi publik yang diminta telah dilampirkan pada email ini.
                </p>
            @endif

            <p>Apabila Anda merasa tanggapan atau informasi yang diberikan belum memuaskan, sesuai dengan UU KIP Pasal 35, Anda berhak menyampaikan pengajuan keberatan kepada Atasan PPID dalam waktu 30 (tiga puluh) hari kerja sejak diterimanya tanggapan ini.</p>

            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">

            <p style="font-size: 12px; color: #64748b; margin: 0;">
                Sekretariat PPID &bull; {{ $setting->institution_name ?? 'Pemerintah Instansi' }}<br>
                Alamat: {{ $setting->address ?? 'Kantor Pelayanan Terpadu' }}<br>
                Email Resmi: {{ $setting->email ?? 'ppid@instansi.go.id' }}
            </p>
        </div>
    </div>
</body>
</html>
