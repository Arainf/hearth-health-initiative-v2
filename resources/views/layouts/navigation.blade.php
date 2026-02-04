<nav
    :class="sidebarCollapsed ? 'w-[80px]' : 'w-full'"
    class="
        bg-[var(--clr-surface-a10)]
        border-[var(--clr-surface-a30)]
        h-full
        rounded-xl
        border
        shadow-lg
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
                class="p-2 rounded-lg hover:bg-[var(--clr-surface-a20)] transition"
                title="Toggle sidebar"
            >
                <i
                    class="fa-solid fa-chevron-right text-[var(--badge-disabled-text)] transition-transform duration-300"
                    :class="sidebarCollapsed ? 'rotate-0 pr-1' : 'rotate-180 pl-1'"
                ></i>
            </button>
        </div>

        <!-- ================= NAV LINKS ================= -->
        <div class="flex flex-col gap-2 flex-1 mt-3">


            @if (Auth::user()->is_doctor)
                <!--Doctor  -->
                <x-navigation.nav-link
                    :href="route('doctor')"
                    :active="request()->routeIs('doctor')"
                    :style="5"
                    title="Doctors"
                >
                    <div
                        class="flex items-center w-full "
                        :class="sidebarCollapsed ? 'justify-center' : ''"
                    >
                        <i class="
                            fa-solid fa-stethoscope w-5 transition-colors
                            {{ request()->routeIs('doctor')
                                 ? 'text-[var(--accent-5)]'
                                : 'text-[var(--badge-disabled-text)] group-hover:text-[var(--accent-5)]'
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
                             ? 'text-[var(--accent-1)]'
                             : 'text-[var(--badge-disabled-text)] group-hover:text-[var(--accent-1)]'
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
                            ? 'text-[var(--accent-3)]'
                            : 'text-[var(--badge-disabled-text)] group-hover:text-[var(--accent-3)]'
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
                                ? 'text-[var(--accent-4)]'
                                : 'text-[var(--badge-disabled-text)] group-hover:text-[var(--accent-4)]'
                            }}
                        "></i>

                        <span
                            x-show="!sidebarCollapsed"
                            x-transition
                            class="ml-4 text-sm font-medium "
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



        </div>

        <div x-show="!sidebarCollapsed" x-transition>
            <p class="text-xs font-semibold text-[var(--text-secondary)] uppercase mb-2 px-1">
                Quick Actions
            </p>

            <div class="grid grid-cols-2 gap-3">

                <a
                    href="{{ route('record') }}"
                    class="flex flex-col items-center justify-center p-3 rounded-lg
               bg-[var(--bg-card)] hover:bg-[var(--clr-surface-a20)] transition"
                >
                    <i class="fa-solid fa-user-plus text-[var(--accent-1)] text-lg"></i>
                    <span class="text-xs font-medium text-[var(--text-muted)] mt-1">New</span>
                </a>

                <a
                    href="{{ route('compare') }}"
                    class="flex flex-col items-center justify-center p-3 rounded-lg
               bg-[var(--bg-card)] hover:bg-[var(--clr-surface-a20)] transition"
                >
                    <i class="fa-solid fa-code-compare text-[var(--accent-2)] text-lg"></i>
                    <span class="text-xs font-medium text-[var(--text-muted)] mt-1">Compare</span>
                </a>

            </div>

        </div>

          <div x-show="sidebarCollapsed" x-transition>
              <div class="grid grid-rows-2 gap-3">

                  <a
                      href="{{ route('record') }}"
                      class="flex flex-col items-center justify-center p-2 rounded-lg
               bg-[var(--bg-card)] hover:bg-[var(--bg-card-hover)] transition"
                  >
                      <i class="fa-solid fa-user-plus text-[var(--accent-1)] text-lg"></i>
                  </a>

                  <a
                      href="{{ route('compare') }}"
                      class="flex flex-col items-center justify-center p-2 rounded-lg
               bg-[var(--bg-card)] hover:bg-[var(--bg-card-hover)] transition"
                  >
                      <i class="fa-solid fa-code-compare text-[var(--accent-2)] text-lg"></i>
                  </a>

              </div>

          </div>
    </div>


    <div class="m-5">

        <div class="flex flex-col gap-2 mb-3">

            <a
                href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg
           text-sm text-[var(--text-primary)] hover:bg-[var(--clr-surface-a20)] transition"
            >
                <i class="fa-regular fa-user w-5 text-[var(--badge-disabled-text)]"></i>
                <span x-show="!sidebarCollapsed" x-transition>Profile</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg
               text-sm  text-[var(--danger-text)] hover:bg-[var(--red-700)] transition"
                >
                    <i class="fa-solid fa-right-from-bracket w-5"></i>
                    <span x-show="!sidebarCollapsed" x-transition>Logout</span>
                </button>
            </form>


        </div>

        <div class="border-t border-[var(--clr-text-a10)] my-3"></div>

        <div class="flex items-center gap-3 overflow-hidden mt-3">
            <img
                class="w-8 h-8 rounded-full"
                src="{{ asset('img/application_logo_small.webp') }}"
                alt="user"
            >
            <div x-show="!sidebarCollapsed" x-transition class="flex flex-col leading-tight">
        <span class="text-sm font-medium truncate text-[var(--text-primary)]">
            {{ Auth::user()->name }}
        </span>
                <span class="text-xs truncate text-[var(--badge-disabled-text)]">
            {{ Auth::user()->occupation }}
        </span>
            </div>
        </div>


    </div>

</nav>
