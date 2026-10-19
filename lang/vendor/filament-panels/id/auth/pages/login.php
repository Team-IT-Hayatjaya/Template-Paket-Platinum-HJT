<?php

return [
    'title' => 'Masuk Panel Kendali Admin',

    'heading' => 'Autentikasi Aparatur',

    'actions' => [
        'register' => [
            'before' => 'atau',
            'label' => 'daftarkan akun baru',
        ],
        'request_password_reset' => [
            'label' => 'Lupa kata sandi?',
        ],
    ],

    'form' => [
        'email' => [
            'label' => 'Alamat Email Resmi',
        ],
        'password' => [
            'label' => 'Kata Sandi',
        ],
        'remember' => [
            'label' => 'Ingat saya di perangkat ini',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'Masuk ke Panel Kendali',
            ],
        ],
    ],

    'multi_factor' => [
        'heading' => 'Verifikasi Identitas Multi-Faktor (2FA)',
        'subheading' => 'Untuk melanjutkan, silakan masukkan kode verifikasi autentikasi Anda.',
        'form' => [
            'provider' => [
                'label' => 'Metode verifikasi',
            ],
            'actions' => [
                'authenticate' => [
                    'label' => 'Konfirmasi & Masuk',
                ],
            ],
        ],
    ],

    'messages' => [
        'failed' => 'Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.',
    ],

    'notifications' => [
        'throttled' => [
            'title' => 'Terlalu banyak percobaan masuk',
            'body' => 'Silakan coba lagi dalam :seconds detik demi keamanan sistem.',
        ],
    ],
];
