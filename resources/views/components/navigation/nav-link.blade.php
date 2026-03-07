@props([
    'active' => false,
    'style' => null,
])

@php
    $base = '
        group
        flex items-center
        gap-4
        pr-4 py-3
        pl-3
        rounded-xl
        text-sm font-normal
        transition-all duration-300
        text-[var(--text)]
        font-inter
    ';

    $accentColors = match ($style) {
        1 => 'text-[var(--accent-1)]',
        2 => 'text-[var(--accent-2)]',
        3 => 'text-[var(--accent-3)]',
        4 => 'text-[var(--accent-4)]',
        5 => 'text-[var(--accent-5)]',
        default => 'text-[var(--accent-primary)]',
    };

    /* Active vs Inactive */
    $activeClasses = "nav-item {$accentColors}";
    $inactiveClasses = "nav-item-shadow hover:{$accentColors}";

    $classes = $base . ' ' . ($active ? $activeClasses : $inactiveClasses);
@endphp

<a {{ $attributes->class([$classes]) }}>
    {{ $slot }}
</a>
