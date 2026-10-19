<?php

// Pastikan direktori sementara /tmp tersedia untuk Laravel (filesystem serverless Vercel read-only)
$tempDirs = [
    '/tmp/views',
    '/tmp/cache',
];

foreach ($tempDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Forward request ke entry point aplikasi Laravel
require __DIR__ . '/../public/index.php';
