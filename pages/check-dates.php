<main id="main" class="dates-page" tabindex="-1">
    <div class="container dates-layout" id="top">
        <section class="dates-editorial" aria-label="Stay at The Beginning">
            <div class="dates-intro"><p class="dates-handwritten">Stay a few days.<br>Or make Goa home<br> for a while.</p><p class="dates-intro-copy"><?= e($property['name']) ?> is designed for both short escapes and slower stays — with a kitchen, workspace, Wi-Fi, laundry and the everyday things that make longer stays easy.</p><div class="dates-small-note" aria-hidden="true"><?= icon('sun') ?><p>Your own rhythm.<br>A little more time.</p></div></div>
            <figure class="dates-photo"><img src="<?= e(url('assets/images/hero/home-hero-mobile.webp')) ?>" alt="The Beginning living room with a sofa, patterned coffee table and personal wall decorations" width="1200" height="1500" fetchpriority="high"><figcaption><?= e($property['name']) ?><span>A home in Goa. A pace of your own.</span></figcaption></figure>
        </section>

        <section class="dates-form-panel" aria-labelledby="dates-title">
            <div id="dates-form-content"><p class="eyebrow">Check Dates</p><h1 id="dates-title">Thinking of staying a while?</h1><p class="dates-support">Tell us when you’re coming. We’ll check the dates and get back to you personally.</p>
            <form id="date-request" action="<?= e(url('check-dates/request')) ?>" method="post" novalidate data-property="<?= e($property['name']) ?>" data-whatsapp="<?= e($whatsappNumber) ?>" data-today="<?= e($enquiryToday) ?>">
                <input type="hidden" name="csrf" value="<?= e($enquiryTokens['csrf']) ?>"><input type="hidden" name="request_id" value="<?= e($enquiryTokens['request_id']) ?>"><input type="hidden" name="channel" value="email">
                <div class="dates-trap" aria-hidden="true"><label>Leave this field empty<input name="website" tabindex="-1" autocomplete="off"></label></div>
                <div id="dates-errors" class="dates-error-summary" role="alert" tabindex="-1" hidden></div>
                <div data-calendar data-api="<?= e(url('api/availability')) ?>" data-unit="<?= e((require __DIR__ . '/../config/availability.php')['unit']) ?>" data-today="<?= e($enquiryToday) ?>" data-start="#checkin" data-end="#checkout"></div>
                <div class="dates-fields">
                    <div class="dates-field"><label for="checkin">Check-in</label><input type="date" id="checkin" name="checkin" min="<?= e($enquiryToday) ?>" required aria-describedby="error-checkin"><span class="dates-field-error" id="error-checkin"></span></div>
                    <div class="dates-field"><label for="checkout">Check-out</label><input type="date" id="checkout" name="checkout" min="<?= e($enquiryToday) ?>" required aria-describedby="error-checkout"><span class="dates-field-error" id="error-checkout"></span></div>
                    <div class="dates-field"><label for="adults">Adults</label><input type="number" id="adults" name="adults" value="1" min="1" max="99" step="1" inputmode="numeric" required aria-describedby="error-adults"><span class="dates-field-error" id="error-adults"></span></div>
                    <div class="dates-field"><label for="children">Children</label><input type="number" id="children" name="children" value="0" min="0" max="99" step="1" inputmode="numeric" required aria-describedby="error-children"><span class="dates-field-error" id="error-children"></span></div>
                    <div class="dates-field"><label for="guest-name">Your name</label><input id="guest-name" name="name" autocomplete="name" placeholder="Full name" maxlength="120" required aria-describedby="error-name"><span class="dates-field-error" id="error-name"></span></div>
                    <div class="dates-field"><label for="phone">Phone <span>(optional)</span></label><input type="tel" id="phone" name="phone" autocomplete="tel" placeholder="Include country code" maxlength="40" aria-describedby="error-phone"><span class="dates-field-error" id="error-phone"></span></div>
                    <div class="dates-field dates-wide"><label for="email">Email <span>(optional for now)</span></label><input type="email" id="email" name="email" autocomplete="email" placeholder="you@example.com" maxlength="254" aria-describedby="error-email"><span class="dates-field-error" id="error-email"></span></div>
                    <div class="dates-field dates-wide"><label for="purpose">What brings you to Goa?</label><select id="purpose" name="purpose" required aria-describedby="error-purpose"><option value="">Select an option</option><?php foreach ($enquiryConfig['purposes'] as $purpose): ?><option><?= e($purpose) ?></option><?php endforeach; ?></select><span class="dates-field-error" id="error-purpose"></span></div>
                    <div class="dates-field dates-wide"><label for="message">Anything you’d like us to know? <span>(optional)</span></label><textarea id="message" name="message" rows="3" maxlength="2000" placeholder="A workation, a longer stay, or something you’re planning…" aria-describedby="error-message"></textarea><span class="dates-field-error" id="error-message"></span></div>
                </div>
                <button type="submit" class="button dates-continue" disabled>Continue <span aria-hidden="true">→</span></button><p class="dates-next-note">Next: choose email or WhatsApp. Nothing is sent yet.</p>
                <noscript><p>Please enable JavaScript to choose how to send your request, or email <a href="mailto:contact@tribeinn.com">contact@tribeinn.com</a>.</p></noscript>
            </form></div>
            <div id="dates-success" class="dates-success" role="status" tabindex="-1" hidden><span class="dates-success-mark" aria-hidden="true">✓</span><p class="eyebrow">A little closer to Goa</p><h2>Request received.</h2><p>We’ll check your dates and get back to you shortly.</p><p class="dates-success-note">Your dates aren’t confirmed yet. We’ll be in touch personally.</p><p id="dates-preview-notice" hidden></p></div>
            <p class="dates-disclaimer"><span aria-hidden="true">ⓘ</span><span>This is a date request, not an instant booking.<br><span>We’ll confirm availability with you directly.</span></span></p>
        </section>
    </div>

    <section class="dates-benefits" aria-label="Settle in for a little longer"><div class="container">
        <div><?= icon('stay') ?><p>A kitchen<br>for everyday meals</p></div><div><?= icon('work') ?><p>Workspace<br>&amp; Wi-Fi</p></div><div class="dates-laundry-icon"><img src="<?= e(url('assets/images/illustrations/utilities/04-practical-living.webp')) ?>" alt="" width="64" height="128" loading="lazy"><p>Laundry<br>for longer stays</p></div><div><?= icon('sun') ?><p>Short escapes.<br>Slower days.</p></div><div><?= icon('nature') ?><p>A calm place<br>to create, think or just be</p></div>
    </div></section>
    <section class="container dates-contact" id="contact" aria-label="Contact TribeInn"><div><span class="dates-contact-symbol" aria-hidden="true">✉</span><div><p>Prefer to email us directly?</p><a href="mailto:contact@tribeinn.com">contact@tribeinn.com <span aria-hidden="true">→</span></a></div></div><?php if ($whatsappNumber): ?><div><div><p>Prefer to talk?</p><a href="https://wa.me/<?= e($whatsappNumber) ?>" target="_blank" rel="noopener noreferrer">WhatsApp us <span aria-hidden="true">↗</span></a></div></div><?php endif; ?><p class="dates-contact-note">Questions, special requests<br>or planning a longer stay?<br>We’re happy to chat.</p></section>
