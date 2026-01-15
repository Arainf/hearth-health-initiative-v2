<div
    id="editorToolbar"
    class="
        sticky top-0 z-10
        flex items-center justify-center gap-1
        px-4 py-2
        bg-white border-b border-gray-200
        hidden
    "
>

    <!-- Undo -->
    <button @click="undo()" class="toolbar-btn">
        <img src="{{ asset('icons/toolBox/u-turn-left.svg') }}">
    </button>

    <!-- Redo -->
    <button @click="redo()" class="toolbar-btn">
        <img src="{{ asset('icons/toolBox/u-turn-right.svg') }}">
    </button>

    <div class="toolbar-divider"></div>

    <!-- HEADING DROPDOWN -->
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="toolbar-btn">
            <img src="{{ asset('icons/toolBox/h.svg') }}">
            <i class="fa-solid fa-chevron-down scale-50"></i>
        </button>

        <div
            x-show="open"
            x-transition
            x-cloak
            @click.outside="open = false"
            class="toolbar-dropdown"
        >
            <button @click="toggleHeading(1); open=false" class="dropdown-item">
                H1
            </button>
            <button @click="toggleHeading(2); open=false" class="dropdown-item">
                H2
            </button>
            <button @click="toggleHeading(3); open=false" class="dropdown-item">
                H3
            </button>
        </div>
    </div>

    <!-- LIST DROPDOWN -->
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="toolbar-btn">
            <img src="{{ asset('icons/toolBox/list.svg') }}">
            <i class="fa-solid fa-chevron-down scale-50"></i>
        </button>

        <div
            x-show="open"
            x-transition
            x-cloak
            @click.outside="open = false"
            class="toolbar-dropdown"
        >
            <button @click="toggleBulletList(); open=false" class="dropdown-item">
                <img src="{{ asset('icons/toolBox/list.svg') }}">
                Bullet list
            </button>
            <button @click="toggleOrderedList(); open=false" class="dropdown-item">
                <img src="{{ asset('icons/toolBox/ordered-list-outline.svg') }}">
                Numbered list
            </button>
        </div>
    </div>

    <div class="toolbar-divider"></div>

    <!-- FORMATTING -->
    <button @click="toggleBold()" class="toolbar-btn" :class="{ 'is-active': isActive('bold') }">
        <img src="{{ asset('icons/toolBox/bold.svg') }}" class="w-4 h-4">
    </button>

    <button @click="toggleItalic()" class="toolbar-btn" :class="{ 'is-active': isActive('italic') }">
        <img src="{{ asset('icons/toolBox/italic.svg') }}" class="w-5 h-5">
    </button>

    <button @click="toggleUnderline()" class="toolbar-btn">
        <img src="{{ asset('icons/toolBox/underline.svg') }}" class="w-5 h-5">
    </button>

    <button @click="toggleStrike()" class="toolbar-btn">
        <img src="{{ asset('icons/toolBox/strike-through-line.svg') }}" class="w-5 h-5">
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
            <button @click="setAlign('left'); open=false" class="dropdown-item">
                <img src="{{ asset('icons/toolBox/align-left.svg') }}">
                Left
            </button>
            <button @click="setAlign('center'); open=false" class="dropdown-item">
                <img src="{{ asset('icons/toolBox/align-center.svg') }}">
                Center
            </button>
            <button @click="setAlign('right'); open=false" class="dropdown-item">
                <img src="{{ asset('icons/toolBox/align-right.svg') }}">
                Right
            </button>
        </div>
    </div>

</div>
