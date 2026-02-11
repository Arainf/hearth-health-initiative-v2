@vite(['resources/css/table.css','resources/js/page/unit.js'])

<x-app-layout>
    <div class="relative flex flex-col h-screen px-2 pt-2 overflow-hidden">
        <!-- HEADER -->
        <div class="flex flex-col border-b px-6 py-3 z-20 sticky top-0">
            <div class="flex justify-between items-center">
                <p class="circular text-lg tracking-tighter">Units</p>
                <x-filter_search id="record-search" placeholder="Search unit" width="w-80" />

                <div class="flex flex-row gap-1">
                    <x-dropdown
                        name="unit-group-filter"
                        :options="$unitGroups"
                        selected="all unit groups"
                        all-value="all"
                        all-display="all unit groups"

                        value-key="unit_group_name"
                        label-key="unit_group_name"
                        class="dropdown form-control"
                    />

                    <x-button.search_button onClick="applyPendingFilters()" />
                    <x-button.reset_button onClick="resetFilters()" />
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        {{-- <div class="flex-1 overflow-y-auto p-6">
            <h1>Testing only if the page and route is working</h1>
        </div> --}}
        <div class="flex items-end w-full px-6 py-3 border-b sticky top-[56px] z-10 justify-end">
            @can('isAdmin')
                <button
                    type="button"
                    class="hhi-btn hhi-btn-create-another">
                    Create Unit
                </button>
            @endcan
        </div>

        <!-- TABLE SECTION -->
        <div class="flex-1 overflow-y-auto p-2 w-full h-full">
            <x-table.units/>
        </div>

    </div>

    <!-- CONFIRM ACTION MODAL -->
    <div id="confirmModal"
         class="fixed inset-0 z-[99999] hidden flex items-center justify-center bg-black/40 dark:bg-black/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-[#212121] dark:border dark:border-gray-600 w-[420px] rounded-xl shadow-xl p-6">
            <h3 id="confirmTitle" class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">
                Confirm action
            </h3>
            <p id="confirmMessage" class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Are you sure?
            </p>
            <div class="flex justify-end gap-3">
                <button id="confirmCancelBtn"
                        class="px-4 py-2 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200">
                    Cancel
                </button>
                <button id="confirmOkBtn"
                        class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    window.page = {
        table : "/table/{{ $table }}"
    }
</script>