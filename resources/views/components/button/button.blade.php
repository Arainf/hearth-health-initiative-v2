@props([
    'variant' => 'primary', // primary | ghost | danger
    'disabled' => false,
    'id' => null,
])

@php
$base = 'inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition focus:outline-none';
$variants = [
    'primary' => 'bg-blue-600 text-white hover:bg-blue-700 disabled:bg-blue-300',
    'ghost'   => 'bg-transparent text-gray-700 hover:bg-gray-100',
    'danger'  => 'bg-red-600 text-white hover:bg-red-700 disabled:bg-red-300',
];
@endphp

<button
    @if($id) id="{{ $id }}" @endif
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => $base.' '.$variants[$variant]]) }}
>
    {{ $slot }}
</button>
