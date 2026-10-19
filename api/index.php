<?php

// Pastikan direktori sementara /tmp tersedia untuk Laravel (filesystem serverless Vercel read-only)
$tempDirs = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions',
    '/tmp/logs',
];

foreach ($tempDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Inisialisasi Database SQLite di /tmp jika driver SQLite digunakan di Vercel
$dbPath = '/tmp/database_platinum.sqlite';
if (!file_exists($dbPath)) {
    $sourceDb = __DIR__ . '/../database/database_platinum.sqlite';
    if (file_exists($sourceDb)) {
        @copy($sourceDb, $dbPath);
    } else {
        @touch($dbPath);
    }
}
putenv("DB_DATABASE={$dbPath}");
$_ENV['DB_DATABASE'] = $dbPath;

// Fallback APP_KEY jika belum diset di Vercel env
if (!getenv('APP_KEY')) {
    putenv('APP_KEY=base64:SmdaQpG8y5cxK49o8+mcbaO9NYxzBzEHhXVpBTZS5zU=');
    $_ENV['APP_KEY'] = 'base64:SmdaQpG8y5cxK49o8+mcbaO9NYxzBzEHhXVpBTZS5zU=';
}

// Forward request ke entry point aplikasi Laravel
require __DIR__ . '/../public/index.php';
