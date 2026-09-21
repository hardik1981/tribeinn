<?php
declare(strict_types=1);

final class GuestGuideRepository {
    private array $data;
    public function __construct(string $source) { $this->data = require $source; }
    public function topics(): array { return $this->data['topics']; }
    public function all(): array {
        $entries = array_values(array_filter($this->data['entries'], fn(array $entry) => $entry['published']));
        usort($entries, fn(array $a, array $b) => $a['sort_order'] <=> $b['sort_order']);
        return $entries;
    }
    public function find(string $slug): ?array {
        foreach ($this->all() as $entry) if ($entry['slug'] === $slug) return $entry;
        return null;
    }
}
