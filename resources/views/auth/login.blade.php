<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @auth
        <a
            href="{{ route('dashboard') }}"
            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
        >
            Dashboard
        </a>
    @endauth

    @guest
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="relative w-[350px] flex flex-row items-center">
                <i class="ml-3 text-[#092D69] absolute fa-solid fa-user"></i>
                <span class="flex flex-col w-full">
            <input id="email"
                   class="peer w-full h-[50px] bg-white/80  text-black placeholder:font-bold placeholder-gray-500 pl-10 sora-400 rounded-lg shadow-2xl "
                   type="text"
                   name="username"
                   placeholder="Username"
                   required autofocus autocomplete="off" />

            </span>

            </div>
            <x-input-error :messages="$errors->get('username')" class="mt-2 bottom-[-40%]" />

            <!-- Password -->
            <div class="relative mt-2 w-[350px] flex flex-row items-center">

                <i class="ml-3 text-[#092D69] absolute fa-solid fa-lock"></i>
                <input id="password" class="peer w-full h-[50px] bg-white/80 text-black placeholder:font-bold placeholder-gray-500 placeholder-weight-500 pl-10 sora-400 rounded-lg shadow-2xl  focus:outline-none"
                       type="password"
                       name="password"
                       placeholder="Password"
                       required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center  mt-4">
                <x-primary-button id="logIn" class="tracking-tighter h-[50px] justify-center w-full bg-[#00205B] hover:bg-[#143774]">
                    <p class="text-sm text-white">LOGIN</p>
                </x-primary-button>
            </div>
        </form>
    @endguest




</x-guest-layout>
