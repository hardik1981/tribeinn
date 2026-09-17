<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#30452d">
    <title><?= e($route === '' ? 'TribeInn — Stay a while. Live a little. | The Beginning, Goa' : $pageTitle . ' | TribeInn') ?></title>
    <meta name="description" content="<?= e($property['description']) ?>">
    <?php if ($route !== '' && $route !== 'stay'): ?><meta name="robots" content="noindex, follow"><?php endif; ?>
    <link rel="icon" type="image/svg+xml" href="<?= e(url('assets/images/favicon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(url('assets/css/site.css')) ?>">
    <script src="<?= e(url('assets/js/site.js')) ?>" defer></script>
    <?php if ($isInner): ?><link rel="stylesheet" href="<?= e(url('assets/css/inner.css')) ?>"><?php endif; ?>
    <?php if ($route === 'stay'): ?>
    <link rel="stylesheet" href="<?= e(url('assets/css/stay.css')) ?>">
    <script src="<?= e(url('assets/js/lightbox.js')) ?>" defer></script>
    <?php endif; ?>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<?php require __DIR__ . ($isInner ? '/header-inner.php' : '/header-home.php'); ?>
