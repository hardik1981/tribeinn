<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <a class="brand" href="<?= e(url()) ?>">Tribe<span>Inn</span><i aria-hidden="true">✳</i></a>
            <p class="footer-property"><?= e($property['name']) ?></p>
            <p class="muted"><?= e($property['location']) ?><br>A little more room to be yourself.</p>
        </div>
        <nav aria-label="Explore"><p class="eyebrow">Make yourself at home</p><a href="<?= e(url('stay')) ?>">Stay</a><a href="<?= e(url('experience')) ?>">Experience</a><a href="<?= e(url('about')) ?>">About</a></nav>
        <nav aria-label="Useful links"><p class="eyebrow">The useful things</p><a href="<?= e(url('guest-guide')) ?>">Guest Guide</a><a href="<?= e(url('faq-policies#faq')) ?>">FAQ</a><a href="<?= e(url('faq-policies#policies')) ?>">Policies</a><a href="<?= e(url('check-dates#contact')) ?>">Contact</a></nav>
        <div class="footer-note">A change of scene.<br><em>A little possibility.</em><?= icon('sun') ?></div>
    </div>
    <div class="container footer-bottom"><span>© <?= date('Y') ?> TribeInn</span><span>Stay a while. Live a little.</span><a href="#top" class="back-top" aria-label="Back to top">Back to top ↑</a></div>
</footer>
</body>
</html>
