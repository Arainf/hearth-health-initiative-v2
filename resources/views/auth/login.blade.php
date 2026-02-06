<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @auth
        <div>
            <span class="circular text-2xl tracking-tighter text-white" >Welcome Back {{ Auth::user()->name }}!</span>
            <div class="flex justify-center pt-5">
                <div class="pr-5">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg
                            text-sm  text-[var(--danger-text)] bg-[var(--red-700)] hover:bg-[#ab3f3f] hover:text-white transition"
                        >
                            <i class="fa-solid fa-right-from-bracket w-5"></i>
                            <span x-show="!sidebarCollapsed" x-transition>Logout</span>
                        </button>
                    </form>
                </div>
                <div>
                    <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg
                    text-sm  text-white  bg-[#00205B] hover:bg-[#143774] transition">
                        <a href="{{ route('dashboard') }}">
                            Dashboard
                        </a>
                    </button>
                </div>
            </div>
        </div>
    @endauth

    @guest
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Username -->
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
