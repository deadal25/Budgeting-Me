<?php

// 1. Prepare writable /tmp directories for Vercel Serverless environment
$tmpStorage = '/tmp/storage';
$dirs = [
    $tmpStorage . '/app/private',
    $tmpStorage . '/app/public',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/logs',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// 2. Instruct Laravel to use /tmp for storage and bootstrap caches
$_ENV['LARAVEL_STORAGE_PATH'] = $tmpStorage;
$_SERVER['LARAVEL_STORAGE_PATH'] = $tmpStorage;
putenv("LARAVEL_STORAGE_PATH={$tmpStorage}");

$_ENV['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/bootstrap/cache/events.php';
$_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';

putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");

// 3. Handle SQLite Database in /tmp if using sqlite driver
$dbConnection = getenv('DB_CONNECTION') ?: 'sqlite';

if ($dbConnection === 'sqlite') {
    $defaultSqlite = __DIR__ . '/../database/database.sqlite';
    $tmpSqlite = '/tmp/database.sqlite';

    if (!file_exists($tmpSqlite)) {
        if (file_exists($defaultSqlite) && filesize($defaultSqlite) > 0) {
            @copy($defaultSqlite, $tmpSqlite);
        } else {
            @touch($tmpSqlite);
        }
    }

    if (file_exists($tmpSqlite)) {
        $_ENV['DB_DATABASE'] = $tmpSqlite;
        $_SERVER['DB_DATABASE'] = $tmpSqlite;
        putenv("DB_DATABASE={$tmpSqlite}");
    }
}

// 4. Delegate request to Laravel's public/index.php
require __DIR__ . '/../public/index.php';
