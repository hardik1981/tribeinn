<main class="experience-page" id="main" tabindex="-1">
    <header class="journal-intro container" id="top"><div><p class="eyebrow">Experience</p><h1>Goa isn’t a checklist.</h1><p>Some places you visit.<br>Some places you slowly get to know.</p></div><div class="journal-postmark" aria-hidden="true"><span>TRIBEINN</span><i>Places<br>People<br>Stories</i><span>GOA · INDIA</span></div></header>
    <div class="journal-feed">
        <div class="journal-feed-label"><span>Discoveries from <?= e($property['name']) ?></span><span><?= count($experiences) ?> postcards</span></div>
        <?php foreach ($experiences as $index => $record) experiencePostcard($record, $index); ?>
        <p class="journal-signoff">Experiences from<br><?= e($property['name']) ?></p>
    </div>
</main>
