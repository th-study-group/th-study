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

function isBirthDatePickerMobileView()
{
    return window.matchMedia('(max-width: 767.98px)').matches;
}

function syncBirthDatePickerSelects(instance)
{
    if (!instance || !instance.birthDatePickerMonthSelect || !instance.birthDatePickerYearSelect) {
        return;
    }

    instance.birthDatePickerYearSelect.value = String(instance.currentYear);
    instance.birthDatePickerMonthSelect.value = String(instance.currentMonth);
}

function mountBirthDatePickerMobileHeader(instance, minYear, maxYear)
{
    if (!instance || !instance.calendarContainer || instance.birthDatePickerMobileHeaderMounted) {
        return;
    }

    const currentMonthWrap = instance.calendarContainer.querySelector('.flatpickr-current-month');
    if (!currentMonthWrap) {
        return;
    }

    const yearSelect = document.createElement('select');
    yearSelect.className = 'form-select form-select-sm birth-date-picker-select';
    yearSelect.setAttribute('aria-label', 'birth year');

    for (let year = maxYear; year >= minYear; year -= 1) {
        const option = document.createElement('option');
        option.value = String(year);
        option.textContent = `${year}\uB144`;
        yearSelect.appendChild(option);
    }

    const monthSelect = document.createElement('select');
    monthSelect.className = 'form-select form-select-sm birth-date-picker-select';
    monthSelect.setAttribute('aria-label', 'birth month');

    for (let month = 0; month < 12; month += 1) {
        const option = document.createElement('option');
        option.value = String(month);
        option.textContent = `${month + 1}\uC6D4`;
        monthSelect.appendChild(option);
    }

    yearSelect.addEventListener('change', function () {
        instance.changeYear(Number(yearSelect.value));
    });

    monthSelect.addEventListener('change', function () {
        instance.changeMonth(Number(monthSelect.value) - instance.currentMonth);
    });

    const mobileHeader = document.createElement('div');
    mobileHeader.className = 'birth-date-picker-mobile-header';
    mobileHeader.appendChild(yearSelect);
    mobileHeader.appendChild(monthSelect);

    instance.calendarContainer.classList.add('birth-date-picker-mobile');
    currentMonthWrap.insertAdjacentElement('afterend', mobileHeader);

    instance.birthDatePickerYearSelect = yearSelect;
    instance.birthDatePickerMonthSelect = monthSelect;
    instance.birthDatePickerMobileHeaderMounted = true;

    syncBirthDatePickerSelects(instance);
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

    const enableMobileSelectHeader = options.mobileSelectHeader === true;
    const isMobileView = enableMobileSelectHeader && isBirthDatePickerMobileView();
    const optionOverrides = { ...options };
    delete optionOverrides.mobileSelectHeader;

    const baseOptions = {
      dateFormat: 'Y-m-d',
      minDate: '1950-01-01',
      maxDate: 'today',
      defaultDate: '1990-01-01',
      showMonths: isMobileView ? 1 : 3,
      disableMobile: true,
      position: 'below',
      locale: {
          weekdays: {
              shorthand: ['일', '월', '화', '수', '목', '금', '토'],
              longhand: ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일']
          }
      },
      ...optionOverrides
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

    const minYear = Number(String(baseOptions.minDate).slice(0, 4)) || 1950;
    const maxYear = baseOptions.maxDate === 'today'
        ? new Date().getFullYear()
        : Number(String(baseOptions.maxDate).slice(0, 4)) || new Date().getFullYear();

    if (isMobileView) {
        const originalOnReady = baseOptions.onReady;
        const originalOnMonthChange = baseOptions.onMonthChange;
        const originalOnYearChange = baseOptions.onYearChange;
        const originalOnOpen = baseOptions.onOpen;

        baseOptions.onReady = function (selectedDates, dateStr, instance) {
            mountBirthDatePickerMobileHeader(instance, minYear, maxYear);
            syncBirthDatePickerSelects(instance);

            if (typeof originalOnReady === 'function') {
                originalOnReady(selectedDates, dateStr, instance);
            }
        };

        baseOptions.onMonthChange = function (selectedDates, dateStr, instance) {
            syncBirthDatePickerSelects(instance);

            if (typeof originalOnMonthChange === 'function') {
                originalOnMonthChange(selectedDates, dateStr, instance);
            }
        };

        baseOptions.onYearChange = function (selectedDates, dateStr, instance) {
            syncBirthDatePickerSelects(instance);

            if (typeof originalOnYearChange === 'function') {
                originalOnYearChange(selectedDates, dateStr, instance);
            }
        };

        baseOptions.onOpen = function (selectedDates, dateStr, instance) {
            syncBirthDatePickerSelects(instance);

            if (typeof originalOnOpen === 'function') {
                originalOnOpen(selectedDates, dateStr, instance);
            }
        };
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
