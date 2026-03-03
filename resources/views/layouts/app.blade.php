<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="transition-all" >
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/application_logo_small.webp') }}">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/playground.css'])
</head>

<body
    data-user="{{ Auth::user()->name }}"
    data-ai-access="{{ auth()->user()->ai_access ? '1' : '0' }}"
    data-ai-ready="{{ auth()->user()->ai_ready ? '1' : '0' }}"
    x-data="{ sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === 'true' }"
    x-init="$watch('sidebarCollapsed', v => localStorage.setItem('sidebar-collapsed', v))"
    class="
        relative
        font-sans
        antialiased
        w-screen
        h-screen
        overflow-hidden

        bg-gradient-to-br
        from-blue-50
        via-white
        to-indigo-50

        dark:from-slate-900
        dark:via-slate-950
        dark:to-slate-900
    "
>

<!-- 🌫 Soft Pastel Background Layer -->
<div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">

    <!-- Top Right Soft Blue -->
    <div class="
        absolute
        w-[700px] h-[700px]
        bg-blue-300/30
        dark:bg-blue-500/10
        rounded-full
        blur-[140px]
        top-[-200px]
        right-[-150px]
    ">
    </div>

    <!-- Bottom Left Lavender -->
    <div class="
        absolute
        w-[600px] h-[600px]
        bg-indigo-300/30
        dark:bg-indigo-500/10
        rounded-full
        blur-[140px]
        bottom-[-200px]
        left-[-150px]
    ">
    </div>

    <!-- Center Accent Glow (Very Subtle) -->
    <div class="
        absolute
        w-[500px] h-[500px]
        bg-sky-200/20
        dark:bg-sky-400/5
        rounded-full
        blur-[120px]
        top-[40%]
        left-[30%]
    ">
    </div>

</div>

    <div class="flex h-full w-full gap-3 p-2 transition-all duration-300 ">
        @php
            $currentPage = request()->route('token') ? $encryption->decrypt(request()->route('token')) : null;
        @endphp
        <!-- Sidebar -->
        @if($currentPage == "record" || $currentPage == "compare")
            <aside
                class="hidden md:flex shrink-0 transition-all duration-300  "
                :class="sidebarCollapsed ? 'w-[80px] ' : 'w-[260px] lg:w-[280px] '"
            >
                @include('layouts.nav-patient-table', ['collapsed' => 'sidebarCollapsed'])
            </aside>
        @else
            <aside
                class="hidden md:flex shrink-0 transition-all duration-300"
                :class="sidebarCollapsed ? 'w-[80px]' : 'w-[260px] lg:w-[280px]'"
            >
                @include('layouts.navigation', ['collapsed' => 'sidebarCollapsed'])
            </aside>

        @endif

        <!-- Main Content -->
        <main
            id="main"
            class="
                bg-[var(--clr-surface-a0)]
                border-[var(--clr-surface-a30)]
                text-[var(--clr-text-a0)]
                border
                w-[100]
                flex-1
                h-full
                rounded-xl
                transition-all
                duration-300
                overflow-x-scroll
                [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]
            "
            :class="sidebarCollapsed ? 'pl-1' : 'pl-0'"
        >
            {{ $slot }}
        </main>

    </div>

</body>
</html>
