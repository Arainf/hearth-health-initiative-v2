@vite(['resources/css/table.css','resources/js/page/archive.js'])

<x-app-layout>
    <div class=" relative flex flex-col h-screen  px-2 pt-2 overflow-hidden">
        <!-- HEADER -->
        <div class="flex flex-col bg-white border-b border-gray-200 px-4 py-3 z-20 sticky top-0 shadow-sm">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4">

                <div class="flex items-center font-inter font-semibold shrink-0 text-gray-800">
                    @php $icon = $MODULE_NAME['icon']; @endphp
                    <x-dynamic-component :component="'lucide-' . $icon" class="w-5 h-5 mr-2" />
                    <span class="text-base sm:text-lg whitespace-nowrap">
                {{ $MODULE_NAME['label'] }}
            </span>
                </div>

                <div class="flex-1 w-full max-w-xl">
                    <x-filter_search id="record-search" placeholder="Search archive" width="w-full" />
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">

                    <div class="flex items-center gap-2 flex-1 lg:flex-none">
                        <div class="w-32 shrink-0">
                            <x-dropdown
                                name="year-filter"
                                :options="$YEARS"
                                selected="{{$CURRENT}}"
                                all-value="all"
                                all-display="all years"
                                class="form-control dropdown w-full"
                            />
                        </div>

                        <div class="min-w-[180px] flex-1 lg:w-64">
                            <x-dropdown_select
                                class="form-control w-full h-full"
                                name="unit_office_template"
                                :options="$UNITS"
                                valueKey="unit_code"
                                labelKey="unit_name"
                                placeholder="Select Unit"
                            />
                        </div>
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <x-button.search_button />
                        <x-button.reset_button />
                    </div>
                </div>
            </div>
        </div>


        <!-- TABLE SECTION -->
        <div class="flex-1 overflow-y-auto p-2 w-full h-full">
            <x-table.archive/>
        </div>
    </div>

    <div id="confirmModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all animate-[fadeIn_.2s_ease-out]">
            <div class="p-6 text-center">
                <div id="modalIconContainer" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4">
                    <i id="modalIcon" data-lucide="help-circle" class="w-6 h-6"></i>
                </div>

                <h3 id="modalTitle" class="text-lg font-bold text-gray-900 mb-2">Confirmation</h3>
                <p id="modalMessage" class="text-sm text-gray-500 mb-6 px-4">Are you sure you want to proceed?</p>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="closeConfirmModal()"
                            class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="button" id="modalConfirmBtn"
                            class="flex-1 px-4 py-2 text-white rounded-lg font-medium transition-colors">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

<script>
    window.page = { token : "{{$TOKEN}}"}
</script>

