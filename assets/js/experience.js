(() => {
    document.querySelectorAll('[data-media-slider]').forEach(slider => {
        const slides = [...slider.querySelectorAll('.journal-slide')];
        const count = slider.querySelector('[data-media-count]');
        let current = 0;
        let pointer = null;
        const rearImages = [...slider.querySelectorAll('.journal-rear-stack img')];
        function updateStack() {
            const upcoming = [];
            for (let step = 1; step < slides.length && upcoming.length < rearImages.length; step++) {
                const image = slides[(current + step) % slides.length].querySelector('img');
                if (image) upcoming.push(image.src);
            }
            rearImages.forEach((image, index) => {
                image.hidden = !upcoming[index];
                if (upcoming[index]) image.src = upcoming[index];
            });
        }
        function stopVideo(slide) {
            slide.querySelector('iframe')?.remove();
            const play = slide.querySelector('.journal-play');
            if (play) play.hidden = false;
        }
        function show(index) {
            stopVideo(slides[current]);
            slides[current].hidden = true;
            current = (index + slides.length) % slides.length;
            slides[current].hidden = false;
            const image = slides[current].querySelector('img');
            if (image) image.loading = 'eager';
            if (count) count.textContent = `${current + 1} / ${slides.length}`;
            updateStack();
        }
        slider.querySelector('[data-previous]')?.addEventListener('click', () => show(current - 1));
        slider.querySelector('[data-next]')?.addEventListener('click', () => show(current + 1));
        slider.addEventListener('keydown', event => {
            if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                event.preventDefault(); show(current + (event.key === 'ArrowRight' ? 1 : -1));
            }
        });
        slider.addEventListener('pointerdown', event => {
            if (!event.isPrimary || event.target.closest('button, iframe')) return;
            pointer = { x: event.clientX, y: event.clientY, id: event.pointerId };
        });
        slider.addEventListener('pointerup', event => {
            if (!pointer || pointer.id !== event.pointerId) return;
            const dx = event.clientX - pointer.x, dy = event.clientY - pointer.y;
            pointer = null;
            if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.5) show(current + (dx < 0 ? 1 : -1));
        });
        slider.addEventListener('pointercancel', () => { pointer = null; });
        slider.querySelectorAll('.journal-play').forEach(button => button.addEventListener('click', () => {
            const wrapper = button.closest('[data-youtube]');
            const frame = document.createElement('iframe');
            frame.src = `https://www.youtube-nocookie.com/embed/${wrapper.dataset.youtube}?autoplay=0&rel=0`;
            frame.title = button.getAttribute('aria-label').replace('Load video for', 'Video for');
            frame.allow = 'encrypted-media; picture-in-picture; fullscreen';
            frame.allowFullscreen = true;
            frame.referrerPolicy = 'strict-origin-when-cross-origin';
            button.hidden = true;
            wrapper.append(frame);
            frame.focus();
        }));
    });
})();
