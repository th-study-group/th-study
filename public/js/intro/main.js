function updateEmptyRowColspan(tableSelector, cellSelector)
{
    var $table = $(tableSelector);
    if (!$table.length) {
        return;
    }

    var $cell = $(cellSelector);
    if (!$cell.length) {
        return;
    }

    var visibleCols = $table.find('thead th:visible').length;
    if (visibleCols > 0) {
        $cell.attr('colspan', visibleCols);
    }
}

function initHomeScroll(){
    const backToTopBtn = document.getElementById('backToTop');
    const sentinel = document.getElementById('topSentinel');

    function smoothScrollTo(targetY, durationMs) {
        const startY = window.scrollY;
        const diff = targetY - startY;
        const start = performance.now();

        function ease(t){
            return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
        }

        function step(now){
            const p = Math.min(1, (now - start) / durationMs);
            window.scrollTo(0, startY + diff * ease(p));
            if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    document.querySelectorAll('.home-landing a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (event) => {
            const id = anchor.getAttribute('href');
            if (!id || id.length < 2) return;
            const el = document.querySelector(id);
            if (!el) return;
            event.preventDefault();
            const y = el.getBoundingClientRect().top + window.scrollY - 96;
            smoothScrollTo(y, 720);
        });
    });

    /*function enableWheelSmooth(){
        let current = window.scrollY;
        let target = window.scrollY;
        let ticking = false;

        const strength = 0.1;
        const maxStep = 220;

        function animate(){
            ticking = true;
            current += (target - current) * strength;
            window.scrollTo(0, current);

            if (Math.abs(target - current) < 0.6) {
                ticking = false;
                return;
            }
            requestAnimationFrame(animate);
        }

        window.addEventListener('wheel', (event) => {
            const inModal = document.querySelector('.modal.show');
            if (inModal) return;
            event.preventDefault();

            const delta = Math.max(-maxStep, Math.min(maxStep, event.deltaY));
            target = Math.max(0, target + delta);

            if (!ticking) {
                current = window.scrollY;
                requestAnimationFrame(animate);
            }
        }, { passive: false });

        window.addEventListener('scroll', () => {
            if (!ticking) {
                current = window.scrollY;
                target = window.scrollY;
            }
        }, { passive: true });
    }
    enableWheelSmooth();
    */

    if (backToTopBtn && sentinel) {
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((entries) => {
                const isTopVisible = entries[0].isIntersecting;
                backToTopBtn.style.display = isTopVisible ? 'none' : 'flex';
            }, { threshold: 0 });

            io.observe(sentinel);
        } else {
            function toggleBackToTop(){
                const y = window.pageYOffset || document.documentElement.scrollTop || 0;
                backToTopBtn.style.display = (y > 200) ? 'flex' : 'none';
            }
            window.addEventListener('scroll', toggleBackToTop, { passive: true });
            window.addEventListener('resize', toggleBackToTop, { passive: true });
            toggleBackToTop();
        }

        backToTopBtn.addEventListener('click', () => {
            smoothScrollTo(0, 780);
        });
    }

    const revealTargets = document.querySelectorAll('.home-landing .reveal');
    if (revealTargets.length) {
        if ('IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                });
            }, { threshold: 0.12 });

            revealTargets.forEach((el) => revealObserver.observe(el));
        } else {
            revealTargets.forEach((el) => el.classList.add('is-visible'));
        }
    }
}
