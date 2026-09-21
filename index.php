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
$isExperience = $route === 'experience' || str_starts_with($route, 'experience/');
$experience = null;
if ($isExperience) {
    require __DIR__ . '/includes/ExperienceRepository.php';
    require __DIR__ . '/includes/experience-components.php';
    $experienceRepository = new ExperienceRepository(__DIR__ . '/config/experience-data.json', __DIR__ . '/config/japanese-garden.json');
    $experiences = $experienceRepository->all();
    if ($route !== 'experience') {
        $experience = $experienceRepository->find(substr($route, strlen('experience/')));
        $found = $experience !== null;
    }
}
if (!$found) { http_response_code(404); }
$pageTitle = $found ? ($experience['title'] ?? $routes[$route]) : 'Page not found';
$isInner = $route !== '';
if ($route === 'stay') {
    $stay = require __DIR__ . '/config/stays/' . $property['stay_content'];
    require __DIR__ . '/includes/stay-gallery.php';
}
require __DIR__ . '/includes/header.php';
require __DIR__ . match (true) {
    $route === '' => '/pages/home.php',
    $route === 'stay' => '/pages/stay.php',
    $isExperience && $found => $experience ? '/pages/experience-story.php' : '/pages/experience.php',
    default => '/pages/placeholder.php',
};
require __DIR__ . '/includes/footer.php';
