<main id="main" class="container placeholder" tabindex="-1">
    <span id="top"></span>
    <p class="eyebrow"><?= $found ? 'A little more to come' : 'A small detour' ?></p>
    <h1><?= e($pageTitle) ?></h1>
    <?php if (!$found): ?>
        <p>We couldn’t find that page. Let’s take you home.</p>
    <?php elseif ($route === 'check-dates'): ?>
        <p>The booking enquiry page is coming soon. Dates cannot be checked or requested here yet.</p>
        <div id="contact"><p>Contact details will be added when they’re ready.</p></div>
    <?php else: ?>
        <p>This part of TribeInn is still taking shape. For now, get a feel for <?= e($property['name']) ?> on our homepage.</p>
        <?php if ($route === 'stay'): ?><span id="rio-de-goa"></span><span id="why-stay-longer"></span><?php endif; ?>
        <?php if ($route === 'faq-policies'): ?><span id="faq"></span><span id="policies"></span><?php endif; ?>
    <?php endif; ?>
    <a class="button" href="<?= e(url()) ?>">Back to Home <span aria-hidden="true">→</span></a>
</main>
