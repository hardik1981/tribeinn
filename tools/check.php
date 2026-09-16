<?php
// HTTP smoke check. Start the local server before running: php tools/check.php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$origin = rtrim($argv[1] ?? 'http://127.0.0.1:8080', '/');
$cache = [];
$checks = 0;
function verify(bool $ok, string $message): void {
    global $checks;
    $checks++;
    if (!$ok) { fwrite(STDERR, "FAIL: $message\n"); exit(1); }
}
function get(string $path): array {
    global $origin, $cache;
    if (isset($cache[$path])) { return $cache[$path]; }
    $body = file_get_contents($origin . $path, false, stream_context_create(['http' => ['ignore_errors' => true]]));
    preg_match('/\s(\d{3})\s/', $http_response_header[0] ?? '', $status);
    return $cache[$path] = [(int) ($status[1] ?? 0), (string) $body];
}
function document(string $html): DOMDocument {
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    return $doc;
}
[$status, $html] = get('/');
verify($status === 200, 'Homepage returns 200');
verify(!preg_match('/(?:Warning|Fatal error|Deprecated):/', $html), 'No PHP errors in homepage');
$doc = document($html);
verify($doc->getElementsByTagName('h1')->length === 1, 'Exactly one homepage h1');
foreach ($doc->getElementsByTagName('a') as $link) {
    $parts = parse_url($link->getAttribute('href'));
    $path = $parts['path'] ?? '/';
    [$status, $target] = get($path);
    verify($status === 200, "Link resolves: $path");
    if (isset($parts['fragment'])) {
        $targetDoc = document($target);
        verify($targetDoc->getElementById($parts['fragment']) !== null, 'Anchor resolves: ' . $link->getAttribute('href'));
    }
}
foreach ($doc->getElementsByTagName('img') as $img) {
    verify($img->getAttribute('alt') !== '', 'Descriptive image alt text');
    verify((int) $img->getAttribute('width') > 0 && (int) $img->getAttribute('height') > 0, 'Image dimensions reserve space');
    $src = $img->getAttribute('src');
    verify(get($src)[0] === 200, "Image resolves: $src");
}
verify(get('/missing-page')[0] === 404, 'Unknown route returns 404');
verify(get('/config/properties.php')[0] === 404, 'Local server does not expose configuration');
verify(get('/source-assets/tribeinn-website-assets-v2/README.md')[0] === 404, 'Local server does not expose source archive');
$properties = require __DIR__ . '/../config/properties.php';
foreach ($properties['the-beginning']['images'] as [$file]) {
    $production = __DIR__ . '/../assets/images/' . $file;
    $source = __DIR__ . '/../source-assets/tribeinn-website-assets-v2/assets/images/' . $file;
    verify(is_file($production), "Configured asset exists: $file");
    if (is_file($source)) {
        verify(hash_file('sha256', $production) === hash_file('sha256', $source), "V2 photo unchanged: $file");
    }
}
echo "PASS: $checks checks (HTTP routes, anchors, images, markup and available V2 source hashes).\n";
