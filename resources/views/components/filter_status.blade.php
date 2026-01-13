@props(['id' => 'status-filter'])

<div class="relative inline-block text-left w-44">
    <button id="{{ $id }}-btn"
            type="button"
            class="flex justify-between items-center w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-white shadow-sm hover:bg-gray-50 transition">
        <span id="{{ $id }}-label">All</span>
        <svg class="w-4 h-4 ml-2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div id="{{ $id }}-menu"
         class="hidden absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg">
        <ul class="py-1 text-sm text-gray-700">
            {{-- Placeholder (will be populated dynamically via JS) --}}
            <li class="dropdown-item flex items-center gap-2 px-3 py-2 cursor-pointer hover:bg-gray-100" data-value="all">
                <span class="inline-block rounded-full h-3 w-3 bg-gray-400"></span>
                All
            </li>
        </ul>
    </div>
</div>
