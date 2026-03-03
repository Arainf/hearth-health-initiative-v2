<x-app-layout>
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg mt-10 border border-gray-100">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ __('Create New Unit') }}</h2>
            <p class="text-sm text-gray-500">The system will automatically generate a unique Unit Code upon saving.</p>
        </div>

        <form method="POST" action="{{ route('store' , [ 'token' => $encryption->encrypt('unit') , 'mode' => $encryption->encrypt('store')])  }}">
            @csrf
            {{-- Pass the store mode so your menu() function knows to save --}}
            <input type="hidden" name="mode" value="{{ $encryption->encrypt('store') }}">

            <div class="mb-4">
                <x-input-label for="unit_name" :value="__('Unit Name')" />
                <x-text-input id="unit_name" class="block mt-1 w-full" type="text" name="unit_name" :value="old('unit_name')" required autofocus placeholder="e.g., Cardiology Department" />
                <x-input-error :messages="$errors->get('unit_name')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="unit_abbr" :value="__('Unit Abbreviation')" />
                <x-text-input id="unit_abbr" class="block mt-1 w-full" type="text" name="unit_abbr" :value="old('unit_abbr')" required placeholder="e.g., CARD" />
                <p class="text-[10px] text-indigo-600 mt-1 uppercase font-semibold tracking-wider">Used to generate the first 4 characters of the ID</p>
                <x-input-error :messages="$errors->get('unit_abbr')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="unit_group_code" :value="__('Unit Group')" />

                <select id="unit_group_code" name="unit_group_code" required class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                    <option value="" disabled selected>{{ __('Select a Group') }}</option>
                    @foreach($unitGroups as $group)
                        <option value="{{ $group->unit_group_code }}" {{ old('unit_group_code') == $group->unit_group_code ? 'selected' : '' }}>
                            {{ $group->unit_group_name }} ({{ $group->unit_group_code }})
                        </option>
                    @endforeach
                </select>

                <x-input-error :messages="$errors->get('unit_group_code')" class="mt-2" />
            </div>

            <hr class="my-6 border-gray-100">

            <div class="flex items-center justify-end">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none" href="{{ route('page' , ['token' => $encryption->encrypt('unit')]) }}">
                    {{ __('Cancel and Go Back') }}
                </a>

                <x-primary-button class="ms-4">
                    {{ __('Save Unit') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
