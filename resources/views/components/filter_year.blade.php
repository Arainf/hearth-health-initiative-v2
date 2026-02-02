@props(['id' => 'year-filter'])

<div class="relative inline-block text-left w-36">
    <button id="{{ $id }}-btn"
            type="button"
            class="flex justify-between items-center w-full px-3 py-2 
                   border border-gray-300 dark:border-gray-600 rounded-md text-sm 
                   bg-white dark:bg-[#212121] shadow-sm 
                   hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors
                   text-gray-700 dark:text-gray-200">
        <span id="{{ $id }}-label">All Years</span>
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
                border border-gray-200 
                rounded-md shadow-lg">
        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
            <li class="{{ $id }}-dropdown-item flex items-center gap-2 px-3 py-2 
                       cursor-pointer hover:bg-gray-100 dark:hover:bg-[#212121]"
                data-value="all">
                All Years
            </li>
        </ul>
    </div>
</div>
