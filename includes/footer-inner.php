<footer class="site-footer--slim">
    <div class="container inner-footer-content">
        <div class="inner-brand"><a class="brand" href="<?= e(url()) ?>" aria-label="TribeInn home">Tribe<span>Inn</span><i aria-hidden="true">✳</i></a><span class="inner-property"><?= e($property['name']) ?></span></div>
        <nav class="inner-footer-nav" aria-label="Footer navigation">
            <?php foreach (array_slice($routes, 0, 5, true) as $slug => $label): ?><a href="<?= e(url($slug)) ?>" <?= $route === $slug ? 'aria-current="page"' : '' ?>><?= e($label) ?></a><?php endforeach; ?>
            <a href="<?= e(url('check-dates')) ?>">Check Dates</a>
        </nav>
        <p class="inner-footer-note">More than just a stay.<br><em>A place to belong.</em></p>
    </div>
    <div class="container inner-footer-meta"><span>© <?= date('Y') ?> TribeInn</span><nav aria-label="Support"><a href="<?= e(url('faq-policies#faq')) ?>">FAQ</a><a href="<?= e(url('faq-policies#policies')) ?>">Policies</a><a href="<?= e(url('check-dates#contact')) ?>">Contact</a></nav><a href="#top">Back to top ↑</a></div>
</footer>
