<main id="main" class="stay-page" tabindex="-1">
    <section class="container stay-intro" id="top" aria-labelledby="stay-title">
        <div><p class="eyebrow section-kicker"><?= e($property['name']) ?></p><h1 id="stay-title">Make yourself at home.</h1><p><?= e($stay['intro']) ?><br><?= e($stay['intro_more']) ?></p><p class="stay-location"><span aria-hidden="true">⌖</span> <?= e($stay['location']) ?></p></div>
        <span class="stay-scribble" aria-hidden="true">More than<br>just a stay.<svg viewBox="0 0 120 20" fill="none"><path d="M5 13C30 4 70 2 112 6M26 18C59 11 82 9 103 10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></span>
    </section>

    <section class="container stay-home-section" aria-labelledby="explore-home-title">
        <div class="stay-section-heading"><h2 id="explore-home-title">Explore the home.</h2><p>Thoughtfully furnished for a comfortable, independent stay.<br><span class="gallery-hint">Select any photograph for a closer look.</span></p></div>
        <?php stayGallery($stay['apartment'], 'apartment', $property['name']); ?>
    </section>

    <section class="container stay-included" aria-labelledby="included-title">
        <div class="stay-section-heading"><div><p class="eyebrow section-kicker">What’s included</p><h2 id="included-title">Everything you need to settle in.</h2></div><p>From cooking and working to relaxing and everyday living —<br>here’s what you’ll find at <?= e($property['name']) ?>.</p></div>
        <div class="utility-grid">
            <?php foreach ($stay['utilities'] as $utility): ?>
            <article class="utility-item"><div class="utility-art"><?php stayImage('illustrations/utilities/' . $utility['file'], ''); ?></div><h3><?= e($utility['title']) ?></h3><p><?= e($utility['description']) ?></p></article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="stay-community" id="rio-de-goa" aria-labelledby="community-title">
        <div class="container">
            <div class="community-intro"><div><p class="eyebrow section-kicker">Life at Rio De Goa</p><h2 id="community-title">Step outside. There’s more.</h2><p><?= e($property['name']) ?> is part of Tata Housing’s Rio De Goa community.<br>From a morning workout to a wander through the gardens, there’s room for a different rhythm.</p></div><span class="stay-scribble" aria-hidden="true">Same home.<br>A bigger story<br>outside.</span></div>
            <?php stayGallery($stay['community'], 'community', 'Rio De Goa'); ?>
            <div class="community-pool-note"><?= communityIcon('pool') ?><p><strong>A swim belongs in the day, too.</strong> Guests can use the main, bigger pool in front of the gym.</p></div>
            <section class="community-summary" aria-labelledby="community-summary-title">
                <div class="stay-section-heading"><div><p class="eyebrow section-kicker">Around the community</p><h2 id="community-summary-title">Everything within reach.</h2></div><p>From morning workouts to evening walks,<br>find your own everyday rhythm.</p></div>
                <div class="community-benefits">
                    <?php foreach ([['pool','Pool','Take a dip in the main pool. Unwind. Repeat.'],['gym','Gym','A little movement, at your pace.'],['gardens','Gardens','Green paths to wander and pause.'],['terrace','Terrace','Open skies and a moment of perspective.'],['community','A Peaceful Community','A slower setting for your everyday life.']] as [$symbol,$title,$copy]): ?>
                    <div><?= communityIcon($symbol) ?><h3><?= e($title) ?></h3><p><?= e($copy) ?></p></div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </section>

    <section class="stay-longer" id="why-stay-longer" aria-labelledby="longer-title">
        <div class="container">
            <div class="longer-intro"><p class="eyebrow section-kicker">Why stay longer</p><h2 id="longer-title">What could you do with<br><em>a little more time?</em></h2><p><?= e($property['name']) ?> isn’t just a place to stay — it’s a space to slow down, explore new ideas and make time for what really matters. Whether you come with a plan, a project or no plan at all, Goa gives you the room to breathe.</p></div>
            <div class="longer-grid">
                <?php foreach (array_slice($stay['long_stay'], 0, 6) as $index => $idea): ?>
                <article class="longer-idea"><?php stayImage('illustrations/why-stay-longer/' . $idea['file'], $idea['alt']); ?><div class="longer-caption"><span class="idea-number" aria-hidden="true"><?= sprintf('%02d', $index + 1) ?></span><div><h3><?= e($idea['title']) ?></h3><p><?= e($idea['description']) ?></p></div></div></article>
                <?php endforeach; ?>
            </div>
            <?php $nothing = $stay['long_stay'][6]; ?>
            <article class="nothing-required"><div class="nothing-copy"><h3><?= e($nothing['title']) ?></h3><div><p><?= e($nothing['description']) ?></p><p><?= e($nothing['more']) ?></p></div></div><?php stayImage('illustrations/why-stay-longer/' . $nothing['file'], $nothing['alt']); ?><p class="nothing-note">Your time. Your kind of Goa.</p></article>
        </div>
    </section>

    <section class="booking-strip stay-booking" aria-labelledby="stay-booking-title"><div class="container booking-inner"><div><h2 id="stay-booking-title">Ready to make it yours?</h2><p>Check availability and start planning your stay at <?= e($property['name']) ?>.</p><p class="booking-enquiry-note">An enquiry is the first step. Dates are confirmed personally.</p></div><a class="button button-light" href="<?= e(url('check-dates')) ?>">Check Dates <span aria-hidden="true">→</span></a></div></section>
</main>

<dialog class="photo-lightbox" id="photo-lightbox" aria-labelledby="lightbox-title" aria-describedby="lightbox-description">
    <div class="lightbox-shell">
        <div class="lightbox-top"><span id="lightbox-gallery"></span><button type="button" class="lightbox-close" aria-label="Close photo viewer" autofocus>Close <span aria-hidden="true">×</span></button></div>
        <div class="lightbox-stage"><button class="lightbox-prev" type="button" aria-label="Previous photograph">←</button><div class="lightbox-image-wrap"><img id="lightbox-image" alt=""><p class="lightbox-error" hidden>The photograph couldn’t load. Please try again.</p></div><button class="lightbox-next" type="button" aria-label="Next photograph">→</button></div>
        <div class="lightbox-bottom"><div class="lightbox-caption" aria-live="polite" aria-atomic="true"><h2 id="lightbox-title"></h2><p id="lightbox-description"></p></div><span id="lightbox-count"></span></div>
        <p class="lightbox-help">Use ← → to browse · Esc to close <span>· Swipe to browse on touch screens</span></p>
    </div>
</dialog>
<script src="<?= e(url('assets/js/stay-disclosure.js')) ?>"></script>
