@props([
    'variant' => 'primary',
    'disabled' => false,
    'id' => null,
])

@php
    $base = 'inline-flex items-center justify-center gap-1.5 font-medium transition focus:outline-none rounded-md

             md:px-3 md:py-1.5 md:text-[11px] md:rounded-md md:gap-1
             lg:px-4 lg:py-2 lg:text-sm lg:rounded-lg lg:gap-2';

    $variants = [
        'primary' => 'bg-[var(--primary-btn-background)] text-[var(--primary-color)]
                      disabled:bg-[var(--primary-disabled-background)] disabled:cursor-not-allowed
                      disabled:opacity-60',

        'ghost'   => 'bg-[var(--secondary-btn-background)] text-[var(--secondary-color)]
                      hover:opacity-90 hover:scale-[1.02]',

        'danger'  => 'bg-red-600 text-white
                      hover:bg-red-700
                      disabled:bg-red-300 disabled:cursor-not-allowed disabled:opacity-60',
    ];
@endphp

<button
    @if($id) id="{{ $id }}" @endif
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => $base . ' ' . $variants[$variant]]) }}
>
    {{ $slot }}
</button>
