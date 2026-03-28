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

    const imageModalEl = document.getElementById('pwaImagePreviewModal');
    const imageTargetEl = document.getElementById('pwaImagePreviewTarget');

    if (!imageModalEl || !imageTargetEl) {
        return;
    }

    const imageModal = new bootstrap.Modal(imageModalEl);

    imageModalEl.addEventListener('show.bs.modal', function () {
        document.body.classList.add('pwa-overlay-open');
    });

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
        const hasShownModal = imageModalEl.classList.contains('show');

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
