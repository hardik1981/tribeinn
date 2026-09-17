<?php
declare(strict_types=1);

function stayImage(string $file, string $alt, string $loading = 'lazy'): void {
    [$width, $height] = getimagesize(__DIR__ . '/../assets/images/' . $file);
    echo '<img src="' . e(url('assets/images/' . $file)) . '" alt="' . e($alt)
        . '" width="' . $width . '" height="' . $height . '" loading="' . e($loading) . '" decoding="async">';
}

function stayGallery(array $photos, string $gallery, string $label): void {
    $preview = $gallery === 'apartment' ? [0, 1, 2, 4, 9, 12] : [0, 1, 2, 5];
    echo '<div id="' . e($gallery) . '-gallery" class="stay-photo-grid" data-gallery="' . e($gallery) . '" data-gallery-label="' . e($label) . '">';
    foreach ($photos as $index => $photo) {
        $id = $gallery . '-photo-' . ($index + 1);
        $class = !empty($photo['wide']) ? ' stay-photo--wide' : '';
        $class .= isset($photo['crop']) ? ' stay-photo--' . $photo['crop'] : '';
        $class .= in_array($index, $preview, true) ? '' : ' stay-photo--extra';
        echo '<figure class="stay-photo' . e($class) . '">';
        echo '<a class="stay-photo-link" href="' . e(url('assets/images/' . $photo['file'])) . '" aria-label="Enlarge ' . e($photo['title']) . ' photograph" aria-describedby="' . e($id) . '">';
        stayImage($photo['file'], $photo['alt'], $gallery === 'apartment' && $index < 3 ? 'eager' : 'lazy');
        echo '<span class="photo-expand" aria-hidden="true">↗</span></a>';
        echo '<figcaption id="' . e($id) . '"><h3>' . e($photo['title']) . '</h3><p>' . e($photo['description']) . '</p></figcaption></figure>';
    }
    echo '</div>';
    $more = $gallery === 'apartment' ? 'See all 14 details ↓' : 'See more of Rio De Goa ↓';
    $less = $gallery === 'apartment' ? 'Show fewer details ↑' : 'Show less ↑';
    echo '<button type="button" class="gallery-disclosure" aria-expanded="false" aria-controls="' . e($gallery) . '-gallery" data-more="' . e($more) . '" data-less="' . e($less) . '">' . e($more) . '</button>';
}

function communityIcon(string $name): string {
    $paths = [
        'pool' => '<path d="M4 21q4-4 8 0t8 0t8 0t4 0M4 27q4-4 8 0t8 0t8 0t4 0M4 33q4-4 8 0t8 0t8 0t4 0M11 17V6a3 3 0 0 1 6 0m5 11V6a3 3 0 0 1 6 0M11 9h11m-11 5h11"/>',
        'gym' => '<path d="M11 18h14M3 13v10m30-10v10M7 9h4v18H7zM25 9h4v18h-4zM3 18h4m22 0h4"/>',
        'gardens' => '<path d="M18 33V17M18 25C5 26 3 14 5 8c10 0 14 8 13 17Zm0-7C17 7 24 3 31 3c3 10-2 17-13 15ZM9 14l9 11m8-16-8 9"/>',
        'terrace' => '<path d="M4 27h28v6H4zM7 27V16h22v11M4 16h28M10 22h16"/><circle cx="18" cy="7" r="3"/><path d="M18 0v1M9 6h2m14 0h2M11 0l1 2m12-2-1 2"/>',
        'community' => '<path d="M4 30V15l14-11 14 11v15M1 17l17-14 17 14M13 30v-9h10v9"/><circle cx="18" cy="14" r="2"/>',
    ];
    return '<svg viewBox="0 0 36 36" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[$name] . '</svg>';
}
