@vite(['resources/css/table.css','resources/js/page/dashboard.js'])

<x-app-layout>
    <div class=" relative flex flex-col h-full bg-[#f9fbfc] px-2 pt-2 overflow-hidden">
        <div class="flex flex-col border-b px-4 sm:px-6 py-3 z-20 sticky top-0 ">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                <!-- LEFT: ICON + TITLE -->
                <div class="flex items-center font-inter font-semibold shrink-0">
                    @php
                        $icon = $MODULE_NAME['icon'];
                    @endphp

                    <x-dynamic-component
                        :component="'lucide-' . $icon"
                        class="w-5 h-5 mr-2"
                    />

                    <span class="text-base sm:text-lg">
                        {{ $MODULE_NAME['label'] }}
                    </span>
                </div>

                <div class="flex flex-row gap-2">
                    <x-button id="btnOpenTemplate">
                        <x-lucide-layout-template class="w-4 h-4"/> Get Template
                    </x-button>
                    <x-button id="btnOpenImport">
                        <x-lucide-download class="w-4 h-4"/> Upload Csv
                    </x-button>

                    <div id="modalTemplate" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm">
                        <div class="bg-white w-[500px] rounded-xl shadow-xl p-6 relative">
                            <button type="button" class="close-modal absolute top-3 right-3 text-gray-500 hover:text-gray-700">✕</button>
                            <h2 class="text-lg font-semibold mb-4">Download Template</h2>
                            <p class="text-sm text-gray-600 mb-6">Download the CSV template file to ensure proper formatting. Also select a unit/office.</p>

                            <x-dropdown_select label="Unit/Office" class="form-control w-full col-span-2" name="unit_office_template" :options="$UNITS" valueKey="unit_code" labelKey="unit_name" placeholder="Select Unit">
                                <x-slot:iconSlot><x-lucide-building-2 class="w-4 h-4"/></x-slot:iconSlot>
                            </x-dropdown_select>

                            <div class="flex justify-end gap-3 mt-3">
                                <x-button type="button" class="close-modal">Cancel</x-button>
                                <x-button id="export_template" data-mode="{{ $encryption->encrypt('export') }}">
                                    <x-lucide-download class="w-4 h-4"/> Download Template
                                </x-button>
                            </div>
                        </div>
                    </div>

                    <div id="modalImport" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm">
                        <div id="importModalContainer" class="bg-white w-[520px] rounded-xl shadow-xl p-6 relative transition-all duration-300 ease-in-out">
                            <button type="button" class="close-modal absolute top-3 right-3 text-gray-500 hover:text-gray-700">✕</button>
                            <h2 class="text-lg font-semibold mb-2">Import Patient Records</h2>
                            <p class="text-sm text-gray-600 mb-4">Upload the official system-generated template (.xlsx only).</p>

                            <div id="import_step_1">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Excel File (.xlsx only)</label>
                                    <input type="file" id="import_file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" class="w-full border rounded-lg p-2 text-sm">
                                </div>
                                <div class="flex justify-end gap-3 mt-4">
                                    <x-button type="button" class="close-modal">Cancel</x-button>
                                    <x-button id="validate_import" data-mode="{{ $encryption->encrypt('validate') }}" class="bg-blue-600 text-white">Validate File</x-button>
                                </div>
                            </div>

                            <!-- STEP 2 -->

                            <div id="import_step_2" class="hidden">
                                <div class="bg-gray-50 border rounded-lg p-4 text-sm">
                                    <p class="mb-2 font-semibold">Import Summary</p>
                                    <p>
                                        Valid Rows:
                                        <span id="valid_rows" class="text-green-600 font-medium"></span>
                                    </p>
                                    <p>
                                        Invalid Rows:
                                        <span id="invalid_rows" class="text-red-600 font-medium"></span>
                                    </p>
                                </div>
                                <div class="flex-1 mt-4 border rounded-lg h-[58vh] overflow-y-scroll">
                                    <table class="min-w-full text-xs border-collapse h-full">
                                        <thead class="bg-gray-100 sticky top-0 z-10">
                                        <tr>
                                            <th class="px-2 py-2 border">Row</th>
                                            <th class="px-2 py-2 border">Full Name</th>
                                            <th class="px-2 py-2 border">Birthday</th>
                                            <th class="px-2 py-2 border">Sex</th>
                                            <th class="px-2 py-2 border">Weight (KG)</th>
                                            <th class="px-2 py-2 border">Height (CM)</th>
                                            <th class="px-2 py-2 border">Phone Number</th>
                                            <!-- Family History -->
                                            <th class="px-2 py-2 border">Hypertension</th>
                                            <th class="px-2 py-2 border">Diabetes Mellitus</th>
                                            <th class="px-2 py-2 border">Heart Attack under 60y</th>
                                            <th class="px-2 py-2 border">Cholesterol</th>
                                            <!-- Risk Factors -->
                                            <th class="px-2 py-2 border">Total Cholesterol (mg/dl)</th>
                                            <th class="px-2 py-2 border">HDL Cholesterol (mg/dl)</th>
                                            <th class="px-2 py-2 border">Systolic BP (mmHg)</th>
                                            <th class="px-2 py-2 border">FBS (mg/dl)</th>
                                            <th class="px-2 py-2 border">HbA1c (%)</th>
                                            <th class="px-2 py-2 border">Hypertension Tx</th>
                                            <th class="px-2 py-2 border">Diabetes M</th>
                                            <th class="px-2 py-2 border">Current Smoker</th>
                                            <th class="px-2 py-2 border">Date Record</th>
                                        </tr>
                                        </thead>
                                        <tbody id="preview_table_body" class="bg-white">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="flex justify-between mt-4">
                                    <x-button id="back_to_upload">
                                        Back
                                    </x-button>
                                    <x-button id="confirm_import"
                                              data-mode="{{ $encryption->encrypt('confirm') }}"
                                              class="bg-green-600 text-white">
                                        Confirm & Save
                                    </x-button>
                                </div>
                            </div>
                            <div id="import_loading" class="hidden text-center text-sm text-gray-500 mt-4">Processing...</div>
                        </div>
                    </div>
                </div>


            </div>


        </div>

        <div class="flex flex-row justify-between gap-4 mx-4 mb-4 pb-4 pt-4 border-b border-border ">

                <x-filter_search
                    id="record-search"
                    placeholder="Search record"
                    width="w-64"
                />

            <div class="flex items-center gap-3 w-full max-w-3xl">

                <!-- UNIT (grows most) -->
                <div class="flex-[3] min-w-[180px]">
                    <x-dropdown_select
                        name="unit_office"
                        :options="$UNITS"
                        valueKey="unit_name"
                        labelKey="unit_name"
                        placeholder="Select Unit"
                        class="w-full"
                    />
                </div>

                <!-- YEAR -->
                <div class="flex-[1] min-w-[110px]">
                    <x-dropdown
                        name="year-filter"
                        :options="$YEARS"
                        selected="{{$CURRENT}}"
                        all-value="all"
                        all-display="all years"
                        class="w-full dropdown"
                    />
                </div>

                <!-- STATUS -->
                <div class="flex-[1] min-w-[130px]">
                    <x-dropdown
                        name="status-filter"
                        :options="$STATUS"
                        selected="all status"
                        all-value="all"
                        all-display="all status"
                        value-key="id"
                        label-key="status_name"
                        count-key="count"
                        class="w-full dropdown"
                    />
                </div>

                <!-- BUTTONS -->
                <div class="flex items-center gap-2 shrink-0">
                    <x-button.search_button class="h-9 px-3 text-xs" />
                    <x-button.reset_button class="h-9 px-3 text-xs" />
                </div>

            </div>




        </div>


        <!-- TABLE SECTION -->
        <div class="flex-1 overflow-y-auto p-2 w-full h-full">
            <x-table.records/>
        </div>
            <!-- LOADING MODAL -->
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



    <!-- VIEW GENERATED TEXT MODAL -->
    <div
        id="reportModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 backdrop-blur-sm"
    >
        <div
            class="
                bg-white
                w-full max-w-3xl
                max-h-[85vh]
                rounded-xl
                shadow-2xl
                flex flex-col
                animate-[fadeIn_.15s_ease-out]
            "
        >
            <!-- MODAL HEADER -->
            <div class="flex items-center justify-between px-5 py-3 border-b">
                <h3 class="text-sm font-semibold text-gray-800">
                    Generated Report
                </h3>

                <button
                    id="closeModal"
                    class="
                        w-8 h-8
                        hhi-btn-delete
                        flex items-center justify-center
                        rounded-lg
                        text-lg
                    "
                >
                    ×
                </button>
            </div>

            <!-- MODAL BODY -->
            <div
                id="modalContent"
                class="
                    px-5 py-4
                    text-sm text-gray-700
                    overflow-y-auto
                    whitespace-pre-line
                    leading-relaxed
                "
            >
                Loading…
            </div>
        </div>
    </div>

</x-app-layout>

<script>
    window.page = { token : "{{$TOKEN}}" }
</script>
