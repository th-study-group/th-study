$(function () {
    if (isStandalonePwa()) {
        document.body.classList.add('is-standalone-pwa');
    }

    initPwaStandaloneLinkGuards();
});

function initPwaStandaloneLinkGuards()
{
    if (!isStandalonePwa() || typeof bootstrap === 'undefined') {
        return;
    }

    const externalModalEl = document.getElementById('pwaExternalLinkModal');
    const imageModalEl = document.getElementById('pwaImagePreviewModal');
    const externalUrlEl = document.getElementById('pwaExternalLinkUrl');
    const imageTargetEl = document.getElementById('pwaImagePreviewTarget');
    const copyBtn = document.getElementById('pwaExternalLinkCopyBtn');
    const openBtn = document.getElementById('pwaExternalLinkOpenBtn');

    if (!externalModalEl || !imageModalEl || !externalUrlEl || !imageTargetEl || !copyBtn || !openBtn) {
        return;
    }

    const externalModal = new bootstrap.Modal(externalModalEl);
    const imageModal = new bootstrap.Modal(imageModalEl);
    let pendingExternalUrl = '';

    externalModalEl.addEventListener('show.bs.modal', function () {
        document.body.classList.add('pwa-overlay-open');
    });

    imageModalEl.addEventListener('show.bs.modal', function () {
        document.body.classList.add('pwa-overlay-open');
    });

    externalModalEl.addEventListener('hidden.bs.modal', syncPwaOverlayState);
    imageModalEl.addEventListener('hidden.bs.modal', syncPwaOverlayState);

    $(document).on('click', 'a[href], img', function (event) {
        const target = event.target;
        const anchor = target.closest ? target.closest('a[href]') : null;

        if (anchor) {
            const href = normalizeHref(anchor.getAttribute('href'));
            if (!href || shouldIgnorePwaGuard(anchor, href)) {
                return;
            }

            if (isImageUrl(href)) {
                event.preventDefault();
                openImagePreview(href);
                return;
            }

            if (isNavigableBrowserUrl(href)) {
                event.preventDefault();
                pendingExternalUrl = href;
                externalUrlEl.textContent = href;
                externalModal.show();
            }

            return;
        }

        if (target.tagName !== 'IMG' || !shouldOpenImageElementInPwa(target)) {
            return;
        }

        const src = normalizeHref(target.getAttribute('src'));
        if (!src || !isImageUrl(src)) {
            return;
        }

        event.preventDefault();
        openImagePreview(src);
    });

    copyBtn.addEventListener('click', function () {
        if (!pendingExternalUrl) {
            return;
        }

        copyTextToClipboard(pendingExternalUrl)
            .then(function () {
                alert('URL이 복사되었습니다.');
            })
            .catch(function () {
                window.prompt('아래 URL을 복사해 주세요.', pendingExternalUrl);
            });
    });

    openBtn.addEventListener('click', function () {
        if (!pendingExternalUrl) {
            return;
        }

        externalModal.hide();
        const popup = window.open(pendingExternalUrl, '_blank', 'noopener,noreferrer');
        if (!popup) {
            window.location.href = pendingExternalUrl;
        }
    });

    imageModalEl.addEventListener('hidden.bs.modal', function () {
        document.body.classList.remove('pwa-image-preview-open');
        imageTargetEl.setAttribute('src', '');
    });

    function openImagePreview(url) {
        document.body.classList.add('pwa-image-preview-open');
        imageTargetEl.setAttribute('src', url);
        imageModal.show();
    }

    function syncPwaOverlayState() {
        const hasShownModal =
            externalModalEl.classList.contains('show') ||
            imageModalEl.classList.contains('show');

        if (!hasShownModal) {
            document.body.classList.remove('pwa-overlay-open');
        }
    }
}

function isStandalonePwa()
{
    const isIosStandalone = typeof window.navigator.standalone !== 'undefined'
        && window.navigator.standalone === true;
    const isDisplayModeStandalone = !!(window.matchMedia
        && window.matchMedia('(display-mode: standalone)').matches);

    return isIosStandalone || isDisplayModeStandalone;
}

function normalizeHref(rawHref)
{
    const href = String(rawHref || '').trim();
    if (!href || href === '#' || href.startsWith('javascript:')) {
        return '';
    }

    try {
        return new URL(href, window.location.href).toString();
    } catch (error) {
        return '';
    }
}

function shouldIgnorePwaGuard(anchor, href)
{
    if (!anchor) {
        return true;
    }

    if (anchor.hasAttribute('download')) {
        return true;
    }

    const protocol = new URL(href).protocol;
    return ['mailto:', 'tel:', 'sms:', 'intent:'].includes(protocol);
}

function isNavigableBrowserUrl(href)
{
    try {
        const url = new URL(href);
        return ['http:', 'https:'].includes(url.protocol);
    } catch (error) {
        return false;
    }
}

function isImageUrl(href)
{
    try {
        const pathname = new URL(href).pathname.toLowerCase();
        return /\.(jpg|jpeg|png|gif|webp|avif|bmp|svg)$/i.test(pathname);
    } catch (error) {
        return false;
    }
}

function copyTextToClipboard(text)
{
    if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
        return navigator.clipboard.writeText(text);
    }

    return Promise.reject(new Error('clipboard_unavailable'));
}

function shouldOpenImageElementInPwa(imageElement)
{
    if (!imageElement || imageElement.tagName !== 'IMG') {
        return false;
    }

    if (imageElement.hasAttribute('data-pwa-image-preview')) {
        return true;
    }

    return !!imageElement.closest('.blog-show-content, .toastui-editor-contents, article');
}
