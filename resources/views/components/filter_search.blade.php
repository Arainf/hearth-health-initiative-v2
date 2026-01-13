@props([
    'id' => 'filter-search',
    'placeholder' => 'Search records...',
    'width' => 'w-64',
])

<div class="relative {{ $width }}">
    <input
        id="{{ $id }}"
        type="text"
        placeholder="{{ $placeholder }}"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 pl-9 focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm"
    >
    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-2.5 top-2.5 h-5 w-5 text-gray-400" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
    </svg>
</div>
