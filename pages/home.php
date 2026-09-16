<main id="main" tabindex="-1">
    <section class="hero container" id="top" aria-labelledby="hero-title">
        <div class="hero-copy">
            <p class="eyebrow"><span class="little-line"></span> A home in Goa. A pace of your own.</p>
            <h1 id="hero-title">Stay a while.<br><em>Live a little.</em></h1>
            <p class="hero-description"><?= e($property['description']) ?></p>
            <div class="hero-actions"><a class="button" href="<?= e(url('stay')) ?>">Explore <?= e($property['name']) ?> <span aria-hidden="true">→</span></a><a class="text-link" href="<?= e(url('check-dates')) ?>">Check Dates <span aria-hidden="true">↗</span></a></div>
            <p class="location"><span aria-hidden="true">⌖</span> <?= e($property['location']) ?></p>
        </div>
        <div class="hero-photo">
            <picture>
                <source media="(max-width: 600px)" srcset="<?= e(url('assets/images/' . $property['images']['hero_mobile'][0])) ?>" width="1200" height="1500">
                <img src="<?= e(url('assets/images/' . $property['images']['hero'][0])) ?>" alt="<?= e($property['images']['hero'][1]) ?>" width="1920" height="1080" fetchpriority="high">
            </picture>
            <div class="photo-label"><span class="label-dot"></span> <?= e($property['name']) ?><span>Our first little corner of Goa</span></div>
            <div class="sun-note" aria-hidden="true"><?= icon('sun') ?><span>hello,<br>slower days.</span></div>
        </div>
    </section>

    <section class="container reasons" aria-label="A stay for your kind of day">
        <?php foreach ([['work', 'Workation Ready', 'Space to find your focus'], ['stay', 'Long Stays', 'Unpack. Settle in. Repeat.'], ['nature', 'Close to Nature', 'A little greener, a little slower'], ['everyone', 'For Everyone', 'Come as you are']] as [$symbol, $title, $description]): ?>
        <div class="reason"><?= icon($symbol) ?><div><h2><?= e($title) ?></h2><p><?= e($description) ?></p></div></div>
        <?php endforeach; ?>
    </section>

    <section class="section container story" aria-labelledby="story-title">
        <div class="section-heading"><div><p class="eyebrow">A little home. A bigger world.</p><h2 id="story-title">Find your kind of Goa.</h2></div><p>Start with a place that feels like you.<br>Let the rest of Goa unfold.</p></div>
        <div class="story-grid">
            <?php foreach ([['living','01','The Home','Your own cosy corner.','A personal space for everyday rituals and entirely unhurried days.','stay'], ['community','02','The Community','Room to step outside.','Colourful courtyards, tropical greens and life at Rio De Goa.','stay#rio-de-goa'], ['goa','03','The Goa Around Us','Go a little further.','Sandy feet, café stops and days with no particular plan.','experience']] as [$photoKey,$number,$title,$subtitle,$copy,$link]): ?>
            <a class="story-card story-<?= e($photoKey) ?>" href="<?= e(url($link)) ?>">
                <div class="story-image"><?php photo($photoKey); ?><span class="image-index"><?= e($number) ?></span></div>
                <div class="story-body"><p class="eyebrow"><?= e($title) ?></p><h3><?= e($subtitle) ?></h3><p><?= e($copy) ?></p><span class="round-arrow" aria-hidden="true">↗</span></div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="unfinished" aria-labelledby="unfinished-title">
        <div class="container unfinished-inner">
            <div class="unfinished-title"><p class="eyebrow">Bring yourself. And maybe an idea.</p><h2 id="unfinished-title">Come with<br>something<br><em>unfinished.</em></h2><svg class="pencil-line" viewBox="0 0 330 20" fill="none" aria-hidden="true"><path d="M3 13C81 4 217 1 324 10M46 18C145 10 233 8 294 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></div>
            <div class="unfinished-copy"><p class="poem">A book. A painting. A business plan.<br>Some work. Some thoughts.<br><em>Or absolutely nothing.</em></p><p>You don’t need a packed itinerary to belong here. Stay long enough to find a rhythm: make breakfast, make a little progress, or make space for a day off.</p><a class="text-link" href="<?= e(url('stay#why-stay-longer')) ?>">Why Stay Longer <span aria-hidden="true">→</span></a></div>
            <span class="margin-note" aria-hidden="true">there’s no hurry here.</span>
        </div>
    </section>

    <section class="section container apartment" aria-labelledby="apartment-title">
        <div class="section-heading"><div><p class="eyebrow">Your home, for a little while</p><h2 id="apartment-title">A closer look at<br><?= e($property['name']) ?>.</h2></div><div><p>Little details. Everyday comforts.<br>Space to live, not just leave your bags.</p><a class="text-link" href="<?= e(url('stay')) ?>">Explore <?= e($property['name']) ?> <span aria-hidden="true">→</span></a></div></div>
        <div class="room-grid">
            <figure class="room room-living"><?php photo('living'); ?><figcaption><span>01 / Living Room</span><strong>Put your feet up.</strong></figcaption></figure>
            <figure class="room room-bedroom"><?php photo('bedroom'); ?><figcaption><span>02 / Bedroom</span><strong>Leave the alarm off.</strong></figcaption></figure>
            <figure class="room room-kitchen"><?php photo('kitchen'); ?><figcaption><span>03 / Kitchen</span><strong>Your morning, your way.</strong></figcaption></figure>
            <figure class="room room-workspace"><?php photo('workspace'); ?><figcaption><span>04 / Work Space</span><strong>A corner for your next idea.</strong></figcaption></figure>
        </div>
        <div class="practical-note"><?php photo('laundry'); ?><div><p class="eyebrow">The everyday is taken care of</p><p>A kitchen to cook in. A utility balcony for laundry.<br>The useful little things that make a longer stay feel like living.</p></div><span class="hand-note" aria-hidden="true">a little less<br>living out of a suitcase.</span></div>
    </section>

    <section class="rio section" aria-labelledby="rio-title">
        <div class="container">
            <div class="section-heading"><div><p class="eyebrow">Beyond your front door · Part of your stay</p><h2 id="rio-title">Life at Rio De Goa.</h2></div><a class="text-link" href="<?= e(url('stay#rio-de-goa')) ?>">Explore Rio De Goa <span aria-hidden="true">→</span></a></div>
            <div class="rio-grid">
                <figure class="rio-garden"><?php photo('gardens'); ?><figcaption>Take the scenic way home.<span>The gardens at Rio De Goa</span></figcaption></figure>
                <div class="rio-copy"><p class="eyebrow">A vibrant community</p><h3>A little movement.<br>A little stillness.</h3><p>Take a dip in the main pool in front of the gym, stretch out your day with a workout, or wander through the gardens.</p><p>For a slower moment, head to the shared terrace and viewing area for the Zuari-side outlook.</p><ul class="amenities" aria-label="Guest-accessible community amenities"><li>Pool</li><li>Gym</li><li>Gardens</li><li>Terrace</li></ul></div>
                <figure class="rio-small"><?php photo('gym'); ?><figcaption>A little energy <span>Gym</span></figcaption></figure>
                <figure class="rio-small"><?php photo('terrace'); ?><figcaption>A little perspective <span>Shared terrace & viewing area</span></figcaption></figure>
            </div>
        </div>
    </section>

    <section class="section container goa" aria-labelledby="goa-title">
        <div class="goa-photo"><?php photo('goa'); ?><span class="postcard-note" aria-hidden="true">a postcard from Goa</span><p class="destination-caption">Out exploring Goa · Destination photograph</p></div>
        <div class="goa-copy"><p class="eyebrow">Outside the property. Into Goa.</p><h2 id="goa-title">Goa around us.<br><em>At your own pace.</em></h2><p>Some days call for the beach. Some for a café table, a bit of culture, or a turn down a quieter road.</p><p>Leave room for the kind of day you didn’t plan.</p><ul class="goa-themes" aria-label="Explore Goa"><li>Beaches</li><li>Cafés</li><li>Culture</li><li>Nature</li><li>Slow Days</li></ul><a class="text-link" href="<?= e(url('experience')) ?>">Explore Around Us <span aria-hidden="true">→</span></a></div>
    </section>

    <section class="booking-strip" aria-labelledby="booking-title"><div class="container booking-inner"><div><p class="eyebrow">A change of pace starts here</p><h2 id="booking-title">Stay for a few days.<br>Or make yourself at home <em>for a while.</em></h2></div><a class="button button-light" href="<?= e(url('check-dates')) ?>">Check Dates <span aria-hidden="true">↗</span></a></div></section>
</main>
