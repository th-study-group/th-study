$(function () {

    $(document).on("contextmenu", function (e) {
        e.preventDefault();
    });

    const modalEl = document.getElementById('loadingModal');
    if (!modalEl || typeof bootstrap === 'undefined') {
        console.warn('[loading] modal or bootstrap missing', {
            hasModal: !!modalEl,
            hasBootstrap: typeof bootstrap !== 'undefined'
        });
        return;
    }

    const loadingModal = new bootstrap.Modal(modalEl, {
        backdrop: 'static',
        keyboard: false
    });

    window.requestAjax = function (options) {
        const settings = $.extend(true, {
            url: '',
            method: 'GET',
            dataType: 'json',
            data: {},
            headers: {},
            showLoading: true,
            shouldHideLoading: null,
            onSuccess: null,
            onError: null,
            onComplete: null,
        }, options);
    
        if (settings.showLoading && typeof window.showLoading === 'function') {
            window.showLoading();
        }
    
        const xhr = $.ajax({
            url: settings.url,
            method: settings.method,
            dataType: settings.dataType,
            data: settings.data,
            headers: settings.headers,
        });
    
        xhr.then(
            function (data, textStatus, jqXHR) {
                if (typeof settings.onSuccess === 'function') {
                    settings.onSuccess(data, textStatus, jqXHR);
                }
            },
            function (jqXHR, textStatus, errorThrown) {
                if (typeof settings.onError === 'function') {
                    settings.onError(jqXHR, textStatus, errorThrown);
                }
            }
        );
    
        xhr.always(function (dataOrXhr, textStatus, jqXHR) {
            const xhrObj = dataOrXhr && dataOrXhr.status !== undefined ? dataOrXhr : jqXHR;
            const shouldHide = typeof settings.shouldHideLoading === 'function'
                ? settings.shouldHideLoading(xhrObj, textStatus)
                : true;
    
            if (settings.showLoading && typeof window.hideLoading === 'function' && shouldHide) {
                window.hideLoading();
            }
    
            if (typeof settings.onComplete === 'function') {
                settings.onComplete(dataOrXhr, textStatus, jqXHR);
            }
        });
    
        return xhr;
    };    

    window.showLoading = function () {
        loadingModal.show();
        setTimeout(function () {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            if (backdrops.length) {
                backdrops[backdrops.length - 1].classList.add('loading-backdrop');
            }
        }, 0);
    };
    
    window.hideLoading = function () {
        if (document.activeElement) {
            document.activeElement.blur();
        }
        loadingModal.hide();
        document.querySelectorAll('.modal-backdrop.loading-backdrop').forEach(function (el) {
            el.classList.remove('loading-backdrop');
        });
    };

    const minVisibleMs = 300;
    showLoading();
    setTimeout(function () {
        hideLoading();
    }, minVisibleMs);
});

function initBirthDatePicker(selector, options = {}) 
{
    const input = document.querySelector(selector);
    const baseOptions = {
      dateFormat: 'Y-m-d',
      minDate: '1950-01-01',
      maxDate: 'today',
      defaultDate: '1990-01-01',
      showMonths: 3,
      ...options
    };

    if (input && input.value) {
      baseOptions.defaultDate = input.value;
    }

    return flatpickr(selector, baseOptions);
}

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
