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