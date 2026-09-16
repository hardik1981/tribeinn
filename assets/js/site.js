(() => {
    const toggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('#main-nav');
    const mobile = window.matchMedia('(max-width: 800px)');
    if (!toggle || !nav) return;
    document.documentElement.classList.add('js');
    toggle.hidden = false;
    const close = () => {
        toggle.setAttribute('aria-expanded', 'false');
        nav.classList.remove('is-open');
    };
    toggle.addEventListener('click', () => {
        const open = toggle.getAttribute('aria-expanded') !== 'true';
        toggle.setAttribute('aria-expanded', String(open));
        nav.classList.toggle('is-open', open);
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            close(); toggle.focus();
        }
    });
    document.addEventListener('click', event => {
        if (!nav.contains(event.target) && !toggle.contains(event.target)) close();
    });
    nav.addEventListener('click', event => { if (event.target.closest('a')) close(); });
    nav.addEventListener('focusout', event => {
        if (!nav.contains(event.relatedTarget) && event.relatedTarget !== toggle) close();
    });
    mobile.addEventListener('change', close);
})();
