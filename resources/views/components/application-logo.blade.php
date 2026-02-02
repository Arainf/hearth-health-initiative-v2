@props(['logo' => ' '])

<div>
    @switch($logo)
        @case('navigation')
            <!-- Full sidebar logo -->
            <img
                class="h-auto w-auto z-50 transition-all duration-300
           dark:hidden"
                src="{{ asset('img/black_line_logo.webp') }}"
                alt="logo dark mode off"
            />

            <img
                class="h-auto w-auto z-50 transition-all duration-300
           hidden dark:block"
                src="{{ asset('img/white_line_logo.png') }}"
                alt="logo dark mode on"
            />


            @break

        @case('black')
            <img
                {{-- x-show="sidebarOpen" --}}
                class="h-auto w-auto z-50 transition-all duration-300"
                src="{{ asset('images/black_line_logo.png') }}"
                alt="heart health initiative logo">

            <img
                {{-- x-show="!sidebarOpen" --}}
                class="h-auto w-auto scale-110 transition-all duration-300"
                src="{{ asset('images/application_logo_small.png') }}"
                alt="small logo ">
            @break

        @case('login')
            <!-- Full sidebar logo -->
            <img
                {{-- x-show="sidebarOpen" --}}
                class="h-auto w-auto z-50 transition-all duration-300"
                src="{{ asset('img/application-logo-seal-white.webp') }}"
                alt="heart health initiative logo">
            @break

        @default
            <img
                class="h-auto w-auto z-50 transition-all duration-300"
                src="{{ asset('img/application-logo-seal-white.webp') }}"
                alt="default logo">
    @endswitch
</div>
