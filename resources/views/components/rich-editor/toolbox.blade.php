<div
    id="editorToolbar"
    class="
        toolbox
        z-10
        items-center justify-center gap-1
        px-4 py-2
        bg-white border-b border-gray-200
    "
>

    <!-- Undo -->
    <button @click="window.ReportEditor.undo()" class="toolbar-btn">
        <img src="{{ asset('icons/toolBox/u-turn-left.svg') }}"
         style="
            height: 1.5rem;
            width: 1.5rem;
        ">
    </button>

    <!-- Redo -->
    <button @click="window.ReportEditor.redo()" class="toolbar-btn">
        <img src="{{ asset('icons/toolBox/u-turn-right.svg') }}"
             style="
            height: 1.5rem;
            width: 1.5rem;
        ">
    </button>

    <div class="toolbar-divider"></div>

    <!-- Add this to your toolbar, preferably near other formatting buttons -->
    <div x-data="{ showSizeMenu: false, currentSize: 16 }" class="relative">
        <!-- Font Size Button -->
        <button
            @click="showSizeMenu = !showSizeMenu"
            class="toolbar-btn"
            :class="{ 'bg-gray-200': showSizeMenu }"
            title="Font Size"
        >
            <span x-text="currentSize"></span>
            <i class="fas fa-chevron-right text-xs ml-1 scale-50"></i>
        </button>

        <!-- Font Size Dropdown -->
        <div
            x-show="showSizeMenu"
            @click.away="showSizeMenu = false"
           class="toolbar-dropdown"
        >
            <!-- Decrease Button -->
            <button
                @click="
                if (currentSize > 8) {
                    currentSize -= 2;
                    window.ReportEditor.toggleSize(currentSize);
                }
            "
                class="w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center justify-between"
                :class="{ 'opacity-50 cursor-not-allowed': currentSize <= 8 }"
                title="Decrease font size"
            >
                <span>Smaller</span>
                <span class="text-xs text-gray-500">A-</span>
            </button>

            <!-- Current Size Display -->
            <div class="px-4 py-2 text-sm text-center border-t border-b border-gray-100">
                <span x-text="currentSize"></span>
            </div>

            <!-- Increase Button -->
            <button
                @click="
                if (currentSize < 36) {
                    currentSize += 2;
                    window.ReportEditor.toggleSize(currentSize);
                }
            "
                class="w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center justify-between"
                :class="{ 'opacity-50 cursor-not-allowed': currentSize >= 36 }"
                title="Increase font size"
            >
                <span>Larger</span>
                <span class="text-xs text-gray-500">A+</span>
            </button>

            <!-- Preset Sizes -->
            <template x-for="size in [8, 10, 12, 14, 16, 18, 24, 30, 36]">
                <button
                    @click="
                    currentSize = size;
                   window.ReportEditor.toggleSize(size);
                    showSizeMenu = false;
                "
                    class="w-full px-4 py-1 text-left text-sm hover:bg-gray-100"
                    :class="{ 'bg-blue-50 text-blue-600': currentSize === size }"
                    x-text="`${size}px`"
                ></button>
            </template>
        </div>
    </div>
    <!-- LIST DROPDOWN -->
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="toolbar-btn">
            <img src="{{ asset('icons/toolBox/list.svg') }}">
            <i class="fa-solid fa-chevron-down scale-50 rotate-[270deg]"></i>
        </button>

        <div
            x-show="open"
            x-transition
            x-cloak
            @click.outside="open = false"
            class="toolbar-dropdown"
        >
            <button @click="window.ReportEditor.toggleBulletList(); open=false" class="dropdown-item">
                <img src="{{ asset('icons/toolBox/list.svg') }}">
                Bullet list
            </button>
            <button @click="window.ReportEditor.toggleOrderedList(); open=false" class="dropdown-item">
                <img src="{{ asset('icons/toolBox/ordered-list-outline.svg') }}">
                Numbered list
            </button>
        </div>
    </div>

    <div class="toolbar-divider"></div>

    <!-- FORMATTING -->
    <button @click="window.ReportEditor.toggleBold()" class="toolbar-btn" >
        <img src="{{ asset('icons/toolBox/bold.svg') }}"
             style="
                height: 1rem;
                width: 1rem;
            ">
    </button>

    <button @click="window.ReportEditor.toggleItalic()" class="toolbar-btn" >
        <img src="{{ asset('icons/toolBox/italic.svg') }}"
             style="
                height: 1.5rem;
                width: 1.5rem;
            ">
    </button>

    <button @click="window.ReportEditor.toggleUnderline()" class="toolbar-btn">
        <img src="{{ asset('icons/toolBox/underline.svg') }}"
             style="
                height: 1.5rem;
                width: 1.5rem;
            ">
    </button>

    <button @click="window.ReportEditor.toggleStrike()" class="toolbar-btn">
        <img src="{{ asset('icons/toolBox/strike-through-line.svg') }}"
             style="
                height: 1.5rem;
                width: 1.5rem;
            ">
    </button>

    <div class="toolbar-divider"></div>

    <!-- ALIGN DROPDOWN -->
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="toolbar-btn">
            <img src="{{ asset('icons/toolBox/align-left.svg') }}">
        </button>

        <div
            x-show="open"
            x-transition
            x-cloak
            @click.outside="open = false"
            class="toolbar-dropdown"
        >
            <button @click="window.ReportEditor.setAlign('left'); open=false" class="dropdown-item">
                <img src="{{ asset('icons/toolBox/align-left.svg') }}">
                Left
            </button>
            <button @click="window.ReportEditor.setAlign('center'); open=false" class="dropdown-item">
                <img src="{{ asset('icons/toolBox/align-center.svg') }}">
                Center
            </button>
            <button @click="window.ReportEditor.setAlign('right'); open=false" class="dropdown-item">
                <img src="{{ asset('icons/toolBox/align-right.svg') }}">
                Right
            </button>
        </div>
    </div>

{{--    <button @click="reportEditor.insertTwoColumn()" class="toolbar-btn">--}}
{{--        Two Columns--}}
{{--    </button>--}}


</div>
