
<div
    id="editorToolbar"
    class="
        toolbox
        z-10
        items-center justify-center gap-1
        px-4 py-2
        bg-[var(--clr-surface-a10)]
    "
>

    <div x-data="{ showSizeMenu: false, currentSize: 14 }" class="relative">
        <button
            @click="showSizeMenu = !showSizeMenu"
            class="toolbar-btn1"
            :class="{ 'bg-[var(--clr-surface-a30)]': showSizeMenu }"
            title="Font Size"
        >
            <span x-text="currentSize"></span>
            <i class="fas fa-chevron-right text-xs ml-1 scale-50"></i>
        </button>

        <div
            x-show="showSizeMenu"
            @click.away="showSizeMenu = false"
           class="toolbar-dropdown"
        >

            <button
                @click="
                if (currentSize > 8) {
                    currentSize -= 2;
                    window.ReportEditor.toggleSize(currentSize);
                }
            "
                class="w-full px-4 py-2 text-sm text-[var(--clr-text-a20)] hover:bg-[var(--clr-surface-a70)] flex items-center justify-between"
                :class="{ 'opacity-50 cursor-not-allowed': currentSize <= 8 }"
                title="Decrease font size"
            >
                <span>Smaller</span>
                <span class="text-xs text-[var(--clr-text-a30)]">A-</span>
            </button>


            <div class="px-4 py-2 text-sm text-center border-t border-b border-[var(--clr-surface-a30)]">
                <span x-text="currentSize"></span>
            </div>


            <button
                @click="
                if (currentSize < 36) {
                    currentSize += 2;
                    window.ReportEditor.toggleSize(currentSize);
                }
            "
                class="w-full px-4 py-2 text-sm text-[var(--clr-text-a20)] hover:bg-[var(--clr-surface-a30)] flex items-center justify-between"
                :class="{ 'opacity-50 cursor-not-allowed': currentSize >= 36 }"
                title="Increase font size"
            >
                <span>Larger</span>
                <span class="text-xs text-[var(--clr-text-a30)]">A+</span>
            </button>


            <template x-for="size in [8, 10, 12, 14, 16, 18, 24, 30, 36]">
                <button
                    @click="
                    currentSize = size;
                   window.ReportEditor.toggleSize(size);
                    showSizeMenu = false;
                "
                    class="w-full px-4 py-1 text-left text-sm hover:bg-[var(--clr-surface-a30)]"
                    :class="{ 'bg-[var(--clr-text-a70)] text-[var(--clr-text-a0)]': currentSize === size }"
                    x-text="`${size}px`"
                ></button>
            </template>
        </div>
    </div>

    <!-- LIST DROPDOWN -->
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="toolbar-btn">
            <img class="dark:invert" src="{{ asset('icons/toolBox/list.svg') }}">
            <i class="fa-solid fa-chevron-down scale-50 rotate-[270deg] " style="color: var(--clr-text-a0)"></i>
        </button>

        <div
            x-show="open"
            x-transition
            x-cloak
            @click.outside="open = false"
            class="toolbar-dropdown"
        >
            <button @click="window.ReportEditor.toggleBulletList(); open=false" class="dropdown-item dark:text-white">
                <img class="dark:invert" src="{{ asset('icons/toolBox/list.svg') }}">
                Bullet list
            </button>
            <button @click="window.ReportEditor.toggleOrderedList(); open=false" class="dropdown-item dark:text-white">
                <img class="dark:invert" src="{{ asset('icons/toolBox/ordered-list-outline.svg') }}">
                Numbered list
            </button>
        </div>
    </div>

    <div class="toolbar-divider"></div>

    <!-- FORMATTING -->
    <button @click="window.ReportEditor.toggleBold()" class="toolbar-btn" >
        <img class="dark:invert" src="{{ asset('icons/toolBox/bold.svg') }}"
             style="
                height: 1rem;
                width: 1rem;
            ">
    </button>

    <button @click="window.ReportEditor.toggleItalic()" class="toolbar-btn" >
        <img class="dark:invert" src="{{ asset('icons/toolBox/italic.svg') }}"
             style="
                height: 1.5rem;
                width: 1.5rem;
            ">
    </button>

    <button @click="window.ReportEditor.toggleUnderline()" class="toolbar-btn">
        <img class="dark:invert" src="{{ asset('icons/toolBox/underline.svg') }}"
             style="
                height: 1.5rem;
                width: 1.5rem;
            ">
    </button>

    <button @click="window.ReportEditor.toggleStrike()" class="toolbar-btn">
        <img class="dark:invert" src="{{ asset('icons/toolBox/strike-through-line.svg') }}"
             style="
                height: 1.5rem;
                width: 1.5rem;
            ">
    </button>

    <div class="toolbar-divider"></div>

    <!-- ALIGN DROPDOWN -->
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="toolbar-btn">
            <img class="dark:invert" src="{{ asset('icons/toolBox/align-left.svg') }}">
        </button>

        <div
            x-show="open"
            x-transition
            x-cloak
            @click.outside="open = false"
            class="toolbar-dropdown"
        >
            <button @click="window.ReportEditor.setAlign('left'); open=false" class="dropdown-item">
                <img class="dark:invert"  src="{{ asset('icons/toolBox/align-left.svg') }}">
                Left
            </button>
            <button @click="window.ReportEditor.setAlign('center'); open=false" class="dropdown-item">
                <img class="dark:invert" src="{{ asset('icons/toolBox/align-center.svg') }}">
                Center
            </button>
            <button @click="window.ReportEditor.setAlign('right'); open=false" class="dropdown-item">
                <img class="dark:invert" src="{{ asset('icons/toolBox/align-right.svg') }}">
                Right
            </button>
        </div>
    </div>


</div>
