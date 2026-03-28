<div class="modal fade pwa-link-modal" id="pwaExternalLinkModal" tabindex="-1" aria-labelledby="pwaExternalLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="pwaExternalLinkModalLabel">링크 이동</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="닫기"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="pwa-link-modal-copy">PWA 앱에서는 링크가 브라우저에서 열릴 수 있습니다.</p>
                <div class="pwa-link-modal-url" id="pwaExternalLinkUrl" aria-live="polite"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" id="pwaExternalLinkCopyBtn">복사</button>
                <button type="button" class="btn btn-primary" id="pwaExternalLinkOpenBtn">열기</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pwa-image-modal" id="pwaImagePreviewModal" tabindex="-1" aria-labelledby="pwaImagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="pwaImagePreviewModalLabel">이미지 보기</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="닫기"></button>
            </div>
            <div class="modal-body">
                <img id="pwaImagePreviewTarget" class="pwa-image-modal-target" src="" alt="미리보기 이미지">
            </div>
        </div>
    </div>
</div>
