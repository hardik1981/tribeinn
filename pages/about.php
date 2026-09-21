<?php
// Supplied photographs; the notebook is an approved conceptual editorial asset.
$aboutImage = static function (string $name, string $alt, int $width, int $height): void { ?>
    <img src="<?= e(url('assets/images/about/' . $name . '-' . $width . '.webp')) ?>"
         srcset="<?= e(url('assets/images/about/' . $name . '-640.webp')) ?> 640w, <?= e(url('assets/images/about/' . $name . '-' . $width . '.webp')) ?> <?= $width ?>w"
         sizes="(max-width: 800px) calc(100vw - 40px), 44vw"
         width="<?= $width ?>" height="<?= $height ?>" loading="lazy" decoding="async" alt="<?= e($alt) ?>">
<?php }; ?>
<main id="main" class="about-page">
    <section class="about-hero" aria-labelledby="about-title">
        <img src="<?= e(url('assets/images/about/goa-beach-1152.webp')) ?>" srcset="<?= e(url('assets/images/about/goa-beach-640.webp')) ?> 640w, <?= e(url('assets/images/about/goa-beach-1152.webp')) ?> 1152w" sizes="100vw" width="1152" height="1536" fetchpriority="high" alt="Open sky, waves and sand on a Goa beach">
        <div class="container about-hero-copy"><h1 id="about-title">About<br>TribeInn</h1></div>
    </section>

    <div class="container about-story">
        <section class="about-chapter" aria-labelledby="about-slow">
            <div class="about-copy">
                <h2 id="about-slow">We have always felt we travel too fast.</h2>
                <p>We reach a place and immediately start thinking about what we need to see.<br>Three days in Goa. Make a list. Beaches. Restaurants. Sunset. Photos. Done.</p>
                <p>But stay somewhere a little longer and something changes.<br>You stop trying to see the place and slowly start living in it.</p>
                <p>You find the grocery shop you like. You know where to get breakfast. You stop needing Google Maps for every little trip. Maybe you spend an entire afternoon at home doing absolutely nothing — and don't feel guilty about wasting a day in Goa.</p>
                <p>That idea stayed with us.</p>
            </div>
            <figure class="about-photo about-landscape"><?php $aboutImage('zuari-landscape', 'Water, green shoreline and a distant bridge in the Zuari-side landscape, Goa', 1280, 960); ?></figure>
        </section>

        <section class="about-chapter about-chapter--reverse" aria-labelledby="about-unfinished">
            <div class="about-copy">
                <h2 id="about-unfinished">What if you came with something unfinished?</h2>
                <p>Maybe you've been trying to write something.<br>Maybe there's a project you've been thinking about, a painting you haven't started, a business idea sitting in your head, some work that needs uninterrupted time.</p>
                <p>Maybe you simply need a different place for a while.<br>Or maybe you bring absolutely nothing.<br>That's fine too.</p>
                <p>We wanted to create a place where you could arrive with a suitcase, unpack it properly and stay.<br>Not just sleep there.</p>
            </div>
            <figure class="about-photo about-notebook"><?php $aboutImage('unfinished-notebook', 'An editorial notebook: A book? A painting? A plan? Some work? Some thoughts? Or nothing at all.', 1280, 853); ?></figure>
        </section>

        <section class="about-chapter" aria-labelledby="about-home">
            <div class="about-copy">
                <h2 id="about-home">That’s why it had to feel like a home.</h2>
                <p>A kitchen you can actually cook in.<br>A washing machine because eventually you run out of clothes.<br>Good internet because life doesn't stop just because you're in Goa.<br>A table that might hold breakfast in the morning and your laptop in the afternoon.<br>A sofa that becomes a bed when you need the extra sleeping space.</p>
                <p>Nothing revolutionary.<br>Just the little things that start mattering when you're somewhere for more than a weekend.</p>
                <p>And that's how TribeInn started.<br><strong>One family. One small home in Goa. One idea we wanted to try.</strong></p>
                <p>We called it The Beginning.<br>Because that's exactly what it is.</p>
            </div>
            <figure class="about-photo"><?php $aboutImage('the-beginning', 'The Beginning’s living room with its patterned table, TV, terracotta wall decorations and adjoining kitchen', 1280, 960); ?></figure>
        </section>

        <section class="about-closing" aria-labelledby="about-closing-title">
            <h2 id="about-closing-title">Stay a while.<br>Live a little.</h2>
            <p>Come for Goa.<br>Come with something unfinished.<br>Or come with absolutely nothing planned.<br>There is no correct way to spend your time here.<br><strong>Just give yourself some of it.</strong></p>
        </section>
    </div>
</main>
