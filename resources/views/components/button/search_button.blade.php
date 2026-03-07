<button id="search-button"
        data-mode="#"
        title="Search"
        class="group flex items-center justify-center w-full px-2 py-2
               rounded-lg transition-all
               hover:scale-105
               inner-shadow
               bg-[var(--primary-btn-background)]
               text-[var(--primary-color)]
               h-[var(--h-filter)]
               "
    {{ $attributes }}>

    <x-lucide-search class="w-[var(--s-icon)]  h-[var(--s-icon)]  stroke-[2px]" />
</button>
