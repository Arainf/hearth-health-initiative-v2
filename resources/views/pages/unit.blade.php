@vite(['resources/css/table.css','resources/js/page/unit.js'])

<x-app-layout>
    <div class="relative flex flex-col h-full px-2 pt-2 overflow-hidden">
        <div class="flex flex-col border-b px-6 py-3 z-20 sticky top-0 bg-white">
            <div class="flex flex-row items-center gap-4 justify-between">
                <div class="flex items-center font-inter font-semibold shrink-0">
                    <x-dynamic-component :component="'lucide-' . $MODULE_NAME['icon']" class="w-5 h-5 mr-2" />
                    <span class="text-base sm:text-lg whitespace-nowrap">{{ $MODULE_NAME['label'] }}</span>
                </div>
                <div class="flex-1 max-w-xl">
                    <x-filter_search  placeholder="Search unit" width="w-full" />
                </div>
                <div class="flex flex-row items-center gap-2 shrink-0">
                    <div class="w-48">
                        <x-dropdown_select name="unit-group-filter" :options="$UNIT" valueKey="unit_group_name" labelKey="unit_group_name" placeholder="Select Group" />
                    </div>
                    <div class="flex gap-1">
                        <x-button.search_button  />
                        <x-button.reset_button  />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-end w-full px-6 py-3 border-b sticky top-[56px] z-10 justify-end">
            @can('isAdmin')
                <button type="button" id="open-create-modal" class="hhi-btn hhi-btn-create-another">
                    Create Unit <x-lucide-circle-plus class="w-4 h-4 ml-1"/>
                </button>
            @endcan
        </div>

        <div class="flex-1 overflow-y-auto p-2 w-full h-full">
            <x-table.units/>
        </div>
    </div>

    <div id="unitModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white w-full max-w-xl rounded-xl shadow-2xl p-6 overflow-hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 id="modalTitle" class="text-xl font-bold text-gray-800">Create New Unit</h2>
                <button type="button" class="closeModal text-gray-400 hover:text-gray-600"><x-lucide-x class="w-5 h-5"/></button>
            </div>

            <form id="unit-form" method="POST">
                @csrf
                <div id="method-field"></div>
                <input type="hidden" id="form-mode-enc" name="mode" value="">
                <input type="hidden" id="form-id-enc" name="id" value="">
                <input type="hidden" id="mode-plain" value="store">

                <div class="space-y-4">
                    <div>
                        <x-input-label for="unit_name" :value="__('Unit Name')" />
                        <x-text-input id="input_unit_name" name="unit_name" class="block mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="unit_abbr" :value="__('Unit Abbreviation')" />
                        <x-text-input id="input_unit_abbr" name="unit_abbr" class="block mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="unit_group_code" :value="__('Unit Group')" />
                        <select id="input_unit_group_code" name="unit_group_code" required class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Select a Group</option>
                            @foreach($UNIT as $group)
                                <option value="{{ $group->unit_group_code }}">{{ $group->unit_group_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" class="closeModal px-4 py-2 bg-gray-100 rounded-md hover:bg-gray-200">Cancel</button>
                    <x-primary-button id="submit-btn">Save Unit</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <div id="confirmModal" class="fixed inset-0 z-[99999] hidden flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#212121] w-[420px] rounded-xl shadow-xl p-6">
            <h3 id="confirmTitle" class="text-lg font-semibold mb-2">Confirm action</h3>
            <p id="confirmMessage" class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure?</p>
            <div class="flex justify-end gap-3">
                <button id="confirmCancelBtn" class="px-4 py-2 rounded-md bg-gray-100 hover:bg-gray-200">Cancel</button>
                <button id="confirmOkBtn" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">Confirm</button>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    window.page = { token : "{{$TOKEN}}" };
    window.encodings = {
        store: "{{ $encryption->encrypt('store') }}",
        update: "{{ $encryption->encrypt('update') }}",
        delete: "{{ $encryption->encrypt('delete') }}"
    };
</script>
