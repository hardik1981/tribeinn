<?php
declare(strict_types=1);

function guideIcon(string $name): string {
    $paths = [
        'key'=>'<circle cx="21" cy="11" r="6"/><path d="m17 16-12 12v4h5v-4h4v-4l6-6"/>',
        'wifi'=>'<path d="M4 12a22 22 0 0 1 28 0M9 18a14 14 0 0 1 18 0M14 24a6 6 0 0 1 8 0"/><circle cx="18" cy="29" r="1"/>',
        'tv'=>'<rect x="4" y="6" width="28" height="20" rx="1"/><path d="M18 26v5m-6 0h12"/>',
        'pot'=>'<path d="M7 13h22v14a4 4 0 0 1-4 4H11a4 4 0 0 1-4-4V13Zm-3 5h3m22 0h3M5 13h26M10 9h16M15 9V6h6v3"/>',
        'laundry'=>'<rect x="7" y="3" width="22" height="30" rx="2"/><path d="M7 10h22m-17-3h1m4 0h1"/><circle cx="18" cy="21" r="8"/><path d="M11 22c4-5 8 5 14-1"/>',
        'building'=>'<path d="M5 32V9h13v23M18 15h13v17M3 32h30M9 14h4m-4 5h4m-4 5h4m10-4h4m-4 5h4M10 32v-4h3v4"/>',
        'help'=>'<path d="M18 3 30 8v10c0 8-12 15-12 15S6 26 6 18V8L18 3Z"/><path d="M14 14a4 4 0 0 1 8 0c0 3-4 3-4 6m0 4v1"/>',
        'rules'=>'<path d="M9 3h13l6 6v24H9V3Zm13 0v7h6M14 16h9m-9 5h9m-9 5h7"/>',
        'search'=>'<circle cx="15" cy="15" r="10"/><path d="m23 23 9 9"/>',
    ];
    return '<svg viewBox="0 0 36 36" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($paths[$name] ?? $paths['help']).'</svg>';
}

function guideHref(string $href): string {
    if (str_starts_with($href, '#')) return $href;
    if (preg_match('~^(https://|mailto:|tel:)~i', $href)) return $href;
    if (str_starts_with($href, 'assets/')) return url($href);
    return '';
}

function guideImage(array $image): void {
    $size = getimagesize(__DIR__.'/../'.$image['src']);
    $guide = !empty($image['guide']);
    ?><figure class="guide-image <?= $guide ? 'guide-image--manual' : '' ?>">
        <a href="<?= e(url($image['src'])) ?>" target="_blank" rel="noopener" aria-label="<?= e('Open full-resolution image: '.$image['alt']) ?>"><img src="<?= e(url($image['src'])) ?>" width="<?= $size[0] ?>" height="<?= $size[1] ?>" alt="<?= e($image['alt']) ?>" loading="lazy" decoding="async"></a>
        <?php if ($image['caption']): ?><figcaption><?= e($image['caption']) ?><?php if ($guide): ?><span>Tap to open full size ↗</span><?php endif; ?></figcaption><?php endif; ?>
    </figure><?php
}

function guideVideo(?array $video): void {
    if (!$video) return;
    if (($video['type'] ?? '') === 'youtube' && preg_match('/^[a-zA-Z0-9_-]{11}$/', $video['id'] ?? '')) {
        ?><div class="guide-video"><iframe src="https://www.youtube-nocookie.com/embed/<?= e($video['id']) ?>" title="<?= e($video['title'] ?? 'Guest guide video') ?>" loading="lazy" allowfullscreen referrerpolicy="strict-origin-when-cross-origin" allow="fullscreen; picture-in-picture"></iframe></div><?php
    } elseif (($video['type'] ?? '') === 'local' && str_starts_with($video['src'] ?? '', 'assets/')) {
        ?><video class="guide-local-video" controls preload="none" aria-label="<?= e($video['title'] ?? 'Guest guide video') ?>"><source src="<?= e(url($video['src'])) ?>"><?php if (!empty($video['captions'])): ?><track kind="captions" src="<?= e(url($video['captions'])) ?>" srclang="en" label="English" default><?php endif; ?></video><?php
    }
}

function guideFaq(array $entry, array $topics): void {
    $search = [$entry['question'], ...$entry['tags']];
    foreach ($entry['answer'] as $block) $search[] = $block['text'] ?? implode(' ', $block['items'] ?? []);
    foreach ($entry['topic'] as $topic) $search[] = $topics[$topic]['label'];
    $slug = $entry['slug'];
    ?><article class="guide-faq" id="<?= e($slug) ?>" data-topics="<?= e(implode(' ', $entry['topic'])) ?>" data-search="<?= e(implode(' ', $search)) ?>">
        <h3><button type="button" class="guide-question" id="question-<?= e($slug) ?>" aria-expanded="true" aria-controls="answer-<?= e($slug) ?>"><span><?= e($entry['question']) ?></span><span class="guide-chevron" aria-hidden="true"></span></button></h3>
        <div class="guide-answer" id="answer-<?= e($slug) ?>" aria-labelledby="question-<?= e($slug) ?>">
            <div class="guide-prose"><?php foreach ($entry['answer'] as $block): ?>
                <?php if ($block['type'] === 'paragraph'): ?><p><?= e($block['text']) ?></p>
                <?php elseif ($block['type'] === 'contact'): ?><p><?= e($block['text']) ?><br><a class="guide-phone" href="<?= e(guideHref($block['href'])) ?>"><?= e($block['label']) ?></a></p>
                <?php elseif (in_array($block['type'], ['list','steps'], true)): $tag = $block['type'] === 'steps' ? 'ol' : 'ul'; ?><<?= $tag ?>><?php foreach ($block['items'] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></<?= $tag ?>><?php endif; ?>
            <?php endforeach; ?></div>
            <?php if ($entry['images']): ?><div class="guide-answer-images <?= count($entry['images']) > 1 ? 'guide-answer-images--pair' : '' ?>"><?php foreach ($entry['images'] as $image) guideImage($image); ?></div><?php endif; ?>
            <?php guideVideo($entry['video']); ?>
            <?php if ($entry['downloads'] || $entry['links']): ?><div class="guide-answer-links">
                <?php foreach ($entry['downloads'] as $download): ?><a href="<?= e(url($download['src'])) ?>" download>↓ <?= e($download['label']) ?> <span>(<?= e($download['format']) ?>)</span></a><?php endforeach; ?>
                <?php foreach ($entry['links'] as $link): $href = guideHref($link['href']); if (!$href) continue; ?><a href="<?= e($href) ?>"<?= !empty($link['primary']) ? ' class="guide-phone"' : '' ?>><?= e($link['label']) ?> <span aria-hidden="true">↗</span></a><?php endforeach; ?>
            </div><?php endif; ?>
        </div>
    </article><?php
}