</main>

<dialog id="dates-dialog" class="dates-dialog" aria-labelledby="contact-choice-title" aria-describedby="contact-choice-help">
    <div class="dates-dialog-top"><p class="eyebrow">Your date request</p><button type="button" class="dates-dialog-close" aria-label="Close contact choices">×</button></div>
    <h2 id="contact-choice-title">How would you like to send your request?</h2><p id="contact-choice-help">Choose what works for you. Nothing has been sent.</p><p id="dates-request-summary" class="dates-request-summary"></p>
    <div class="dates-methods"><button type="button" class="button" id="choose-email">Email TribeInn <span aria-hidden="true">→</span></button><button type="button" class="button dates-whatsapp" id="choose-whatsapp">WhatsApp TribeInn <span aria-hidden="true">→</span></button></div>
    <div id="dates-whatsapp-status" class="dates-method-status" role="status" hidden></div>
    <form id="email-request" novalidate hidden><div class="dates-field"><label for="reply-email">Your email, so we can reply</label><input id="reply-email" type="email" autocomplete="email" maxlength="254" required placeholder="you@example.com" aria-describedby="reply-email-error"><span class="dates-field-error" id="reply-email-error"></span></div><p id="email-status" class="dates-method-status" role="alert" hidden></p><button type="submit" class="button dates-send">Send email request <span aria-hidden="true">→</span></button></form>
    <button type="button" class="dates-edit">← Edit my details</button>
</dialog>
