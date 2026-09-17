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
$isInner = $route !== '';
if ($route === 'stay') {
    $stay = require __DIR__ . '/config/stays/' . $property['stay_content'];
    require __DIR__ . '/includes/stay-gallery.php';
}
require __DIR__ . '/includes/header.php';
require __DIR__ . match ($route) {
    '' => '/pages/home.php',
    'stay' => '/pages/stay.php',
    default => '/pages/placeholder.php',
};
require __DIR__ . '/includes/footer.php';
