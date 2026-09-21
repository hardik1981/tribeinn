<?php
// Local PHP server only: php -S 127.0.0.1:8080 router.php
declare(strict_types=1);
$path = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$base = rtrim((string) (getenv('TRIBE_BASE_PATH') ?: ''), '/');
if ($base !== '' && str_starts_with($path, $base . '/')) { $path = substr($path, strlen($base)); }
$file = realpath(__DIR__ . $path);
$assets = realpath(__DIR__ . '/assets');
if ($file && is_file($file) && str_starts_with($file, $assets . DIRECTORY_SEPARATOR)) {
    $mime = ['css' => 'text/css', 'js' => 'text/javascript', 'webp' => 'image/webp', 'png' => 'image/png', 'svg' => 'image/svg+xml'];
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    if (isset($mime[$extension])) {
        header('Content-Type: ' . $mime[$extension]);
        readfile($file);
        return true;
    }
}
require __DIR__ . '/index.php';
