<nav
    :class="sidebarCollapsed ? 'w-[80px]' : 'w-full'"
    class="
        h-full
        rounded-xl
        border
        bg-[#FCFDFE]
        shadow-lg
        border-gray-100
        flex
        flex-col
        justify-between
        transition-all
        duration-300
        overflow-hidden
    "
>

    <!-- ================= TOP ================= -->
    <div class="flex flex-col p-5 gap-5 h-full">

        <!-- LOGO + TOGGLE -->
        <div class="flex items-center justify-between h-12">

            <!-- LOGO -->
            <div class="flex items-center overflow-hidden">
                <div x-show="!sidebarCollapsed" x-transition>
                    <x-application-logo logo="navigation" />
                </div>
            </div>

            <!-- TOGGLE -->
            <button
                @click="sidebarCollapsed = !sidebarCollapsed
                  setTimeout(() => {
                    window.table?.columns.adjust();
                }, 320);
                "
                class="p-2 rounded-lg hover:bg-gray-100 transition"
                title="Toggle sidebar"
            >
                <i
                    class="fa-solid fa-chevron-right text-gray-500 transition-transform duration-300"
                    :class="sidebarCollapsed ? 'rotate-0 pr-2' : 'rotate-180 pl-2'"
                ></i>
            </button>
        </div>

        <!-- ================= NAV LINKS ================= -->
        <div class="flex flex-col gap-2 flex-1 mt-3">

            <!-- Records -->
            <x-navigation.nav-link
                :href="route('dashboard')"
                :active="request()->routeIs('dashboard') || request()->routeIs('compare')"
                :style="1"
                title="Records"
            >
                <div
                    class="flex items-center w-full"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                >
                    <i class="
                        fa-regular fa-folder-open w-5 transition-colors
                        {{ request()->routeIs('dashboard') || request()->routeIs('compare')
                            ? 'text-[#F1AE00]'
                            : 'text-gray-400 group-hover:text-[#F1AE00]'
                        }}
                    "></i>

                    <span
                        x-show="!sidebarCollapsed"
                        x-transition
                        class="ml-4 text-sm font-medium"
                    >
                        Records
                    </span>
                </div>
            </x-navigation.nav-link>

            <!-- change it to archive records -->
            <!-- Compare
            <x-navigation.nav-link
                :href="route('compare')"
                :active="request()->routeIs('compare')"
                :style="2"
                title="Compare Records"
            >
                <div
                    class="flex items-center w-full"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                >
                    <i class="
                        fa-solid fa-code-compare w-5 transition-colors
                        {{ request()->routeIs('compare')
                            ? 'text-indigo-600'
                            : 'text-gray-400 group-hover:text-indigo-600'
                        }}
                    "></i>

                    <span
                        x-show="!sidebarCollapsed"
                        x-transition
                        class="ml-4 text-sm font-medium  overflow-hidden text-nowrap"
                    >
                        Compare Records
                    </span>
                </div>
            </x-navigation.nav-link> -->

            <!-- Patients -->
            <x-navigation.nav-link
                :href="route('patient')"
                :active="request()->routeIs('patient') || request()->routeIs('patientFiles')"
                :style="3"
                title="Patients"
            >
                <div
                    class="flex items-center w-full"
                    :class="sidebarCollapsed ? 'justify-center' : ''"
                >
                    <i class="
                        fa-solid fa-user-group w-5 transition-colors
                        {{ request()->routeIs('patient') || request()->routeIs('patientFiles')
                            ? 'text-green-600'
                            : 'text-gray-400 group-hover:text-green-600'
                        }}
                    "></i>

                    <span
                        x-show="!sidebarCollapsed"
                        x-transition
                        class="ml-4 text-sm font-medium"
                    >
                        Patients
                    </span>
                </div>
            </x-navigation.nav-link>

            <!-- Accounts -->
            @if (Auth::user()->is_admin)
                <x-navigation.nav-link
                    :href="route('account')"
                    :active="request()->routeIs('account')"
                    :style="4"
                    title="Accounts"
                >
                    <div
                        class="flex items-center w-full"
                        :class="sidebarCollapsed ? 'justify-center' : ''"
                    >
                        <i class="
                            fa-solid fa-user-shield w-5 transition-colors
                            {{ request()->routeIs('account')
                                ? 'text-red-600'
                                : 'text-gray-400 group-hover:text-red-600'
                            }}
                        "></i>

                        <span
                            x-show="!sidebarCollapsed"
                            x-transition
                            class="ml-4 text-sm font-medium"
                        >
                            Accounts
                        </span>
                    </div>
                </x-navigation.nav-link>

                <!-- Archive Records -->
                <x-navigation.nav-link
                    :href="route('archive')"
                    :active="request()->routeIs('archive')"
                    :style="2"
                    title="Archive Records"
                >
                    <div
                        class="flex items-center w-full"
                        :class="sidebarCollapsed ? 'justify-center' : ''"
                    >
                        <i class="
                            fa-solid fa-box-archive w-5 transition-colors
                            {{ request()->routeIs('archive')
                                ? 'text-purple-600'
                                : 'text-gray-400 group-hover:text-purple-600'
                            }}
                        "></i>

                        <span
                            x-show="!sidebarCollapsed"
                            x-transition
                            class="ml-4 text-sm font-medium"
                        >
                            Archive
                        </span>
                    </div>
                </x-navigation.nav-link>
            @endif


            @if (Auth::user()->is_doctor)
                <!--Doctor  -->
                <x-navigation.nav-link
                    :href="route('doctor')"
                    :active="request()->routeIs('doctor') "
                    :style="5"
                    title="Doctors"
                >
                    <div
                        class="flex items-center w-full"
                        :class="sidebarCollapsed ? 'justify-center' : ''"
                    >
                        <i class="
                            fa-solid fa-stethoscope w-5 transition-colors
                            {{ request()->routeIs('doctor')
                                ? 'text-cyan-700'
                                : 'text-gray-400 group-hover:text-cyan-700'
                            }}
                        "></i>

                        <span
                            x-show="!sidebarCollapsed"
                            x-transition
                            class="ml-4 text-sm font-medium"
                        >
                            Doctor
                        </span>
                    </div>
                </x-navigation.nav-link>
            @endif
        </div>

        <!-- ================= QUICK ACTIONS ================= -->
        <div x-show="!sidebarCollapsed" x-transition>
            <p class="text-xs font-semibold text-gray-400 uppercase mb-2 px-1">
                Quick Actions
            </p>

            <div class="grid grid-cols-2 gap-3">

                <a
                    href="{{ route('form') }}"
                    class="flex flex-col items-center justify-center p-3 rounded-lg bg-[#F1AE00]/10 hover:bg-[#F1AE00]/20 transition"
                >
                    <i class="fa-solid fa-user-plus text-[#F1AE00] text-lg"></i>
                    <span class="text-xs font-medium text-gray-800 mt-1">New</span>
                </a>

                <a
                    href="{{ route('compare') }}"
                    class="flex flex-col items-center justify-center p-3 rounded-lg bg-[#00205B]/10 hover:bg-[#00205B]/20 transition"
                >
                    <i class="fa-solid fa-code-compare text-[#00205B] text-lg"></i>
                    <span class="text-xs font-medium text-gray-800 mt-1">Compare</span>
                </a>

            </div>
        </div>

          <div x-show="sidebarCollapsed" x-transition>
            <div class="grid grid-rows-2 gap-3">

                <a
                    href="{{ route('form') }}"
                    class="flex flex-col items-center justify-center p-2 rounded-lg bg-[#F1AE00]/10 hover:bg-[#F1AE00]/20 transition"
                >
                    <i class="fa-solid fa-user-plus text-[#F1AE00] text-lg"></i>
                </a>

                <a
                    href="{{ route('compare') }}"
                    class="flex flex-col items-center justify-center p-2 rounded-lg bg-[#00205B]/10 hover:bg-[#00205B]/20 transition"
                >
                    <i class="fa-solid fa-code-compare text-[#00205B] text-lg"></i>
                </a>

            </div>
        </div>

    </div>

    <!-- ================= BOTTOM ================= -->
    <div class="m-5">

        <div class="flex flex-col gap-2 mb-3">

            <a
                href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100 transition"
            >
                <i class="fa-regular fa-user w-5 text-gray-400"></i>
                <span x-show="!sidebarCollapsed" x-transition>Profile</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50 transition"
                >
                    <i class="fa-solid fa-right-from-bracket w-5"></i>
                    <span x-show="!sidebarCollapsed" x-transition>Logout</span>
                </button>
            </form>

        </div>

        <div class="border-t border-gray-200 my-3"></div>

        <div class="flex items-center gap-3 overflow-hidden mt-3">
            <img
                class="w-8 h-8 rounded-full"
                src="{{ asset('img/application_logo_small.webp') }}"
                alt="user"
            >

            <div x-show="!sidebarCollapsed" x-transition class="flex flex-col leading-tight">
                <span class="text-sm font-medium truncate">
                    {{ Auth::user()->name }}
                </span>
                <span class="text-xs text-gray-500 truncate">
                    {{ Auth::user()->occupation }}
                </span>
            </div>
        </div>

    </div>

</nav>
