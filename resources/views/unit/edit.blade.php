<x-app-layout>
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg mt-10">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">{{ __('Edit Unit') }}</h2>

        {{-- Use the encrypted TOKEN if needed, or a standard update route --}}
        <form method="POST" action="{{ route('update' , [ 'token' => $encryption->encrypt('unit') , 'id' => $encryption->encrypt($unit->unit_code) , 'mode' => $encryption->encrypt('update')]) }}">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="unit_name" :value="__('Unit Name')" />
                <x-text-input id="unit_name" class="block mt-1 w-full" type="text" name="unit_name"
                              :value="old('unit_name', $unit->unit_name)" required autofocus />
                <x-input-error :messages="$errors->get('unit_name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="unit_code" :value="__('Unit Code')" />
                <x-text-input id="unit_code" class="block mt-1 w-full bg-gray-100" type="text" name="unit_code"
                              :value="$unit->unit_code" readonly />
                <p class="text-xs text-gray-500 mt-1">Unit codes cannot be changed after creation.</p>
            </div>

            <div class="mt-4">
                <x-input-label for="unit_abbr" :value="__('Unit Abbreviation')" />
                <x-text-input id="unit_abbr" class="block mt-1 w-full" type="text" name="unit_abbr"
                              :value="old('unit_abbr', $unit->unit_abbr)" required />
                <x-input-error :messages="$errors->get('unit_abbr')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="unit_group_code" :value="__('Unit Group')" />
                <select id="unit_group_code" name="unit_group_code" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                    @foreach($unitGroups as $group)
                        <option value="{{ $group->unit_group_code }}"
                            {{ (old('unit_group_code', $unit->unit_group_code) == $group->unit_group_code) ? 'selected' : '' }}>
                            {{ $group->unit_group_name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('unit_group_code')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900"  href="{{ route('page' , ['token' => $encryption->encrypt('unit')]) }}">
                    {{ __('Cancel') }}
                </a>

                <x-primary-button class="ms-4">
                    {{ __('Update Unit') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
