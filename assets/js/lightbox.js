(() => {
    const dialog = document.querySelector('#photo-lightbox');
    if (!dialog || typeof dialog.showModal !== 'function') return;
    const image = dialog.querySelector('#lightbox-image');
    const heading = dialog.querySelector('#lightbox-title');
    const description = dialog.querySelector('#lightbox-description');
    const count = dialog.querySelector('#lightbox-count');
    const galleryName = dialog.querySelector('#lightbox-gallery');
    const error = dialog.querySelector('.lightbox-error');
    const close = dialog.querySelector('.lightbox-close');
    const previous = dialog.querySelector('.lightbox-prev');
    const next = dialog.querySelector('.lightbox-next');
    let photos = [];
    let current = 0;
    let opener = null;
    let scrollPosition = 0;
    let savedBodyStyle = null;
    let pointerStart = null;

    function show(index) {
        current = (index + photos.length) % photos.length;
        const link = photos[current];
        const figure = link.closest('figure');
        error.hidden = true;
        image.alt = link.querySelector('img').alt;
        image.src = link.href;
        heading.textContent = figure.querySelector('h3').textContent;
        description.textContent = figure.querySelector('figcaption p').textContent;
        count.textContent = `${current + 1} / ${photos.length}`;
    }

    document.querySelectorAll('[data-gallery]').forEach(gallery => {
        const links = Array.from(gallery.querySelectorAll('.stay-photo-link'));
        links.forEach((link, index) => {
            link.setAttribute('aria-haspopup', 'dialog');
            link.addEventListener('click', event => {
                // Keep standard new-tab/download actions and a native image fallback without JS.
                if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
                event.preventDefault();
                photos = links;
                opener = link;
                galleryName.textContent = gallery.dataset.galleryLabel;
                show(index);
                scrollPosition = window.scrollY;
                savedBodyStyle = document.body.getAttribute('style');
                const body = document.body.style;
                body.position = 'fixed';
                body.top = `-${scrollPosition}px`;
                body.width = '100%';
                dialog.showModal();
                close.focus({ preventScroll: true });
            });
        });
    });

    previous.addEventListener('click', () => show(current - 1));
    next.addEventListener('click', () => show(current + 1));
    close.addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', event => { if (event.target === dialog) dialog.close(); });
    dialog.addEventListener('keydown', event => {
        if (event.key === 'ArrowLeft') { event.preventDefault(); show(current - 1); }
        if (event.key === 'ArrowRight') { event.preventDefault(); show(current + 1); }
        if (event.key === 'Home') { event.preventDefault(); show(0); }
        if (event.key === 'End') { event.preventDefault(); show(photos.length - 1); }
        // Native dialog supplies modality; explicitly cycle the three controls for predictable focus.
        if (event.key === 'Tab') {
            if (event.shiftKey && document.activeElement === close) { event.preventDefault(); next.focus(); }
            else if (!event.shiftKey && document.activeElement === next) { event.preventDefault(); close.focus(); }
        }
    });
    dialog.addEventListener('close', () => {
        if (savedBodyStyle === null) document.body.removeAttribute('style');
        else document.body.setAttribute('style', savedBodyStyle);
        const root = document.documentElement;
        const savedBehavior = root.style.scrollBehavior;
        root.style.scrollBehavior = 'auto';
        window.scrollTo(0, scrollPosition);
        opener?.focus({ preventScroll: true });
        root.style.scrollBehavior = savedBehavior;
        pointerStart = null;
    });
    image.addEventListener('error', () => { error.hidden = false; });
    image.addEventListener('load', () => { error.hidden = true; });
    image.addEventListener('dragstart', event => event.preventDefault());
    image.addEventListener('pointerdown', event => {
        if (!event.isPrimary) { pointerStart = null; return; }
        pointerStart = { x: event.clientX, y: event.clientY, id: event.pointerId };
    });
    image.addEventListener('pointerup', event => {
        if (!pointerStart || pointerStart.id !== event.pointerId) return;
        const dx = event.clientX - pointerStart.x;
        const dy = event.clientY - pointerStart.y;
        pointerStart = null;
        if (Math.abs(dx) > 55 && Math.abs(dx) > Math.abs(dy) * 1.5) show(current + (dx < 0 ? 1 : -1));
    });
    image.addEventListener('pointercancel', () => { pointerStart = null; });
})();
