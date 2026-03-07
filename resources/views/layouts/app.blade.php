<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="transition-all" >
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/application_logo_small.webp') }}">

    <!-- Preload image -->
{{--    <link rel="preload" as="image" href="{{ asset('img/black_line_logo.webp') }}">--}}
{{--    <link rel="preload" as="image" href="{{ asset('img/white_line_logo.png') }}">--}}
{{--    <link rel="preload" as="image" href="{{ asset('images/black_line_logo.png') }}">--}}
{{--    <link rel="preload" as="image" href="{{ asset('images/application_logo_small.png') }}">--}}
    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/playground.css'])
</head>

<body
    data-user="{{ Auth::user()->name }}"
    x-data="{ sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === 'true' }"
    x-init="$watch('sidebarCollapsed', v => localStorage.setItem('sidebar-collapsed', v))"
    class=" relative font-sans antialiased w-screen h-screen overflow-hidden" >

    {{--DEBUG SCREEN --}}
    <div class="absolute top-1 left-1 bg-red-300 p-1 z-50 rounded-sm flex items-center justify-center">
        <span class="sm:block md:hidden lg:hidden">
            small
        </span>
        <span class="sm:hidden md:block lg:hidden">
            medium
        </span>
        <span class="sm:hidden md:hidden lg:block">
            large
        </span>
    </div>
    <div class="flex h-full w-full gap-3 p-2 transition-all duration-300 ">
        @php
            $currentPage = request()->route('token') ? $encryption->decrypt(request()->route('token')) : null;
        @endphp
        <!-- Sidebar -->
        @if($currentPage == "record" || $currentPage == "compare")
            <aside
                class="hidden md:flex shrink-0 transition-all duration-300  "
                :class="sidebarCollapsed ? 'sm:w-[60px] md:w-[60px] lg:w-[80px] ' : 'md:w-[200px] lg:w-[280px] '"
            >
                @include('layouts.nav-patient-table', ['collapsed' => 'sidebarCollapsed'])
            </aside>
        @else
            <aside
                class="hidden md:flex shrink-0 transition-all duration-300"
                :class="sidebarCollapsed ? 'sm:w-[60px] md:w-[60px] lg:w-[80px]' : 'md:w-[200px] lg:w-[280px]'"
            >
                @include('layouts.navigation', ['collapsed' => 'sidebarCollapsed'])
            </aside>

        @endif

        <!-- Main Content -->
        <main
            id="main"
            class="
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
