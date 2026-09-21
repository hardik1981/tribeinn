<?php
declare(strict_types=1);

function experienceTags(array $record): void { ?>
    <?php if (!empty($record['tags'])): ?><ul class="journal-tags" aria-label="Tags"><?php foreach ($record['tags'] as $tag): ?><li><?= e($tag) ?></li><?php endforeach; ?></ul><?php endif; ?>
<?php }

function experienceMedia(array $record, bool $priority = false): void {
    if (!$record['media']) return;
    $id = 'media-' . $record['slug'];
    $stamps = ['morjim-beach' => 'beach-days', 'bridge-n-tunnel' => 'hidden-gems', 'filomenas-kitchen' => 'good-food', 'japanese-garden' => 'explore-goa', 'baowich' => 'good-food'];
    $rear = array_slice(array_values(array_filter(array_slice($record['media'], 1), fn($item) => $item['type'] === 'image')), 0, 2); ?>
    <div class="journal-media" data-media-slider role="region" aria-roledescription="carousel" aria-label="<?= e($record['title']) ?> photographs and videos" tabindex="0">
        <div class="journal-rear-stack" aria-hidden="true"><?php foreach ($rear as $image): ?><img src="<?= e(url($image['src'])) ?>" alt="" loading="lazy" decoding="async" draggable="false"><?php endforeach; ?></div>
        <div class="journal-slides" id="<?= e($id) ?>">
        <?php foreach ($record['media'] as $index => $media): ?>
            <figure class="journal-slide" role="group" aria-roledescription="slide" aria-label="<?= $index + 1 ?> of <?= count($record['media']) ?>" <?= $index ? 'hidden' : '' ?>>
            <?php if ($media['type'] === 'image'): ?>
                <img src="<?= e(url($media['src'])) ?>" alt="<?= e($media['alt']) ?>" width="<?= $media['width'] ?>" height="<?= $media['height'] ?>" loading="<?= $priority && !$index ? 'eager' : 'lazy' ?>" <?= $priority && !$index ? 'fetchpriority="high"' : '' ?> decoding="async" draggable="false">
            <?php else: ?>
                <div class="journal-video" data-youtube="<?= e($media['id']) ?>"><button type="button" class="journal-play" aria-label="Load video for <?= e($record['title']) ?>"><span aria-hidden="true">▷</span> Watch the video <small>YouTube · plays here</small></button></div>
            <?php endif; ?>
                <?php if ($media['caption'] !== ''): ?><figcaption><?= e($media['caption']) ?></figcaption><?php endif; ?>
            </figure>
        <?php endforeach; ?>
        </div>
        <?php if (isset($stamps[$record['slug']])): ?><img class="journal-stamp" src="<?= e(url('assets/images/experience/stamps/stamp-' . $stamps[$record['slug']] . '.svg')) ?>" alt="" aria-hidden="true" loading="lazy" draggable="false"><?php endif; ?>
        <?php if (count($record['media']) > 1): ?><div class="journal-media-controls"><button type="button" data-previous aria-label="Previous media for <?= e($record['title']) ?>" aria-controls="<?= e($id) ?>">←</button><span data-media-count aria-live="polite" aria-atomic="true">1 / <?= count($record['media']) ?></span><button type="button" data-next aria-label="Next media for <?= e($record['title']) ?>" aria-controls="<?= e($id) ?>">→</button></div><?php endif; ?>
    </div>
<?php }

function experiencePostcard(array $record, int $index): void { ?>
    <article class="journal-postcard" id="<?= e($record['slug']) ?>" aria-labelledby="title-<?= e($record['slug']) ?>">
        <div class="postcard-topline"><span>From the TribeInn journal</span><span>No. <?= sprintf('%02d', $index + 1) ?></span></div>
        <div class="postcard-photo-paper"><?php experienceMedia($record, $index === 0); ?></div>
        <div class="postcard-body">
            <div class="postcard-heading"><div><?php if (!empty($record['category'])): ?><p class="journal-category"><?= e($record['category']) ?></p><?php endif; ?><h2 id="title-<?= e($record['slug']) ?>"><?= e($record['title']) ?></h2><?php if (!empty($record['location'])): ?><p class="journal-location">⌖ <?= e($record['location']) ?></p><?php endif; ?></div><?php experienceTags($record); ?></div>
            <?php if (!empty($record['short_description'])): ?><p class="postcard-description"><?= e($record['short_description']) ?></p><?php endif; ?>
            <div class="postcard-bottom"><a class="button" href="<?= e(url('experience/' . $record['slug'])) ?>">Read the story <span aria-hidden="true">→</span></a><?php if (!empty($record['tribeinn_note'])): ?><p class="journal-postcard-note"><?= e($record['tribeinn_note']) ?></p><?php endif; ?></div>
        </div>
    </article>
<?php }

