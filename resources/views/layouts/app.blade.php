<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="transition-all" >
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/application_logo_small.webp') }}">

    <!-- Fonts -->
{{--    <link rel="preconnect" href="https://fonts.bunny.net">--}}
{{--    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />--}}

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/playground.css'])
    <script src="https://kit.fontawesome.com/66b8a2d2a7.js" crossorigin="anonymous"></script>
</head>

<body
    data-user="{{ Auth::user()->name }}"
    data-ai-access="{{ auth()->user()->ai_access ? '1' : '0' }}"
    data-ai-ready="{{ auth()->user()->ai_ready ? '1' : '0' }}"
    x-data="{ sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === 'true' }"
    x-init="$watch('sidebarCollapsed', v => localStorage.setItem('sidebar-collapsed', v))"
    class="
    bg-[var(--clr-surface-a0)]
    font-sans
    antialiased
    w-screen
    h-screen
    overflow-hidden"
>

    <div class="flex h-full w-full gap-3 p-2 transition-all duration-300 ">

        <!-- Sidebar -->
        @if(!request()->routeIs('record'))
            <aside
                class="hidden md:flex shrink-0 transition-all duration-300"
                :class="sidebarCollapsed ? 'w-[80px]' : 'w-[260px] lg:w-[280px]'"
            >
                @include('layouts.navigation', ['collapsed' => 'sidebarCollapsed'])
            </aside>
        @else
            <aside
                class="hidden md:flex shrink-0 w-[280px]"
            >
                @include('layouts.nav-patient-table')
            </aside>
        @endif

        <!-- Main Content -->
        <main
            id="main"
            class="
                bg-[var(--clr-surface-a10)]
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
