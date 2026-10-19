<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Keamanan Aktif (Paket Gold ke atas)
|--------------------------------------------------------------------------
|
| Seluruh mekanisme di bawah hanya aktif bila Package::has('security_hardening')
| bernilai true. Pada paket Silver konfigurasi ini diabaikan.
|
*/

return [

    /*
    | Penguncian akun setelah percobaan login gagal berturut-turut.
    */
    'lockout' => [
        'max_attempts' => (int) env('SECURITY_LOCKOUT_MAX_ATTEMPTS', 5),
        'decay_minutes' => (int) env('SECURITY_LOCKOUT_MINUTES', 15),
    ],

    /*
    | Pemblokiran IP otomatis bila satu alamat IP melakukan terlalu banyak
    | percobaan login gagal (lintas akun) dalam jendela waktu tertentu.
    */
    'ip_auto_block' => [
        'enabled' => (bool) env('SECURITY_IP_AUTO_BLOCK', true),
        'max_failures' => (int) env('SECURITY_IP_AUTO_BLOCK_FAILURES', 20),
        'window_minutes' => (int) env('SECURITY_IP_AUTO_BLOCK_WINDOW', 60),
        'block_hours' => (int) env('SECURITY_IP_AUTO_BLOCK_HOURS', 24),
    ],

    /*
    | Pemantau defacement berbasis checksum SHA-256.
    | Path relatif terhadap base_path(). Baseline disimpan di disk 'local'
    | (storage non-publik).
    */
    'integrity' => [
        'disk' => 'local',
        'baseline_file' => 'security/integrity-baseline.json',
        'paths' => [
            'resources/views',
            'routes',
            'public/index.php',
            'public/.htaccess',
            'public/robots.txt',
            'bootstrap/app.php',
            'app/Http',
        ],
        'extensions' => ['php', 'blade.php', 'htaccess', 'txt', 'html', 'js', 'css'],
        'alert_email' => env('SECURITY_ALERT_EMAIL'),
    ],
];
