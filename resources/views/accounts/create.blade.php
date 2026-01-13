@if(session('account_created'))
    <script>
        window.accountCreated = true;
    </script>
@endif


@vite(['resources/js/account-create.js'])


<style>
    .card {
        background: #ffffff;
        border: 1px solid #e6e7eb;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        border-radius: 12px;
    }

    .summary-card {
        background: linear-gradient(180deg,#ffffff,#fbfdff);
        border: 1px solid #e6e7eb;
        padding: 14px;
        border-radius: 10px;
    }

    .muted { color: #6b7280; }

    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid rgba(0,0,0,0.12);
        border-top-color: rgba(59,130,246,1);
        border-radius: 50%;
        animation: spin .9s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .hidden { display: none !important; }
    .small { font-size: 13px; }
    .ghost-btn {
        background: transparent;
        border: 1px solid #e6e7eb;
        padding: 6px 10px;
        border-radius: 8px;
        cursor: pointer;
    }
</style>

<x-app-layout>
    <div class="max-w-2xl mx-auto px-6 py-6">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Create Account</h1>

            <x-secondary-button onclick="window.location='{{ route('account') }}'">
                Back
            </x-secondary-button>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('accounts.store') }}"
              class="bg-white rounded-xl shadow p-6 space-y-4">
            @csrf

            {{-- Name --}}
            <div>
                <x-input-label for="name" value="Full Name"/>
                <x-text-input id="name" name="name" class="mt-1 w-full"
                              value="{{ old('name') }}" required/>
                <x-input-error :messages="$errors->get('name')" />
            </div>

            {{-- Email --}}
            <div>
                <x-input-label for="username" value="Username"/>
                <x-text-input
                    id="username"
                    name="username"
                    class="mt-1 w-full"
                    value="{{ old('username') }}"
                    required
                />
                <x-input-error :messages="$errors->get('username')" />
            </div>


            {{-- Password --}}
            <div>
                <x-input-label for="password" value="Password"/>
                <x-text-input id="password" name="password"
                              type="password"
                              class="mt-1 w-full" required/>
                <x-input-error :messages="$errors->get('password')" />
            </div>

            {{-- Confirm Password --}}
            <div>
                <x-input-label for="password_confirmation" value="Confirm Password"/>
                <x-text-input id="password_confirmation"
                              name="password_confirmation"
                              type="password"
                              class="mt-1 w-full" required/>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-6 border-t">
                <x-secondary-button type="button"
                                    onclick="window.location='{{ route('account') }}'">
                    Cancel
                </x-secondary-button>

                <x-secondary-button type="submit" id="createAccountBtn">
                    Create Account
                </x-secondary-button>

            </div>
        </form>

    </div>

    <!-- LOADING MODAL -->
    <div id="loadingModal"
         class="fixed inset-0 z-50 bg-black/40 backdrop-blur-[1px] hidden flex items-center justify-center">

        <div class="bg-white w-[360px] rounded-xl p-6 flex flex-col items-center text-center shadow-lg">

            <!-- Spinner -->
            <div class="w-10 h-10 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin mb-4"></div>

            <!-- Text -->
            <h3 class="text-lg font-semibold text-gray-800">Saving Record</h3>
            <p class="text-sm text-gray-600 mt-1">
                Please wait while we process the data…
            </p>

        </div>
    </div>

    <!-- SUCCESS MODAL -->
    <div id="successModal"
         class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 backdrop-blur-sm">

        <div class="bg-white w-[420px] rounded-xl shadow-xl p-6">
            <h3 class="text-lg font-semibold mb-2 text-gray-900">
                Account Created
            </h3>

            <p class="text-sm text-gray-600 mb-6">
                The account has been successfully created.
            </p>

            <div class="flex justify-end gap-3">
                <x-secondary-button id="createAnotherBtn">
                    Create Another
                </x-secondary-button>

                <x-secondary-button
                    onclick="window.location='{{ route('account') }}'">
                    Back to Accounts
                </x-secondary-button>
            </div>
        </div>
    </div>


</x-app-layout>
