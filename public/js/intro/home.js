function initHomeScroll() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const offsetTop = 88;

    function smoothScrollTo(targetY, durationMs) {
        if (prefersReducedMotion) {
            window.scrollTo(0, targetY);
            return;
        }

        const startY = window.scrollY;
        const diff = targetY - startY;
        const start = performance.now();

        function ease(t) {
            return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
        }

        function step(now) {
            const progress = Math.min(1, (now - start) / durationMs);
            window.scrollTo(0, startY + diff * ease(progress));
            if (progress < 1) requestAnimationFrame(step);
        }

        requestAnimationFrame(step);
    }

    document.querySelectorAll('.home-landing a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (event) => {
            const id = anchor.getAttribute('href');
            if (!id || id.length < 2) return;

            const target = document.querySelector(id);
            if (!target) return;

            event.preventDefault();
            const y = target.getBoundingClientRect().top + window.scrollY - offsetTop;
            smoothScrollTo(y, 700);
        });
    });

    const revealTargets = document.querySelectorAll('.home-landing .reveal');
    if (!revealTargets.length) return;

    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                const cards = entry.target.querySelectorAll('.highlight-card');
                cards.forEach((card, index) => {
                    card.style.transitionDelay = `${index * 70}ms`;
                });

                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.14 });

        revealTargets.forEach((el) => revealObserver.observe(el));
        return;
    }

    revealTargets.forEach((el) => el.classList.add('is-visible'));
}

function initHeroTyping() {
    const el = document.getElementById('heroTypingText');
    if (!el) return;

    const fullText = el.dataset.text || el.textContent || '';
    if (!fullText) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
        el.textContent = fullText;
        return;
    }

    el.textContent = '';
    let index = 0;

    const timer = window.setInterval(() => {
        index += 1;
        el.textContent = fullText.slice(0, index);

        if (index >= fullText.length) {
            window.clearInterval(timer);
        }
    }, 48);
}

function initLiteYouTubeEmbeds(root = document) {
    const targets = root.querySelectorAll('.yt-lite[data-video-id]');
    targets.forEach((el) => {
        if (el.dataset.bound === 'Y') return;
        el.dataset.bound = 'Y';
        let lastActivatedAt = 0;

        const activate = () => {
            const videoId = el.dataset.videoId;
            if (!videoId) return;

            if (el.querySelector('iframe')) return;

            const iframe = document.createElement('iframe');
            iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&controls=1&playsinline=1&rel=0&modestbranding=1&fs=1&enablejsapi=1`;
            iframe.title = 'YouTube video player';
            iframe.loading = 'lazy';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
            iframe.allowFullscreen = true;

            el.innerHTML = '';
            el.appendChild(iframe);

            const ctrlBtn = document.createElement('button');
            ctrlBtn.type = 'button';
            ctrlBtn.className = 'yt-control-btn';
            ctrlBtn.textContent = '일시정지';
            ctrlBtn.setAttribute('aria-label', '영상 일시정지');
            ctrlBtn.dataset.playing = 'Y';

            ctrlBtn.addEventListener('click', (evt) => {
                evt.preventDefault();
                evt.stopPropagation();
                const playing = ctrlBtn.dataset.playing === 'Y';
                if (iframe.contentWindow) {
                    iframe.contentWindow.postMessage(
                        JSON.stringify({
                            event: 'command',
                            func: playing ? 'pauseVideo' : 'playVideo',
                            args: [],
                        }),
                        '*'
                    );
                }
                ctrlBtn.dataset.playing = playing ? 'N' : 'Y';
                ctrlBtn.textContent = playing ? '재생' : '일시정지';
                ctrlBtn.setAttribute('aria-label', playing ? '영상 재생' : '영상 일시정지');
            });

            el.appendChild(ctrlBtn);
        };

        const activateFromEvent = (e) => {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const now = Date.now();
            if (now - lastActivatedAt < 450) return;
            lastActivatedAt = now;
            activate();
        };

        el.addEventListener('click', activateFromEvent);
        el.addEventListener('touchend', activateFromEvent, { passive: false });
        el.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                activate();
            }
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initLiteYouTubeEmbeds(document), { once: true });
} else {
    initLiteYouTubeEmbeds(document);
}
