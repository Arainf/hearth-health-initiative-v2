<button id="search-button"
        data-mode="#"
        title="Search"
        class="group flex items-center justify-center w-full px-4 py-3 text-sm font-medium
               rounded-md transition-all border
               focus:outline-none focus:ring-2
               "
        style="
            background: var(--accent-primary);
            color: #ffffff;
            border-color: var(--accent-primary);
        "
    {{ $attributes }}>

    <x-lucide-search class="w-4 h-4 text-white stroke-[3px]" />
</button>
