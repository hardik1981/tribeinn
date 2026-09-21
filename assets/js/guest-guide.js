(() => {
    const page = document.querySelector('.guide-page');
    if (!page) return;
    const cards = [...page.querySelectorAll('.guide-faq')];
    const search = page.querySelector('#guide-search');
    const topics = [...page.querySelectorAll('[data-topic]')];
    const status = page.querySelector('#guide-results');
    const empty = page.querySelector('#guide-empty');
    let topic = '';
    const normalize = value => value.toLowerCase().normalize('NFKD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-z0-9\s]/g,'');
    const searchable = new Map(cards.map(card => [card, normalize(card.dataset.search)]));
    const bySlug = new Map(cards.map(card => [card.id, card]));
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');

    function setOpen(card, open) {
        card.querySelector('.guide-question').setAttribute('aria-expanded', String(open));
        card.querySelector('.guide-answer').hidden = !open;
    }
    function filter() {
        const terms = normalize(search.value).trim().split(/\s+/).filter(Boolean);
        const searching = search.value.trim() !== '';
        let count = 0;
        cards.forEach(card => {
            const match = (searching || !topic || card.dataset.topics.split(' ').includes(topic)) && terms.every(term => searchable.get(card).includes(term));
            card.hidden = !match;
            if (match) count++;
        });
        topics.forEach(button => button.setAttribute('aria-pressed', String(!searching && button.dataset.topic === topic)));
        status.textContent = searching ? `${count} ${count === 1 ? 'answer' : 'answers'} found for “${search.value.trim()}”` : !topic ? `All ${count} questions` : `${count} ${count === 1 ? 'answer' : 'answers'}`;
        empty.hidden = count !== 0;
    }
    function clearFilters() { topic = ''; search.value = ''; filter(); }
    function scrollToCard(card) {
        // Shared chrome is not currently fixed; reserve clearance if it becomes sticky later.
        const header = document.querySelector('.site-header');
        const headerStyle = getComputedStyle(header);
        const offset = ['fixed','sticky'].includes(headerStyle.position) ? header.getBoundingClientRect().height + 20 : 24;
        card.style.scrollMarginTop = `${offset}px`;
        card.scrollIntoView({block:'start', behavior:reduced.matches ? 'instant' : 'smooth'});
    }
    function followHash() {
        let slug;
        try { slug = decodeURIComponent(location.hash.slice(1)); } catch { return; }
        const card = bySlug.get(slug);
        if (!card) {
            if (!slug) { clearFilters(); cards.forEach(card => setOpen(card, false)); }
            return;
        }
        clearFilters();
        cards.forEach(item => setOpen(item, item === card));
        // Focusing the control also gives screen-reader and keyboard visitors context.
        card.querySelector('.guide-question').focus({preventScroll:true});
        requestAnimationFrame(() => scrollToCard(card));
    }
    function navigate(card) {
        const hash = `#${encodeURIComponent(card.id)}`;
        if (location.hash !== hash) history.pushState(null, '', hash);
        followHash();
    }
    cards.forEach(card => {
        setOpen(card, false);
        card.querySelector('.guide-question').addEventListener('click', () => {
            const open = card.querySelector('.guide-question').getAttribute('aria-expanded') === 'true';
            setOpen(card, !open);
            if (!open) {
                if (location.hash !== `#${card.id}`) history.pushState(null, '', `#${card.id}`);
            } else if (location.hash === `#${card.id}`) history.pushState(null, '', location.pathname + location.search);
        });
    });
    page.querySelectorAll('a[href^="#"]').forEach(link => {
        const card = bySlug.get(link.getAttribute('href').slice(1));
        if (!card) return;
        link.addEventListener('click', event => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault(); navigate(card);
        });
    });
    topics.forEach(button => {
        button.disabled = false;
        button.addEventListener('click', () => {
            topic = button.dataset.topic;
            search.value = ''; filter();
            page.querySelector('.guide-faq-section').scrollIntoView({block:'start',behavior:reduced.matches ? 'instant' : 'smooth'});
        });
    });
    search.addEventListener('input', filter);
    page.querySelector('.guide-tools').hidden = false;
    filter();
    if (location.hash) followHash(); else if (cards[0]) setOpen(cards[0], true);
    window.addEventListener('hashchange', followHash);
    window.addEventListener('popstate', followHash);
})();
