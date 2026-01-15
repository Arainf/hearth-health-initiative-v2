<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/application_logo_small.webp') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/datatable.bt4.css', 'resources/css/playground.css'])
    <script src="https://kit.fontawesome.com/66b8a2d2a7.js" crossorigin="anonymous"></script>
</head>

<body
    data-user="{{ Auth::user()->name }}"
    data-ai-access="{{ auth()->user()->ai_access ? '1' : '0' }}"
    data-ai-ready="{{ auth()->user()->ai_ready ? '1' : '0' }}"
    x-data="{ sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === 'true' }"
    x-init="$watch('sidebarCollapsed', v => localStorage.setItem('sidebar-collapsed', v))"
    class="bg-[#EDEDED] font-sans antialiased w-screen h-screen overflow-hidden"
>

    <div class="flex h-full w-full gap-3 p-2 transition-all duration-300">

        <!-- Sidebar -->
        @if(!request()->routeIs('form'))
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
                flex-1
                h-full
                overflow-auto
                rounded-xl
                transition-all
                duration-300
            "
            :class="sidebarCollapsed ? 'pl-1' : 'pl-0'"
        >
            {{ $slot }}
        </main>

    </div>

</body>
</html>
