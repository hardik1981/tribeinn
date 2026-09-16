<?php
declare(strict_types=1);

$properties = require __DIR__ . '/../config/properties.php';
$property = $properties['the-beginning'];
$basePath = rtrim((string) (getenv('TRIBE_BASE_PATH') ?: ''), '/');
$routes = [
    '' => 'Home', 'stay' => 'Stay', 'experience' => 'Experience',
    'guest-guide' => 'Guest Guide', 'about' => 'About',
    'check-dates' => 'Check Dates', 'faq-policies' => 'FAQ & Policies',
];

function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function url(string $path = ''): string {
    global $basePath;
    return $basePath . '/' . ltrim($path, '/');
}
function photo(string $key, string $class = ''): void {
    global $property;
    [$file, $alt] = $property['images'][$key];
    [$width, $height] = getimagesize(__DIR__ . '/../assets/images/' . $file);
    // The supplied WebPs are used unchanged; explicit sizes reserve layout space.
    echo '<img src="' . e(url('assets/images/' . $file)) . '" alt="' . e($alt)
        . '" width="' . $width . '" height="' . $height . '" class="' . e($class)
        . '" loading="lazy" decoding="async">';
}
function arrow(): string { return '<span aria-hidden="true">↗</span>'; }
function icon(string $name): string {
    $paths = [
        'work' => '<rect x="6" y="7" width="24" height="17" rx="2"/><path d="M3 29h30M13 29l1-5m8 5-1-5"/>',
        'stay' => '<path d="M7 9h22v22H7zM12 5v8m12-8v8M7 17h22m-15 6h8m-8 4h5"/>',
        'nature' => '<path d="M9 28C4 12 15 5 29 6c0 15-5 23-20 22ZM9 28l14-15M7 31l2-3"/>',
        'everyone' => '<circle cx="13" cy="12" r="5"/><path d="M3 30v-3a10 10 0 0 1 20 0v3m0-23a5 5 0 0 1 0 10m5 13v-4a9 9 0 0 0-4-8"/>',
        'sun' => '<circle cx="18" cy="18" r="7"/><path d="M18 1v5m0 24v5M1 18h5m24 0h5M6 6l4 4m16 16 4 4M6 30l4-4M26 10l4-4"/>',
    ];
    return '<svg viewBox="0 0 36 36" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[$name] . '</svg>';
}
