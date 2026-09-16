<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#30452d">
    <title><?= e($route === '' ? 'TribeInn — Stay a while. Live a little. | The Beginning, Goa' : $pageTitle . ' | TribeInn') ?></title>
    <meta name="description" content="<?= e($property['description']) ?>">
    <?php if ($route !== ''): ?><meta name="robots" content="noindex, follow"><?php endif; ?>
    <link rel="icon" type="image/svg+xml" href="<?= e(url('assets/images/favicon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(url('assets/css/site.css')) ?>">
    <script src="<?= e(url('assets/js/site.js')) ?>" defer></script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= e(url()) ?>" aria-label="TribeInn home">Tribe<span>Inn</span><i aria-hidden="true">✳</i></a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav" hidden><span>Menu</span><span class="menu-lines" aria-hidden="true"></span></button>
        <nav class="main-nav" id="main-nav" aria-label="Main navigation">
            <?php foreach (array_slice($routes, 0, 5, true) as $slug => $label): ?>
                <a href="<?= e(url($slug)) ?>" <?= $route === $slug ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
            <?php endforeach; ?>
            <a class="button nav-cta" href="<?= e(url('check-dates')) ?>" <?= $route === 'check-dates' ? 'aria-current="page"' : '' ?>>Check Dates <?= arrow() ?></a>
        </nav>
    </div>
</header>
