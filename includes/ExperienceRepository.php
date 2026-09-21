<?php
declare(strict_types=1);

/** File-backed boundary: templates consume normalized records, not storage details. */
final class ExperienceRepository {
    private array $records;

    public function __construct(string $file, ?string $gardenFile = null) {
        $data = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        $garden = $gardenFile ? json_decode(file_get_contents($gardenFile), true, 512, JSON_THROW_ON_ERROR) : [];
        $this->records = [];
        foreach ($data['experiences'] ?? [] as $record) {
            if (!preg_match('/^[a-z0-9-]+$/', $record['slug'] ?? '')) continue;
            if ($record['slug'] === 'japanese-garden' && $garden) {
                $record['images'] = $garden['images'] ?? $record['images'];
                $record['short_description'] = $record['short_description'] ?: ($garden['summary'] ?? '');
            }
            $record['media'] = self::media($record);
            $this->records[] = $record;
        }
        usort($this->records, fn($a, $b) => ($a['sort_order'] ?? PHP_INT_MAX) <=> ($b['sort_order'] ?? PHP_INT_MAX));
    }

    public function all(): array { return $this->records; }
    public function find(string $slug): ?array {
        foreach ($this->records as $record) if ($record['slug'] === $slug) return $record;
        return null;
    }
    public static function youtubeId(string $url): ?string {
        $parts = parse_url($url);
        if (!$parts || !in_array($parts['scheme'] ?? '', ['https', 'http'], true)) return null;
        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');
        if ($host === 'youtu.be') $id = $path;
        elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'www.youtube-nocookie.com'], true)) {
            parse_str($parts['query'] ?? '', $query);
            $id = $path === 'watch' ? ($query['v'] ?? '') : (preg_match('~^(?:embed|shorts)/([^/]+)$~', $path, $match) ? $match[1] : '');
        } else return null;
        return is_string($id) && preg_match('/^[A-Za-z0-9_-]{11}$/', $id) ? $id : null;
    }
    public static function media(array $record): array {
        $items = $record['media'] ?? $record['images'] ?? [];
        $result = [];
        foreach ($items as $item) {
            if (is_string($item)) $item = ['type' => 'image', 'src' => $item];
            $type = $item['type'] ?? 'image';
            if ($type === 'youtube') {
                $id = self::youtubeId($item['url'] ?? '');
                if ($id) $result[] = ['type' => 'youtube', 'id' => $id, 'caption' => $item['caption'] ?? ''];
            } elseif ($type === 'image') {
                $src = $item['src'] ?? '';
                if (!preg_match('~^assets/images/experience/[a-z0-9/_-]+\.webp$~i', $src)) continue;
                $file = __DIR__ . '/../' . $src;
                if (!is_file($file)) continue;
                [$width, $height] = getimagesize($file);
                $result[] = ['type' => 'image', 'src' => $src, 'alt' => $item['alt'] ?? ($record['title'] . ' — ' . preg_replace('/^\d+-/', '', str_replace('-', ' ', pathinfo($src, PATHINFO_FILENAME)))), 'caption' => $item['caption'] ?? '', 'width' => $width, 'height' => $height];
            }
        }
        // Lead with the supplied featured photograph, without duplicating it.
        $featured = $record['featured_image'] ?? '';
        foreach ($result as $i => $item) {
            if (($item['src'] ?? '') === $featured) { array_splice($result, $i, 1); array_unshift($result, $item); break; }
        }
        return $result;
    }
}
