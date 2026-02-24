<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/common.js') }}?v={{ filemtime(public_path('js/common.js')) }}"></script>
<script src="{{ asset('js/splash.js') }}?v={{ filemtime(public_path('js/splash.js')) }}"></script>
<script src="{{ asset('js/pwa_push.js') }}?v={{ filemtime(public_path('js/pwa_push.js')) }}"></script>
<script src="{{ asset('tinymce/tinymce.min.js') }}?v={{ filemtime(public_path('tinymce/tinymce.min.js')) }}"></script>
<script src="https://cdn.tiny.cloud/1/{{ config('services.tinyeditor.key') }}/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>