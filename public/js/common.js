$(function () {
    initFixedHeaderOffset();
    initGlobalBackToTop();
    initDragHorizontalScroll();
    initBackdropSafetyGuard();
    initOffcanvasScrollReset();

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
            processData: undefined,
            contentType: undefined,
            xhr: undefined,
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
            processData: settings.processData,
            contentType: settings.contentType,
            xhr: settings.xhr,
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

    const loadingState = {
        activeCount: 0,
        blockerEl: null,
        isVisible: false,
        visibleSince: 0,
        hideTimer: null,
        watchdogTimer: null,
    };

    const LOADING_WATCHDOG_MS = 15000;
    const LOADING_MIN_VISIBLE_MS = 450;

    function getLoadingBlockerElement() {
        if (loadingState.blockerEl && document.body.contains(loadingState.blockerEl)) {
            return loadingState.blockerEl;
        }

        const blocker = document.createElement('div');
        blocker.id = 'loadingBlocker';
        blocker.className = 'loading-blocker';
        blocker.setAttribute('aria-hidden', 'true');
        document.body.appendChild(blocker);
        loadingState.blockerEl = blocker;

        return blocker;
    }

    function showLoadingUi() {
        if (loadingState.hideTimer) {
            clearTimeout(loadingState.hideTimer);
            loadingState.hideTimer = null;
        }

        if (loadingState.isVisible) {
            return;
        }

        const blocker = getLoadingBlockerElement();
        blocker.classList.add('is-active');
        document.body.classList.add('loading-active');
        loadingModal.show();

        setTimeout(function () {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            if (backdrops.length) {
                backdrops[backdrops.length - 1].classList.add('loading-backdrop');
            }
        }, 0);

        loadingState.isVisible = true;
        loadingState.visibleSince = Date.now();
    }

    function resetLoadingWatchdog() {
        if (loadingState.watchdogTimer) {
            clearTimeout(loadingState.watchdogTimer);
        }

        loadingState.watchdogTimer = setTimeout(function () {
            if (loadingState.activeCount > 0 || loadingState.isVisible) {
                console.warn('[loading] watchdog forced hide', {
                    activeCount: loadingState.activeCount,
                    isVisible: loadingState.isVisible
                });
                window.hideLoading({ force: true });
            }
        }, LOADING_WATCHDOG_MS);
    }

    function hideLoadingUi() {
        const isModalShown = modalEl.classList.contains('show');
        if (!loadingState.isVisible && !isModalShown) {
            return;
        }

        if (document.activeElement) {
            document.activeElement.blur();
        }

        loadingModal.hide();
        document.querySelectorAll('.modal-backdrop.loading-backdrop').forEach(function (el) {
            el.classList.remove('loading-backdrop');
        });

        if (loadingState.blockerEl) {
            loadingState.blockerEl.classList.remove('is-active');
        }
        document.body.classList.remove('loading-active');
        loadingState.isVisible = false;
        loadingState.visibleSince = 0;
    }

    window.showLoading = function () {
        loadingState.activeCount += 1;
        showLoadingUi();
        resetLoadingWatchdog();
    };
    
    window.hideLoading = function (options) {
        const forceHide = !!(options && options.force === true);

        if (!forceHide && loadingState.activeCount > 0) {
            loadingState.activeCount -= 1;
        }

        if (!forceHide && loadingState.activeCount > 0) {
            return;
        }

        loadingState.activeCount = 0;

        if (loadingState.hideTimer) {
            clearTimeout(loadingState.hideTimer);
            loadingState.hideTimer = null;
        }

        const elapsed = loadingState.visibleSince > 0
            ? Date.now() - loadingState.visibleSince
            : LOADING_MIN_VISIBLE_MS;
        const remaining = forceHide ? 0 : Math.max(0, LOADING_MIN_VISIBLE_MS - elapsed);

        if (remaining > 0) {
            loadingState.hideTimer = setTimeout(function () {
                loadingState.hideTimer = null;
                hideLoadingUi();
            }, remaining);
            return;
        }

        if (loadingState.watchdogTimer) {
            clearTimeout(loadingState.watchdogTimer);
            loadingState.watchdogTimer = null;
        }
        hideLoadingUi();
    };

    initInitialEntryLoading(loadingModal);
});

function initInitialEntryLoading(loadingModal)
{
    if (!loadingModal) {
        return;
    }

    // PWA 스플래시가 표시 중이면 끝난 뒤에 짧게 로딩 표시
    if (window.__thSplashVisible === true) {
        window.addEventListener('th:splash:hidden', function () {
            runInitialEntryLoading(loadingModal);
        }, { once: true });
        return;
    }

    runInitialEntryLoading(loadingModal);
}

function runInitialEntryLoading(loadingModal)
{
    const navEntry = performance.getEntriesByType
        ? performance.getEntriesByType('navigation')[0]
        : null;

    // 뒤로가기/앞으로가기 복원 시에는 불필요한 점멸 방지
    if (navEntry && navEntry.type === 'back_forward') {
        return;
    }

    try {
        if (typeof window.showLoading === 'function') {
            window.showLoading();
        } else {
            loadingModal.show();
        }
    } catch (e) {
        return;
    }

    setTimeout(function () {
        if (typeof window.hideLoading === 'function') {
            window.hideLoading();
        } else {
            loadingModal.hide();
        }
    }, 700);
}

