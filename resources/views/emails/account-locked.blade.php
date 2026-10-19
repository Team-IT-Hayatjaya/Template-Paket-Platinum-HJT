<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peringatan Keamanan: Akun Dikunci Sementara</title>
</head>
<body style="margin:0;padding:24px;background:#f4f6fb;font-family:Arial,Helvetica,sans-serif;color:#131c28;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;border-top:6px solid #ba1a1a;">
        <tr>
            <td style="padding:24px 28px;">
                <h1 style="margin:0 0 8px;font-size:20px;color:#93000a;">Peringatan Keamanan: Akun Dikunci Sementara</h1>
                <p style="margin:0 0 16px;line-height:1.6;">
                    Pemberitahuan resmi keamanan untuk Administrator <strong>{{ $setting->institution_name ?? config('app.name') }}</strong>.
                </p>

                <div style="background:#fff8f6;border-left:4px solid #ba1a1a;padding:16px;margin:16px 0;border-radius:4px;">
                    <p style="margin:0 0 6px;"><strong>Rincian Insiden Percobaan Login:</strong></p>
                    <ul style="margin:0;padding-left:20px;line-height:1.6;font-size:13px;">
                        <li><strong>Akun Terkena Dampak:</strong> {{ $user->email }} ({{ $user->name }})</li>
                        <li><strong>Jumlah Percobaan Gagal:</strong> {{ $attempts }} kali berturut-turut</li>
                        <li><strong>Durasi Penguncian Akun:</strong> {{ $decayMinutes }} menit</li>
                        <li><strong>Waktu Kejadian:</strong> {{ now()->timezone(config('app.timezone'))->format('d/m/Y H:i:s') }} WIB</li>
                        <li><strong>Alamat IP Penyerang:</strong> <code style="font-family:monospace;background:#eee;padding:2px 4px;border-radius:3px;">{{ $ip ?? '-' }}</code></li>
                    </ul>
                </div>

                <p style="margin:16px 0;line-height:1.6;font-size:13px;">
                    Sistem perlindungan otomatis (*Account Lockout*) telah menonaktifkan sementara proses autentikasi untuk akun tersebut guna mencegah serangan *brute force* atau *credential stuffing*.
                </p>

                <p style="margin:20px 0 0;line-height:1.6;font-size:13px;">
                    <strong>Tindakan yang Disarankan:</strong>
                </p>
                <ol style="margin:0;padding-left:20px;font-size:13px;line-height:1.6;">
                    <li>Periksa apakah alamat IP di atas berasal dari aktivitas staf resmi yang lupa kata sandi atau potensi serangan luar.</li>
                    <li>Jika merupakan serangan mencurigakan, masukkan alamat IP tersebut ke <strong>Daftar Blokir IP</strong> melalui menu <em>Pusat Keamanan</em> di panel admin.</li>
                    <li>Buka kunci akun secara manual melalui panel admin jika pengguna terbukti sah.</li>
                </ol>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 28px;background:#f8f9ff;font-size:12px;color:#43474e;">
                Email otomatis dari Sistem Keamanan Lanjutan &bull; {{ $setting->institution_name ?? config('app.name') }}.
            </td>
        </tr>
    </table>
</body>
</html>
