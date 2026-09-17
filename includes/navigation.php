<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav" hidden><span>Menu</span><span class="menu-lines" aria-hidden="true"></span></button>
<nav class="main-nav" id="main-nav" aria-label="Main navigation">
    <?php foreach (array_slice($routes, 0, 5, true) as $slug => $label): ?>
        <a href="<?= e(url($slug)) ?>" <?= $route === $slug ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
    <a class="button nav-cta" href="<?= e(url('check-dates')) ?>" <?= $route === 'check-dates' ? 'aria-current="page"' : '' ?>>Check Dates <?= arrow() ?></a>
</nav>
