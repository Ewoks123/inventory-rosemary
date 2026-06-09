<?php

// Vercel serverless entry point for Laravel

// Buat direktori yang dibutuhkan di /tmp (writable di Vercel)
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Set environment variables untuk path /tmp
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('CACHE_DIR=/tmp/bootstrap/cache');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');

// Override storage path ke /tmp
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// Set APP_URL dinamis sesuai domain Vercel (gunakan x-forwarded-proto untuk HTTPS)
if (!empty($_SERVER['HTTP_HOST'])) {
    $proto = $_SERVER['HTTP_X_FORWARDED_PROTO']
        ?? ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'https');
    $_ENV['APP_URL'] = $proto . '://' . $_SERVER['HTTP_HOST'];
    putenv('APP_URL=' . $_ENV['APP_URL']);
    $_SERVER['HTTPS'] = 'on';
}

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
