<main class="experience-page journal-story" id="main" tabindex="-1">
    <article class="journal-story-inner" id="top">
        <a class="journal-back" href="<?= e(url('experience#' . $experience['slug'])) ?>">← All postcards</a>
        <header class="journal-story-header"><p class="eyebrow"><?= e($experience['category'] ?? 'Experience') ?></p><h1><?= e($experience['title']) ?></h1><div class="journal-story-meta"><?php if (!empty($experience['location'])): ?><span>⌖ <?= e($experience['location']) ?></span><?php endif; ?><?php if (!empty($experience['visited'])): ?><span>Visited <?= e($experience['visited']) ?></span><?php endif; ?></div><?php experienceTags($experience); ?></header>
        <div class="postcard-photo-paper"><?php experienceMedia($experience, true); ?></div>
        <div class="journal-story-content">
            <div class="journal-prose">
                <?php if (!empty($experience['short_description'])): ?><p class="journal-story-lead"><?= e($experience['short_description']) ?></p><?php endif; ?>
                <?php foreach (preg_split('/\R\s*\R/u', trim($experience['story'] ?? ''), -1, PREG_SPLIT_NO_EMPTY) as $paragraph): ?><p><?= e($paragraph) ?></p><?php endforeach; ?>
                <?php if (!empty($experience['tribeinn_note'])): ?><aside class="journal-note"><span class="eyebrow">A TribeInn note</span><p><?= e($experience['tribeinn_note']) ?></p></aside><?php endif; ?>
            </div>
            <?php $details = array_filter(['Our pick' => $experience['our_pick'] ?? '', 'Best time' => $experience['best_time'] ?? '', 'Combine with' => $experience['combine_with'] ?? '', 'Getting there' => $experience['drive_time'] ?? '']); ?>
            <?php if ($details || !empty($experience['maps_url'])): ?><aside class="journal-field-notes"><h2>Field notes</h2><dl><?php foreach ($details as $label => $value): ?><dt><?= e($label) ?></dt><dd><?= e($value) ?></dd><?php endforeach; ?></dl><?php if (!empty($experience['maps_url']) && filter_var($experience['maps_url'], FILTER_VALIDATE_URL) && parse_url($experience['maps_url'], PHP_URL_SCHEME) === 'https'): ?><a class="journal-map" href="<?= e($experience['maps_url']) ?>" target="_blank" rel="noopener noreferrer">Open map <span aria-hidden="true">↗</span><span class="sr-only"> (new tab)</span></a><?php endif; ?></aside><?php endif; ?>
        </div>
        <footer class="journal-story-end"><p>From <?= e($property['name']) ?>, with curiosity.</p><a href="<?= e(url('experience')) ?>">More from the journal →</a></footer>
    </article>
</main>
