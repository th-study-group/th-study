$(function () {
    initGlobalBackToTop();
    initDragHorizontalScroll();

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