function initFixedHeaderOffset()
{
    const navbar = document.querySelector('nav.navbar.sticky-top');
    if (!navbar) {
        document.documentElement.style.setProperty('--app-header-height', '0px');
        return;
    }

    const syncHeaderHeight = function () {
        const rect = navbar.getBoundingClientRect();
        const height = Math.max(0, Math.ceil(rect.height));
        document.documentElement.style.setProperty('--app-header-height', `${height}px`);
    };

    syncHeaderHeight();
    window.addEventListener('resize', syncHeaderHeight);
    window.addEventListener('orientationchange', syncHeaderHeight);
    window.addEventListener('pageshow', syncHeaderHeight);
    setTimeout(syncHeaderHeight, 60);
}

function initOffcanvasScrollReset()
{
    document.addEventListener('show.bs.offcanvas', function (event) {
        const offcanvas = event.target;
        if (!offcanvas || !offcanvas.classList.contains('offcanvas')) {
            return;
        }

        offcanvas.scrollTop = 0;

        const body = offcanvas.querySelector('.offcanvas-body');
        if (body) {
            body.scrollTop = 0;
        }
    });
}

function initBackdropSafetyGuard()
{
    function cleanupStuckOverlays() {
        var hasShownModal = document.querySelector('.modal.show') !== null;
        var hasShownOffcanvas = document.querySelector('.offcanvas.show') !== null;

        if (!hasShownModal) {
            document.querySelectorAll('.modal-backdrop').forEach(function (el) {
                el.remove();
            });
        }

        if (!hasShownOffcanvas) {
            document.querySelectorAll('.offcanvas-backdrop').forEach(function (el) {
                el.remove();
            });
        }

        if (!hasShownModal && !hasShownOffcanvas) {
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('touch-action');
        }
    }

    cleanupStuckOverlays();
    window.addEventListener('pageshow', cleanupStuckOverlays);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            cleanupStuckOverlays();
        }
    });

    // iOS에서 복귀 직후 잔류 레이어가 늦게 붙는 케이스 방지
    setTimeout(cleanupStuckOverlays, 120);
    setTimeout(cleanupStuckOverlays, 500);
    setTimeout(cleanupStuckOverlays, 1200);
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

function initBirthDatePicker(selector, options = {}) 
{
    const input = document.querySelector(selector);
    const toYmd = (date) => {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    };

    const baseOptions = {
      dateFormat: 'Y-m-d',
      minDate: '1950-01-01',
      maxDate: 'today',
      defaultDate: '1990-01-01',
      showMonths: 3,
      disableMobile: true,
      ...options
    };

    if (baseOptions.minDate instanceof Date) {
      baseOptions.minDate = toYmd(baseOptions.minDate);
    }

    if (baseOptions.maxDate instanceof Date) {
      baseOptions.maxDate = toYmd(baseOptions.maxDate);
    }

    if (baseOptions.defaultDate instanceof Date) {
      baseOptions.defaultDate = toYmd(baseOptions.defaultDate);
    }

    if (input && input.value) {
      baseOptions.defaultDate = input.value;
    }

    return flatpickr(selector, baseOptions);
}

function initGlobalBackToTop()
{
    const btn = document.getElementById('globalBackToTop');
    if (!btn) {
        return;
    }
    btn.classList.add('is-visible');

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

function initDragHorizontalScroll()
{
    const wrappers = document.querySelectorAll('.board-table-wrap');
    if (!wrappers.length) {
        return;
    }

    wrappers.forEach(function (el) {
        let isPointerDown = false;
        let isDragging = false;
        let startX = 0;
        let startScrollLeft = 0;

        el.addEventListener('pointerdown', function (e) {
            if (e.button !== 0) {
                return;
            }

            if (el.scrollWidth <= el.clientWidth) {
                return;
            }

            if (e.target.closest('a, button, input, select, textarea, label')) {
                return;
            }

            isPointerDown = true;
            isDragging = false;
            startX = e.clientX;
            startScrollLeft = el.scrollLeft;
            el.classList.add('is-dragging');
        });

        el.addEventListener('pointermove', function (e) {
            if (!isPointerDown) {
                return;
            }

            const diffX = e.clientX - startX;
            if (!isDragging && Math.abs(diffX) > 5) {
                isDragging = true;
            }

            if (!isDragging) {
                return;
            }

            el.scrollLeft = startScrollLeft - diffX;
            e.preventDefault();
        });

        function stopDragging() {
            if (!isPointerDown) {
                return;
            }

            isPointerDown = false;
            el.classList.remove('is-dragging');

            if (isDragging) {
                el.dataset.dragSuppressClickUntil = String(Date.now() + 150);
            }
        }

        el.addEventListener('pointerup', stopDragging);
        el.addEventListener('pointercancel', stopDragging);
        el.addEventListener('pointerleave', stopDragging);

        el.addEventListener('click', function (e) {
            const suppressUntil = Number(el.dataset.dragSuppressClickUntil || 0);
            if (suppressUntil > Date.now()) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);
    });
}
