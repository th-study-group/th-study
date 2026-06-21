@props([
    'unit',
    'width',
    'height'
])

<ins
    class="kakao_ad_area"
    style="display:none;"
    data-ad-unit="{{ $unit }}"
    data-ad-width="{{ $width }}"
    data-ad-height="{{ $height }}">
</ins>