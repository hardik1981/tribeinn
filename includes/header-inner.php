<header class="site-header site-header--slim">
    <div class="container header-inner">
        <div class="inner-brand"><a class="brand" href="<?= e(url()) ?>" aria-label="TribeInn home">Tribe<span>Inn</span><i aria-hidden="true">✳</i></a><span class="inner-property"><?= e($property['name']) ?></span></div>
        <a class="button inner-mobile-cta" href="<?= e(url('check-dates')) ?>">Check Dates <span aria-hidden="true">→</span></a>
        <?php require __DIR__ . '/navigation.php'; ?>
    </div>
</header>
