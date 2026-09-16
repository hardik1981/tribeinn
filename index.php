<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';
$path = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if ($basePath !== '' && str_starts_with($path, $basePath . '/')) {
    $path = substr($path, strlen($basePath));
}
$route = trim($path, '/');
if ($route === 'index.php') { $route = ''; }
$found = array_key_exists($route, $routes);
if (!$found) { http_response_code(404); }
$pageTitle = $found ? $routes[$route] : 'Page not found';
require __DIR__ . '/includes/header.php';
require __DIR__ . ($route === '' ? '/pages/home.php' : '/pages/placeholder.php');
require __DIR__ . '/includes/footer.php';
