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
        border
        border-transparent
       text-[var(--sidebar-text)]
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
    $activeClasses = "bg-[var(--clr-surface-a20)] {$accentColors}";
    $inactiveClasses = "hover:bg-[var(--clr-surface-a20)] hover:{$accentColors}";

    $classes = $base . ' ' . ($active ? $activeClasses : $inactiveClasses);
@endphp

<a {{ $attributes->class([$classes]) }}>
    {{ $slot }}
</a>
