<?php

return [
    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => 'Lewati ke konten utama',
    ],

    'actions' => [
        'billing' => [
            'label' => 'Kelola langganan',
        ],

        'logout' => [
            'label' => 'Keluar',
        ],

        'open_database_notifications' => [
            'label' => 'Notifikasi',
            'label_with_unread_count' => '{1} Notifikasi, :count belum dibaca|[2,*] Notifikasi, :count belum dibaca',
        ],

        'open_user_menu' => [
            'label' => 'Menu pengguna',
        ],

        'sidebar' => [
            'collapse' => [
                'label' => 'Tutup bilah samping',
            ],
            'expand' => [
                'label' => 'Buka bilah samping',
            ],
        ],

        'theme_switcher' => [
            'label' => 'Tema',
            'dark' => [
                'label' => 'Aktifkan tema gelap',
            ],
            'light' => [
                'label' => 'Aktifkan tema terang',
            ],
            'system' => [
                'label' => 'Gunakan tema sistem',
            ],
        ],
    ],

    'navigation' => [
        'label' => 'Navigasi bilah samping',
    ],

    'topbar' => [
        'label' => 'Bilah atas',
    ],

    'avatar' => [
        'alt' => 'Foto profil :name',
    ],

    'logo' => [
        'alt' => 'Logo :name',
    ],

    'tenant_menu' => [
        'search_field' => [
            'label' => 'Cari instansi',
            'placeholder' => 'Cari',
        ],
    ],
];
