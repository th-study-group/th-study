@props([
    'adSlot',
    'format' => 'auto',
    'fullWidthResponsive' => false,
])

@if (config('services.adsense.id'))
    <ins
        {{ $attributes->merge(['class' => 'adsbygoogle']) }}
        style="display: block;"
        data-ad-client="{{ config('services.adsense.id') }}"
        data-ad-slot="{{ $adSlot }}"
        data-ad-format="{{ $format }}"
        @if ($fullWidthResponsive) data-full-width-responsive="true" @endif></ins>
@endif
