@props([
    'href' => null,
])

@php
    $baseClasses = "inline-flex items-center gap-2
        px-4 py-2 rounded-lg font-medium
        text-sm tracking-tight
        bg-gradient-to-b from-white/70 to-white/40
        backdrop-blur-md shadow-sm border border-gray-300/60
        text-gray-800
        hover:shadow-md hover:border-gray-400
        active:scale-[0.98]
        transition-all duration-150 ease-out";
@endphp

@if ($href)
    <a href="{{ $href }}"
        {{ $attributes->merge(['class' => $baseClasses]) }}>
        {{ $slot }}
    </a>
@else
    <button
        {{ $attributes->merge([
            'type' => 'button',
            'class' => $baseClasses
        ]) }}>
        {{ $slot }}
    </button>
@endif
