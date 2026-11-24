<?php

// Prepare writable /tmp directories for Laravel on Vercel serverless environment
$storagePath = '/tmp/storage';
$dirs = [
    $storagePath . '/framework/views',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/cache/data',
    $storagePath . '/logs',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Copy starter SQLite database with all demo data to writable /tmp
if (!file_exists('/tmp/database.sqlite')) {
    if (file_exists(__DIR__ . '/../database/database.sqlite')) {
        @copy(__DIR__ . '/../database/database.sqlite', '/tmp/database.sqlite');
    } else {
        @touch('/tmp/database.sqlite');
    }
}

// Forward request to Laravel public/index.php
require __DIR__ . '/../public/index.php';
