@props(['logo' => ' '])

<div>
    @switch($logo)
        @case('navigation')
            <img
                class="h-auto w-auto z-50 transition-all duration-300
                dark:hidden md:scale-75"
                src="{{ asset('img/black_line_logo.webp') }}"
                alt="logo dark mode off"

            />

            <img
                class="h-auto w-auto z-50 transition-all duration-300
                hidden dark:block md:scale-75"
                src="{{ asset('img/white_line_logo.png') }}"
                alt="logo dark mode on"
            />


            @break

        @case('black')
            <img
                class="h-auto w-auto z-50 transition-all duration-300 md:scale-75 "
                src="{{ asset('images/black_line_logo.png') }}"
                alt="heart health initiative logo"
            >

            <img
                class="h-auto w-auto scale-110 transition-all duration-300 md:scale-75"
                src="{{ asset('images/application_logo_small.png') }}"
                alt="small logo "
            >
            @break

        @case('login')
            <img
                class="h-auto w-auto z-50 transition-all duration-300 md:scale-75 md:mb-[-46px] "
                src="{{ asset('img/application-logo-seal-white.webp') }}"
                alt="heart health initiative logo"
            >
            @break

        @default
            <img
                class="h-auto w-auto z-50 transition-all duration-300 md:scale-75 md:mb-[-46px]"
                src="{{ asset('img/application-logo-seal-white.webp') }}"
                alt="default logo"
            >
    @endswitch
</div>
