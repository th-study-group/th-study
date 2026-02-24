@push('scripts')
    <script>
        tinymce.init({
            selector: 'textarea.tinymce',
            height: 500,

            plugins: [
                'lists', 'link', 'image', 'media', 'table',
                'codesample', 'fullscreen', 'preview', 'searchreplace'
            ],

            toolbar: [
                'undo redo | blocks | bold italic underline | forecolor backcolor |',
                'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent |',
                'link image media table | codesample code | fullscreen preview'
            ].join(' '),

            automatic_uploads: true,
            images_upload_credentials: true,
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function (resolve, reject) {
                    var formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());

                    if (typeof window.requestAjax !== 'function') {
                        reject('요청 함수가 준비되지 않았습니다.');
                        return;
                    }

                    window.requestAjax({
                        url: '{{ route('notes.upload-image') }}',
                        method: 'POST',
                        dataType: 'json',
                        data: formData,
                        processData: false,
                        contentType: false,
                        showLoading: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        xhr: function () {
                            var xhr = $.ajaxSettings.xhr();
                            if (xhr && xhr.upload) {
                                xhr.upload.addEventListener('progress', function (e) {
                                    if (e.lengthComputable) {
                                        progress((e.loaded / e.total) * 100);
                                    }
                                });
                            }
                            return xhr;
                        },
                        onSuccess: function (data) {
                            if (!data || typeof data.location !== 'string') {
                                reject('Invalid response format');
                                return;
                            }
                            resolve(data.location);
                        },
                    onError: function (jqXHR) {
                        var message = '이미지 업로드 실패';
                        if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            message = jqXHR.responseJSON.message;
                        }
                        if (jqXHR && jqXHR.status) {
                            message += ' (' + jqXHR.status + ')';
                        }
                        reject(message);
                    }
                });
            });
        }
        });
    </script>
@endpush
