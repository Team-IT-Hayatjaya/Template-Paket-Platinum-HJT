<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peringatan Keamanan Portal</title>
</head>
<body style="margin:0;padding:24px;background:#f4f6fb;font-family:Arial,Helvetica,sans-serif;color:#131c28;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;border-top:6px solid #ba1a1a;">
        <tr>
            <td style="padding:24px 28px;">
                <h1 style="margin:0 0 8px;font-size:20px;color:#93000a;">Peringatan: Perubahan Berkas Terdeteksi</h1>
                <p style="margin:0 0 16px;line-height:1.6;">
                    Pemantau integritas (checksum SHA-256) pada portal
                    <strong>{{ $setting->institution_name ?? config('app.name') }}</strong>
                    mendeteksi <strong>{{ $check->totalChanges() }}</strong> berkas yang berbeda dari baseline tepercaya
                    pada {{ $check->checked_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}.
                </p>

                @foreach ([
                    'Berkas Diubah' => $check->modified_files ?? [],
                    'Berkas Hilang / Dihapus' => $check->missing_files ?? [],
                    'Berkas Baru Tidak Dikenal' => $check->new_files ?? [],
                ] as $label => $files)
                    @if (count($files))
                        <h2 style="margin:16px 0 6px;font-size:15px;">{{ $label }} ({{ count($files) }})</h2>
                        <ul style="margin:0;padding-left:20px;font-family:Consolas,monospace;font-size:13px;line-height:1.5;">
                            @foreach (array_slice($files, 0, 50) as $file)
                                <li>{{ $file }}</li>
                            @endforeach
                            @if (count($files) > 50)
                                <li>… dan {{ count($files) - 50 }} berkas lainnya</li>
                            @endif
                        </ul>
                    @endif
                @endforeach

                <p style="margin:20px 0 0;line-height:1.6;">
                    <strong>Tindakan yang disarankan:</strong> periksa perubahan di atas. Bila perubahan tidak sah (indikasi defacement),
                    pulihkan berkas dari cadangan/repositori. Bila perubahan merupakan pembaruan resmi, perbarui baseline melalui
                    menu <em>Keamanan &rsaquo; Pusat Keamanan</em> di panel admin.
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 28px;background:#f8f9ff;font-size:12px;color:#43474e;">
                Email ini dikirim otomatis oleh sistem keamanan portal. Mohon tidak membalas email ini.
            </td>
        </tr>
    </table>
</body>
</html>
