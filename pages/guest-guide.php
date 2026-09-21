<main id="main" class="guide-page" tabindex="-1">
    <section class="guide-hero container" id="top" aria-labelledby="guide-title">
        <div class="guide-hero-copy"><p class="eyebrow">Guest Guide</p><h1 id="guide-title">Everything you need for a comfortable stay at The Beginning.</h1><p>Practical information, simple guides and answers to common questions — so you can settle in quickly and make the most of your time in Goa.</p></div>
        <img src="<?= e(url('assets/images/guest-guide/sofa-bed-sofa-mode.jpg')) ?>" width="1536" height="1152" alt="The Beginning living room with its sofa, patterned coffee table and colourful wall clock" fetchpriority="high">
    </section>
    <div class="container guide-layout">
        <div class="guide-main">
            <section class="guide-browse" aria-labelledby="guide-browse-title">
                <h2 id="guide-browse-title">Browse by topic</h2><p>Find what you need, or explore the full guide below.</p>
                <div class="guide-topics" aria-label="Guide topics"><button type="button" class="guide-topic guide-topic--all" data-topic="" aria-pressed="true" aria-controls="guide-faqs" disabled><span>All Questions</span><small>25 answers</small></button><?php foreach ($guideTopics as $key => $topic): ?><button type="button" class="guide-topic" data-topic="<?= e($key) ?>" aria-pressed="false" aria-controls="guide-faqs" disabled><?= guideIcon($topic['icon']) ?><span><?= e($topic['label']) ?></span></button><?php endforeach; ?></div>
            </section>
            <div class="guide-tools" hidden><label for="guide-search" class="guide-search-label">Search the guide</label><div class="guide-search-wrap"><?= guideIcon('search') ?><input id="guide-search" type="search" placeholder="Search questions (e.g. Wi-Fi, hob, TV, parking…)" autocomplete="off" aria-controls="guide-faqs"></div><div class="guide-filter-status"><p id="guide-results" role="status" aria-live="polite" aria-atomic="true">All 25 questions</p></div></div>
            <section class="guide-faq-section" aria-labelledby="guide-faq-title"><h2 id="guide-faq-title">Questions &amp; Guides</h2><p class="guide-faq-intro">Open a question for practical information, instructions and downloadable guides.</p><noscript><p>All answers are shown below. Use your browser’s Find feature to search the guide.</p></noscript><div id="guide-faqs"><?php foreach ($guideEntries as $entry) guideFaq($entry, $guideTopics); ?></div><div id="guide-empty" class="guide-empty" hidden><h3>No matching questions yet.</h3><p>Try a different word, choose another topic or show all questions.</p><a href="#guide-help">Still need a hand? Get in touch →</a></div></section>
        </div>
        <aside class="guide-sidebar" aria-label="Useful guest links">
            <nav class="guide-quick" aria-labelledby="guide-quick-title"><h2 id="guide-quick-title">Quick links</h2><?php foreach ([['wifi','Wi-Fi details','wifi'],['tv-soundbar','TV & soundbar guide','tv'],['induction-hob','Induction hob guide','pot'],['facility-timings','Pool & gym timings','building'],['security','Security contact','help'],['checkout','Before you leave','key']] as [$slug,$label,$iconName]): ?><a href="#<?= e($slug) ?>"><?= guideIcon($iconName) ?><span><?= e($label) ?></span><span aria-hidden="true">↗</span></a><?php endforeach; ?></nav>
            <section class="guide-help" id="guide-help" aria-labelledby="guide-help-title"><p class="eyebrow">A little help</p><h2 id="guide-help-title">Still have a question?<br>We’re here to help.</h2><p>Get in touch with TribeInn.</p><a class="button" href="mailto:<?= e($guideContact['recipient']) ?>">Email us <span aria-hidden="true">→</span></a><a class="guide-email" href="mailto:<?= e($guideContact['recipient']) ?>"><?= e($guideContact['recipient']) ?></a><?php if ($guideWhatsapp): ?><a class="button guide-whatsapp" href="https://wa.me/<?= e($guideWhatsapp) ?>" target="_blank" rel="noopener noreferrer">WhatsApp us <span aria-hidden="true">↗</span></a><?php endif; ?></section>
            <p class="guide-sidebar-note">Small details.<br>Smoother stays.</p>
        </aside>
    </div>
</main>
