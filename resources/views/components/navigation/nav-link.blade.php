@props([
    'active' => false,
    'style' => null,
])

@php
$base = '
    group
    flex items-center
    gap-4
    px-4 py-3
    rounded-xl
    text-sm font-medium
    transition-all duration-200
';

$inactive = 'text-gray-500 hover:bg-gray-50 hover:text-gray-900';

$palette = match ($style) {
    1 => ['bg' => 'bg-amber-50',  'text' => 'text-[#F1AE00]', 'border' => 'border-amber-200'], // Admin
    2 => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'border' => 'border-indigo-200'], // User
    3 => ['bg' => 'bg-green-50',  'text' => 'text-green-600', 'border' => 'border-green-200'],
    4 => ['bg' => 'bg-red-50',    'text' => 'text-red-600',   'border' => 'border-red-200'],
    5 => ['bg' => 'bg-cyan-50',   'text' => 'text-cyan-700',  'border' => 'border-cyan-200'], // Doctor
    default => ['bg' => 'bg-gray-50', 'text' => 'text-gray-800', 'border' => 'border-gray-200'],
};


$activeClasses = "{$palette['bg']} {$palette['text']} border {$palette['border']}";

$classes = $base . ' ' . ($active ? $activeClasses : $inactive);
@endphp

<a {{ $attributes->class([$classes]) }}>
    {{ $slot }}
</a>
