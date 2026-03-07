@props([
    'id' => 'filter-search',
    'placeholder' => 'Search records...',
    'width' => 'w-64',
])

<div class="relative h-full   {{ $width }}" {{$attributes}} >
    <input
        id="{{ $id }}"
        type="text"
        placeholder="{{ $placeholder }}"
        class="w-full h-full border-[2px] rounded-lg md:rounded-lg lg:rounded-xl px-3 py-2 pl-7 lg:pl-9
                focus:outline-none
                border-[var(--primary-border)]
               font-inter
               bg-[var(--tertiary-system-background)]
               placeholder-[var(--secondary-color)]
               placeholder:font-normal
               transition-colors
               text-[length:var(--s-sub-header)]
               "
    >
    <svg xmlns="http://www.w3.org/2000/svg"
         class="absolute left-[var(--pos-search-icon)] top-[var(--pos-search-icon)] w-[var(--s-icon)] h-[var(--s-icon)]"
         fill="none" viewBox="0 0 24 24"
         stroke="currentColor"
         stroke-width="2">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
    </svg>
</div>
