@props(['id' => 'status-filter'])

<div class="relative inline-block text-left w-44">
    <button id="{{ $id }}-btn"
            type="button"
            class="flex justify-between items-center w-full px-3 py-2
                   border border-gray-300 dark:border-gray-600 rounded-md text-sm
                   bg-white dark:bg-[#212121] shadow-sm
                   hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors
                   text-gray-700 dark:text-gray-200">
        <span id="{{ $id }}-label">All</span>
        <svg class="w-4 h-4 ml-2 text-gray-500 dark:text-gray-500"
             fill="none"
             stroke="currentColor"
             stroke-width="2"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div id="{{ $id }}-menu"
         class="hidden absolute z-10 mt-1 w-full
                bg-white dark:bg-[#212121]
                border border-gray-200 dark:border-gray-700
                rounded-md shadow-lg">
        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
            {{-- Placeholder (will be populated dynamically via JS) --}}
            <li class="{{ $id }}-dropdown-item flex items-center gap-2 px-3 py-2
                       cursor-pointer hover:bg-gray-100 dark:bg-[#212121]"
                data-value="all">
                <span class="inline-block rounded-full h-3 w-3 bg-gray-400 dark:bg-gray-500"></span>
                All
            </li>
        </ul>
    </div>
</div>


{{--data.forEach(status => {--}}
{{--const colorMap = {--}}
{{--'approved': '#16a34a',--}}
{{--'pending': '#f59e0b',--}}
{{--'not evaluated': '#9ca3af'--}}
{{--};--}}
{{--const color = colorMap[status.status_name.toLowerCase()] || '#6b7280';--}}
{{--const formattedValue = status.status_name.toLowerCase();--}}

{{--menuList.insertAdjacentHTML('beforeend', `--}}
{{--<li class="status-filter-dropdown-item flex items-center gap-2 px-3 py-2 cursor-pointer hover:bg-gray-100"--}}
{{--    data-value="${formattedValue}">--}}
{{--    <span class="inline-block rounded-full h-3 w-3" style="background-color:${color}"></span>--}}
{{--    ${status.status_name}--}}
{{--    <span class="ml-auto text-gray-500">(${status.count})</span>--}}
{{--</li>--}}
{{--`);--}}
{{--});--}}
