@php
    $currentPage = request()->route('token') ? $encryption->decrypt(request()->route('token')) : null;
@endphp

<nav :class="sidebarCollapsed ? 'sm:w-[60px] md:w-[60px] lg:w-[80px]' : 'w-full'"
     class="navigation h-full rounded-xl border shadow-lg flex flex-col justify-between transition-all duration-300">

    <div class="flex flex-col p-5 gap-5 h-full">
        <div class="flex items-center justify-between h-12">
            <div class="flex items-center overflow-hidden">
                <div x-show="!sidebarCollapsed" x-transition>
                    <x-application-logo logo="navigation"/>
                </div>
            </div>

            <button @click="sidebarCollapsed = !sidebarCollapsed;"
                    class="p-2 rounded-lg hover:bg-[var(--clr-surface-a20)] transition"
                    :class="!sidebarCollapsed ? ' ml-3' : 'ml-0'"
                    title="Toggle sidebar">
                <x-lucide-chevron-right class="w-6 h-6 text-[var(--badge-disabled-text)] transition-transform duration-300"
                                        ::class="sidebarCollapsed ? 'rotate-0 pr-1' : 'rotate-180 pl-1'"/>
            </button>
        </div>

        <div class="flex flex-col gap-2 flex-1 mt-3">
            @if (Auth::user()->is_doctor)
                <x-navigation.nav-link :href="route('page', ['token' => $encryption->encrypt('doctor')])"
                                       :active="$currentPage === 'doctor' || $currentPage === 'compare'"
                                       :style="5">
                    <div class="relative group flex items-center w-full">
                        <x-lucide-stethoscope class="w-4 flex-shrink-0 nav-icon transition-colors {{ $currentPage === 'doctor' ? 'text-[var(--accent-5)]' : 'text-[var(--badge-disabled-text)] group-hover:text-[var(--accent-5)]' }}"/>
                        <span :class="!sidebarCollapsed ? '' : 'text-transparent'"
                              class="ml-4 text-sm font-medium transition-all ease-in duration-150">
                            Doctor
                        </span>

                        <div x-show="sidebarCollapsed" x-transition.opacity
                             class="absolute left-full top-1/2 -translate-y-1/2 ml-[18px] pointer-events-none z-[100]">
                            <div class="flex items-center floating-nav  text-xs font-medium rounded-lg origin-left scale-x-0 opacity-0 group-hover:scale-x-100 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300 ease-[cubic-bezier(.4,0,.2,1)] px-4 h-11 whitespace-nowrap shadow-xl">
                                Doctor
                            </div>
                        </div>
                    </div>
                </x-navigation.nav-link>
            @endif

            <x-navigation.nav-link :href="route('page', ['token' => $encryption->encrypt('dashboard')])"
                                   :active="$currentPage === 'dashboard'"
                                   :style="1">
                <div class="relative group flex items-center w-full">
                    <x-lucide-folder-open class="w-4 flex-shrink-0 nav-icon transition-colors {{ $currentPage === 'dashboard' ? 'text-[var(--accent-1)]' : 'text-[var(--badge-disabled-text)] group-hover:text-[var(--accent-1)]' }}"/>
                    <span :class="!sidebarCollapsed ? '' : 'text-transparent'"
                          class="ml-4 text-sm font-medium transition-all ease-in duration-150">
                        Records
                    </span>
                    <div x-show="sidebarCollapsed" x-transition.opacity
                         class="absolute left-full top-1/2 -translate-y-1/2 ml-[18px] pointer-events-none z-[100]">
                        <div class="flex items-center floating-nav  text-xs font-medium rounded-lg origin-left scale-x-0 opacity-0 group-hover:scale-x-100 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300 ease-[cubic-bezier(.4,0,.2,1)] px-4 h-11 whitespace-nowrap shadow-xl">
                            Records
                        </div>
                    </div>
                </div>
            </x-navigation.nav-link>

            <x-navigation.nav-link :href="route('page', ['token' => $encryption->encrypt('patient')])"
                                   :active="$currentPage === 'patient'"
                                   :style="3">
                <div class="relative group flex items-center w-full">
                    <x-lucide-users class="w-4 flex-shrink-0 nav-icon transition-colors {{ $currentPage === 'patient' ? 'text-[var(--accent-3)]' : 'text-[var(--badge-disabled-text)] group-hover:text-[var(--accent-3)]' }}"/>
                    <span :class="!sidebarCollapsed ? '' : 'text-transparent'"
                          class="ml-4 text-sm font-medium transition-all ease-in duration-150">
                        Patients
                    </span>
                    <div x-show="sidebarCollapsed" x-transition.opacity
                         class="absolute left-full top-1/2 -translate-y-1/2 ml-[18px] pointer-events-none z-[100]">
                        <div class="flex items-center floating-nav text-xs font-medium rounded-lg origin-left scale-x-0 opacity-0 group-hover:scale-x-100 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300 ease-[cubic-bezier(.4,0,.2,1)] px-4 h-11 whitespace-nowrap shadow-xl">
                            Patients
                        </div>
                    </div>
                </div>
            </x-navigation.nav-link>

            @if (Auth::user()->is_admin)
                <x-navigation.nav-link :href="route('page', ['token' => $encryption->encrypt('account')])"
                                       :active="$currentPage === 'account'"
                                       :style="4">
                    <div class="relative group flex items-center w-full">
                        <x-lucide-shield-user class="w-4 flex-shrink-0 nav-icon transition-colors {{ $currentPage === 'account' ? 'text-[var(--accent-4)]' : 'text-[var(--badge-disabled-text)] group-hover:text-[var(--accent-4)]' }}"/>
                        <span :class="!sidebarCollapsed ? '' : 'text-transparent'"
                              class="ml-4 text-sm font-medium transition-all ease-in duration-150">
                            Accounts
                        </span>
                        <div x-show="sidebarCollapsed" x-transition.opacity
                             class="absolute left-full top-1/2 -translate-y-1/2 ml-[18px] pointer-events-none z-[100]">
                            <div class="flex items-center floating-nav  text-xs font-medium rounded-lg origin-left scale-x-0 opacity-0 group-hover:scale-x-100 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300 ease-[cubic-bezier(.4,0,.2,1)] px-4 h-11 whitespace-nowrap shadow-xl">
                                Accounts
                            </div>
                        </div>
                    </div>
                </x-navigation.nav-link>

                <x-navigation.nav-link :href="route('page', ['token' => $encryption->encrypt('archive')])"
                                       :active="$currentPage === 'archive'"
                                       :style="2">
                    <div class="relative group flex items-center w-full">
                        <x-lucide-package class="w-4 flex-shrink-0 nav-icon transition-colors {{ $currentPage === 'archive' ? 'text-purple-600' : 'text-[var(--badge-disabled-text)] group-hover:text-purple-600' }}"/>
                        <span :class="!sidebarCollapsed ? '' : 'text-transparent'"
                              class="ml-4 text-sm font-medium transition-all ease-in duration-150">
                            Archive
                        </span>
                        <div x-show="sidebarCollapsed" x-transition.opacity
                             class="absolute left-full top-1/2 -translate-y-1/2 ml-[18px] pointer-events-none z-[100]">
                            <div class="flex items-center floating-nav text-xs font-medium rounded-lg origin-left scale-x-0 opacity-0 group-hover:scale-x-100 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300 ease-[cubic-bezier(.4,0,.2,1)] px-4 h-11 whitespace-nowrap shadow-xl">
                                Archive
                            </div>
                        </div>
                    </div>
                </x-navigation.nav-link>

                <x-navigation.nav-link :href="route('page', ['token' => $encryption->encrypt('unit')])"
                                       :active="$currentPage === 'unit'"
                                       :style="3">
                    <div class="relative group flex items-center w-full">
                        <x-lucide-building-2 class="w-4 flex-shrink-0 nav-icon transition-colors {{ $currentPage === 'unit' ? 'text-[var(--accent-6)]' : 'text-[var(--badge-disabled-text)] group-hover:text-[var(--accent-6)]' }}"/>
                        <span :class="!sidebarCollapsed ? '' : 'text-transparent'"
                              class="ml-4 text-sm font-medium transition-all ease-in duration-150">
                            Units
                        </span>
                        <div x-show="sidebarCollapsed" x-transition.opacity
                             class="absolute left-full top-1/2 -translate-y-1/2 ml-[18px] pointer-events-none z-[100]">
                            <div class="flex items-center floating-nav text-xs font-medium rounded-lg origin-left scale-x-0 opacity-0 group-hover:scale-x-100 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300 ease-[cubic-bezier(.4,0,.2,1)] px-4 h-11 whitespace-nowrap shadow-xl">
                                Units
                            </div>
                        </div>
                    </div>
                </x-navigation.nav-link>
            @endif
        </div>

        <div class="bg-[var(--clr-surface-a20)] p-2 rounded-lg" x-show="!sidebarCollapsed" x-transition:enter.delay.225ms>
            <p class="text-xs font-semibold text-[var(--text-secondary)] uppercase mb-2 px-1">Quick Actions</p>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('page', ['token' => $encryption->encrypt('record')]) }}"
                   class="flex flex-col items-center justify-center p-3 rounded-lg bg-[var(--bg-card)] hover:bg-[var(--clr-surface-a20)] transition">
                    <x-lucide-user-plus class="w-5 h-5 text-[var(--accent-1)]"/>
                    <span class="text-xs font-medium text-[var(--text-muted)] mt-1">New</span>
                </a>
                <a href="{{ route('page', ['token' => $encryption->encrypt('compare')]) }}"
                   class="flex flex-col items-center justify-center p-3 rounded-lg bg-[var(--bg-card)] hover:bg-[var(--clr-surface-a20)] transition">
                    <x-lucide-component class="w-5 h-5 text-[var(--accent-2)]"/>
                    <span class="text-xs font-medium text-[var(--text-muted)] mt-1">Compare</span>
                </a>
            </div>
        </div>

        <div x-show="sidebarCollapsed" x-transition:enter.delay.225ms class="flex flex-col gap-2 justify-center">
            <a href="{{ route('page', ['token' => $encryption->encrypt('record')]) }}"
               class="relative group flex items-center justify-center bg-[var(--clr-surface-a20)] p-2 rounded-lg hover:rounded-tr-none hover:rounded-br-none  hover:z-[100] transition-all duration-300" style="z-index: 10">
                <x-lucide-user-plus class="w-5 h-5 text-[var(--accent-1)] z-10"/>
                <div class="absolute left-full ml-[-4] overflow-hidden pointer-events-none" style="z-index: 10">
                    <div class="flex items-center bg-[var(--clr-surface-a20)]  text-xs font-medium rounded-tr-lg rounded-br-lg px-0 opacity-0 h-9 group-hover:w-auto group-hover:px-4 group-hover:opacity-100 transition-all duration-300 whitespace-nowrap shadow-xl">
                        New Record
                    </div>
                </div>
            </a>
            <a href="{{ route('page', ['token' => $encryption->encrypt('compare')]) }}"
               class="relative group flex items-center justify-center bg-[var(--clr-surface-a20)] p-2 rounded-lg hover:rounded-tr-none hover:rounded-br-none  hover:z-[100] transition-all duration-300" style="z-index: 10">
                <x-lucide-component class="w-5 h-5 text-[var(--accent-2)] z-10" />
                <div class="absolute left-full ml-[-4] overflow-hidden pointer-events-none" >
                    <div class="flex items-center bg-[var(--clr-surface-a20)]  text-xs font-medium rounded-tr-lg rounded-br-lg px-0 opacity-0 h-9 group-hover:w-auto group-hover:px-4 group-hover:opacity-100 transition-all duration-300 whitespace-nowrap shadow-xl">
                        Compare
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="m-5">
        <div class="flex flex-col gap-2 mb-3">
            <a href="{{ route('page' , [ 'token' => $encryption->encrypt('profile')]) }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-[var(--text-primary)] hover:bg-[var(--clr-surface-a20)] transition">
                <x-lucide-user class="w-5 text-[var(--badge-disabled-text)]"/>
                <span x-show="!sidebarCollapsed" x-transition>Profile</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-[var(--danger-text)] hover:bg-[var(--red-700)] transition">
                    <x-lucide-log-out class="w-5"/>
                    <span x-show="!sidebarCollapsed" x-transition>Logout</span>
                </button>
            </form>
        </div>

        <div class="border-t border-[var(--clr-text-a10)] my-3"></div>

        <div class="flex items-center gap-3 mt-3 relative group">
            <img class="w-8 h-8 rounded-full" src="{{ asset('img/application_logo_small.webp') }}" alt="user">
            <div x-show="!sidebarCollapsed" x-transition class="flex flex-col leading-tight overflow-hidden">
                <span class="text-sm font-medium truncate text-[var(--text-primary)]">{{ Auth::user()->name }}</span>
                <span class="text-xs truncate text-[var(--badge-disabled-text)]">{{ Auth::user()->occupation }}</span>
            </div>

            <div x-show="sidebarCollapsed" class="absolute left-full top-1/2 -translate-y-1/2 ml-[18px] pointer-events-none z-[100]">
                <div class="flex flex-col justify-center bg-[var(--clr-surface-a20)]  rounded-lg origin-left scale-x-0 opacity-0 group-hover:scale-x-100 group-hover:opacity-100 transition-all duration-300 px-4 h-12 shadow-xl whitespace-nowrap">
                    <span class="text-xs font-bold">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] opacity-70">{{ Auth::user()->occupation }}</span>
                </div>
            </div>
        </div>
    </div>
</nav>
