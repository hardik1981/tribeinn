<?php
// Run against the local server: php tools/check-stay.php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/check.php';
[$status, $html] = get('/stay');
verify($status === 200, 'Stay returns 200');
verify(!preg_match('/(?:Warning|Fatal error|Deprecated):/', $html), 'Stay has no PHP errors');
$doc = document($html);
$xpath = new DOMXPath($doc);
verify($doc->getElementsByTagName('h1')->length === 1, 'One Stay h1');
verify(str_contains($html, 'boAt soundbar'), 'Correct boAt soundbar wording');
verify(!preg_match('/Bose|infinity|common-lounge/i', $html), 'No incorrect soundbar or excluded amenity');
verify($xpath->query('//*[@id="main-nav"]//a[@aria-current="page" and @href="/stay"]')->length === 1, 'Stay active navigation');
verify($doc->getElementById('rio-de-goa') !== null && $doc->getElementById('why-stay-longer') !== null, 'Homepage deep-link anchors exist');
verify($xpath->query('//*[@data-gallery="apartment"]//figure')->length === 14, '14 visible apartment photos');
verify($xpath->query('//*[@data-gallery="community"]//figure')->length === 6, '6 visible Rio photos');
verify($xpath->query('//img[contains(@src,"illustrations/utilities/")]')->length === 6, 'All six supplied utility icons displayed');
verify($xpath->query('//img[contains(@src,"illustrations/why-stay-longer/")]')->length === 7, 'All seven supplied long-stay illustrations displayed');
foreach ($xpath->query('//*[@data-gallery]//figure') as $figure) {
    verify($xpath->query('.//figcaption/h3', $figure)->length === 1 && $xpath->query('.//figcaption/p', $figure)->length === 1, 'Caption and description outside lightbox');
    $link = $xpath->query('.//a', $figure)->item(0);
    $img = $xpath->query('.//img', $figure)->item(0);
    verify($link->getAttribute('href') === $img->getAttribute('src'), 'Lightbox uses same original photo as grid');
}
$sourceRoots = [
    'illustrations/utilities/' => __DIR__ . '/../source-assets/utility-icons/',
    'illustrations/why-stay-longer/' => __DIR__ . '/../source-assets/why-stay-longer/',
];
$images = [];
foreach ($doc->getElementsByTagName('img') as $img) {
    $src = $img->getAttribute('src');
    if ($src === '') { continue; } // The lightbox acquires its source only when opened.
    verify(get($src)[0] === 200, "Stay asset resolves: $src");
    verify((int) $img->getAttribute('width') > 0 && (int) $img->getAttribute('height') > 0, 'Intrinsic dimensions set');
    verify($img->hasAttribute('alt'), 'Alt attribute present, including decorative utilities');
    $file = substr($src, strlen('/assets/images/'));
    $local = __DIR__ . '/../assets/images/' . $file;
    $source = __DIR__ . '/../source-assets/tribeinn-website-assets-v2/assets/images/' . $file;
    foreach ($sourceRoots as $prefix => $root) {
        if (str_starts_with($file, $prefix)) { $source = $root . substr($file, strlen($prefix)); break; }
    }
    verify(is_file($source), "Supplied source exists: $file");
    verify(hash_file('sha256', $local) === hash_file('sha256', $source), "Supplied asset unchanged: $file");
    $images[] = $src;
}
verify(count(array_unique($images)) === 33, '20 unique photographs and 13 supplied illustrated assets');
foreach ($doc->getElementsByTagName('a') as $link) {
    $parts = parse_url($link->getAttribute('href'));
    $path = $parts['path'] ?? '/stay';
    [$status, $target] = get($path);
    verify($status === 200, 'Stay link resolves: ' . $link->getAttribute('href'));
    if (isset($parts['fragment'])) verify(document($target)->getElementById($parts['fragment']) !== null, 'Stay anchor resolves');
}
$home = get('/')[1];
verify(!str_contains($home, 'inner.css') && !str_contains($home, 'stay.css') && !str_contains($home, 'lightbox.js'), 'Stay assets isolated from Home');
$baseline = __DIR__ . '/../var/home-before-stay.html';
if (is_file($baseline)) {
    $normalise = static fn(string $text): string => trim(preg_replace('/>\s+</', '><', $text));
    verify($normalise(file_get_contents($baseline)) === $normalise($home), 'Homepage HTML unchanged apart from formatting whitespace');
}
echo "PASS: $checks total Home + Stay checks; interactive browser tests are separate.\n";
