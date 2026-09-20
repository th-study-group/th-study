@props([
    'adSlot',
    'format' => null,
    'fullWidthResponsive' => false,
])

@if (config('services.adsense.id'))
    <ins
        {{ $attributes->merge(['class' => 'adsbygoogle']) }}
        style="display: block;"
        data-ad-client="{{ config('services.adsense.id') }}"
        data-ad-slot="{{ $adSlot }}"
        @if ($format) data-ad-format="{{ $format }}" @endif
        @if ($fullWidthResponsive) data-full-width-responsive="true" @endif></ins>
@endif
