<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembaruan Status Permohonan Informasi Publik</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1e293b; background-color: #f8fafc; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden;">
        <div style="background-color: #14304F; color: #ffffff; padding: 20px 24px; text-align: center;">
            <h2 style="margin: 0; font-size: 18px; letter-spacing: 0.5px;">PEJABAT PENGELOLA INFORMASI DAN DOKUMENTASI (PPID)</h2>
            <p style="margin: 4px 0 0; font-size: 14px; color: #C9A227; font-weight: bold;">{{ $setting->institution_name ?? config('app.name', 'Portal Resmi Pemerintah') }}</p>
        </div>

        <div style="padding: 24px;">
            <p style="margin-top: 0;">Yth. <strong>{{ $permohonan->nama_pemohon }}</strong>,</p>

            <p>Pemberitahuan resmi mengenai perkembangan tindak lanjut permohonan informasi publik Anda pada sistem PPID:</p>

            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #14304F; padding: 18px; margin: 20px 0; border-radius: 6px;">
                <p style="margin: 0 0 10px; font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: bold; letter-spacing: 0.5px;">
                    Rincian Status Terkini:
                </p>
                <p style="margin: 4px 0;">Nomor Registrasi: <strong style="color: #14304F; font-size: 16px; font-family: monospace;">{{ $permohonan->nomor_registrasi }}</strong></p>
                
                <p style="margin: 8px 0 4px;">Status Pembaruan: 
                    <span style="display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase; background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">
                        {{ $permohonan->status?->nama ?? 'Diproses' }}
                    </span>
                </p>

                @if(!empty($permohonan->status?->deskripsi))
                    <p style="margin: 4px 0 10px; font-size: 13px; color: #475569; font-style: italic;">
                        {{ $permohonan->status->deskripsi }}
                    </p>
                @endif

                @if(!empty($keterangan))
                    <div style="margin-top: 10px; padding: 10px; background-color: #ffffff; border-radius: 4px; border: 1px dashed #cbd5e1;">
                        <strong style="font-size: 12px; color: #334155;">Catatan Tindak Lanjut:</strong>
                        <p style="margin: 4px 0 0; font-size: 13px; color: #1e293b;">{{ $keterangan }}</p>
                    </div>
                @endif

                <p style="margin: 10px 0 4px; font-size: 13px;">Informasi Diminta: <em>"{{ Str::limit($permohonan->rincian_informasi, 120) }}"</em></p>
                <p style="margin: 4px 0; font-size: 13px;">Tenggat Waktu Respons: <strong>{{ $permohonan->tenggat_jawab?->translatedFormat('d F Y') }}</strong></p>
            </div>

            <p style="margin: 20px 0;">Anda dapat memantau linimasa lengkap, sisa hari kerja tenggat, serta mengunduh dokumen jawaban resmi secara mandiri melalui tautan di bawah ini:</p>

            <div style="text-align: center; margin: 28px 0;">
                <a href="{{ route('ppid.lacak', ['nomor_registrasi' => $permohonan->nomor_registrasi, 'email' => $permohonan->email]) }}"
                   style="display: inline-block; background-color: #14304F; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    &rarr; Lacak Status Permohonan Online
                </a>
            </div>

            <p style="font-size: 12px; color: #64748b; line-height: 1.5;">
                Jika tombol di atas tidak dapat diklik, salin dan tempelkan tautan berikut ke peramban web Anda:<br>
                <a href="{{ route('ppid.lacak', ['nomor_registrasi' => $permohonan->nomor_registrasi, 'email' => $permohonan->email]) }}" style="color: #0284c7; word-break: break-all;">
                    {{ route('ppid.lacak', ['nomor_registrasi' => $permohonan->nomor_registrasi, 'email' => $permohonan->email]) }}
                </a>
            </p>

            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">

            <p style="font-size: 11px; color: #64748b; margin: 0;">
                Sekretariat PPID &bull; {{ $setting->institution_name ?? 'Pemerintah Instansi' }}<br>
                Alamat: {{ $setting->address ?? 'Kantor Pelayanan Terpadu' }}<br>
                Email: {{ $setting->email ?? 'ppid@instansi.go.id' }} | Telp: {{ $setting->phone ?? '-' }}
            </p>
        </div>
    </div>
</body>
</html>
