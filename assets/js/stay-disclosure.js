(() => {
    document.querySelectorAll('.gallery-disclosure').forEach(button => {
        const grid = document.getElementById(button.getAttribute('aria-controls'));
        if (!grid) return;
        grid.classList.add('disclosure-ready');
        button.addEventListener('click', () => {
            const expanded = grid.classList.toggle('is-expanded');
            button.setAttribute('aria-expanded', String(expanded));
            button.textContent = expanded ? button.dataset.less : button.dataset.more;
            // Keep the collapse control in view when the removed cards were above it.
            if (!expanded && button.getBoundingClientRect().top < 0) {
                button.scrollIntoView({ block: 'end', behavior: 'instant' });
            }
        });
    });
})();
