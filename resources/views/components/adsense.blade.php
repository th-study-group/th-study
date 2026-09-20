@props([
    'slot',
    'format' => 'auto',
    'fullWidthResponsive' => false,
])

@if (config('services.adsense.id'))
    <ins
        {{ $attributes->merge(['class' => 'adsbygoogle']) }}
        style="display: block;"
        data-ad-client="{{ config('services.adsense.id') }}"
        data-ad-slot="{{ $slot }}"
        data-ad-format="{{ $format }}"
        @if ($fullWidthResponsive) data-full-width-responsive="true" @endif></ins>
@endif
