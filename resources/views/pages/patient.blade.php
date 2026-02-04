@vite(['resources/css/table.css','resources/js/page/patients.js'])

<x-app-layout>

    <div class=" relative flex flex-col h-full w-full rounded-xl bg-[#FCFDFE] px-2 pt-2  border-2 shadow-xl border-gray-100">
        <!-- HEADER -->
        <div class="flex flex-row bg-white px-6 py-3 z-20 sticky top-0 justify-between">
            <div class="flex items-center">
                <p class="circular text-lg tracking-tighter">Patients</p>
            </div>

            <x-filter_search id="record-search" placeholder="Search Patient" width="w-80" />

            <div class="flex flex-row gap-1">
                <x-filters />

                <x-button.search_button onClick="applyPendingFilters()" />
                <x-button.reset_button onClick="resetFilters()" />
            </div>

        </div>


        <!-- TABLE SECTION -->
        <div
            id="table-wrapper"
            class=" flex-1 w-full h-full py-2 overflow-x-auto md:overflow-x-hidden"
        >

            <x-table.patients/>
        </div>

        <div
                id="tableLoading"
                class="
                    absolute inset-0
                    z-30
                    bg-white/70
                    h-full w-full
                    backdrop-blur-[1px]
                    hidden
                    flex items-center justify-center
                    pointer-events-none
                "
            >
                <div class="flex flex-col items-center gap-3">
                    <div class="w-8 h-8 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>
                    <span class="text-sm text-gray-600 font-medium">
                        Loading records…
                    </span>
                </div>
            </div>

    </div>



</x-app-layout>

<!-- DELETE CONFIRM MODAL -->
<div id="deleteModal"
     class="fixed inset-0 z-[100000] hidden flex items-center justify-center bg-black/50">

    <div class="bg-white w-[420px] rounded-xl shadow-xl p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">
            Delete Patient
        </h3>

        <p class="text-sm text-gray-600 mb-6">
            Are you sure you want to delete this patient?
            <br><br>
            <span class="text-orange-600 font-medium">
                ⚠️ All records associated with this patient will be moved to Archive Records.
            </span>
            <br><br>
            <span class="text-red-600 font-medium">This action cannot be undone.</span>
        </p>

        <div class="flex justify-end gap-3">
            <button id="cancelDelete"
                    class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                Cancel
            </button>

            <button id="confirmDelete"
                    class="px-4 py-2 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                Delete
            </button>
        </div>
    </div>
</div>


<script>
    window.page = {
        table : "/table/{{$table}}"
    }
</script>

