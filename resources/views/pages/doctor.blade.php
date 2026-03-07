@vite(['resources/css/table.css','resources/js/page/doctor.js'])

<x-app-layout>
    <div class="relative flex flex-col h-full p-1 overflow-hidden ">

        <!-- HEADER -->
        <div class="flex flex-row border-b px-3 py-2 z-20 gap-4 sticky top-0 items-center justify-between ">

            <div class="flex items-center font-inter font-semibold shrink-0">
                @php
                    $icon = $MODULE_NAME['icon'];
                @endphp
                <x-dynamic-component :component="'lucide-' . $icon" class="mr-2 w-[var(--s-icon)] h-[var(--s-icon)]"/>
                <span class="text-[length:var(--s-header)] font-[var(--w-header)] font-inter ">
                    {{ $MODULE_NAME['label'] }}
                </span>
            </div>


            <div class="grid grid-cols-8 max-h-28 gap-1 md:gap-1 lg:gap-2 w-[75%] ">

                <div class="w-full  sm:h-10 md:h-8 lg:h-12 col-span-2  md:col-span-2">
                    <x-filter_search
                        id="record-search"
                        placeholder="Search record"
                        width="w-full"
                    />
                </div>

                <div class="col-span-2 md:col-span-1">
                    <x-dropdown
                        name="year-filter"
                        :options="$YEARS"
                        selected="{{$CURRENT}}"
                        all-value="all"
                        all-display="Year"
                        class="dropdown form-control w-full"
                    />
                </div>

                <div class="col-span-2 md:col-span-2">
                    <x-dropdown
                        name="status-filter"
                        :options="$STATUS"
                        selected="all"
                        all-value="all"
                        all-display="Status"
                        value-key="id"
                        label-key="status_name"
                        count-key="count"
                        class="dropdown form-control w-full "
                    />
                </div>


                <div class="sm:h-10 md:h-8 lg:h-12 md:col-span-2 ">
                    <x-dropdown_select
                        class="form-control w-full col-span-2  "
                        name="unit_office"
                        :options="$UNITS"
                        valueKey="unit_name"
                        labelKey="unit_name"
                        placeholder="Select Unit"
                    />
                </div>


                <div class="flex flex-row gap-1">
                    <x-button.search_button  />
                    <x-button.reset_button />
                </div>
            </div>

        </div>

        <div class="flex flex-row justify-between items-center px-1 py-2 gap-2 border-b">
            <div id="datatable-pagination">
            </div>
            <div>
                <x-button variant="primary" id="btnOpenTemplate">
                    <x-lucide-layout-template class="w-[var(--s-icon)] h-[var(--s-icon)]"/> Get Template
                </x-button>
                <x-button variant="primary" id="btnOpenImport">
                    <x-lucide-download class="w-[var(--s-icon)] h-[var(--s-icon)]"/> Upload Csv
                </x-button>
            </div>
        </div>


        <!-- TABLE SECTION -->
        <div class="relative flex-1  p-2 w-full h-full">
            <x-table.records/>
        </div>

    </div>


    <!-- SUCCESS MODAL -->
    <div
        id="successModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-[var(--clr-surface-a70)] backdrop-blur-sm"
    >
        <div class="bg-white rounded-xl shadow-2xl p-6 w-[380px] animate-[fadeIn_.15s_ease-out]">
            <div class="flex items-center justify-center mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-check text-green-600 text-xl"></i>
                </div>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2 text-center" id="successTitle">Success</h2>
            <p class="text-gray-600 text-sm mb-6 text-center" id="successMessage">
                Operation completed successfully.
            </p>
            <div class="flex justify-end">
                <button
                    id="closeSuccessBtn"
                    class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm"
                >
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- TEMPLATE MODAL -->
    <div id="modalTemplate" class="fixed inset-0 hidden flex items-center justify-center backdrop-blur-sm font-inter " style="z-index: 20">
        <div class="bg-[var(--system-background)] w-[500px] rounded-xl shadow-xl p-6 relative">
            <button type="button" class="close-modal absolute top-3 right-3 text-gray-500 hover:text-[var(--secondary-text)]">✕</button>
            <h2 class="text-2xl font-semibold mb-4">Download Template</h2>
            <p class="text-sm text-[var(--secondary-text)] mb-6">Download the CSV template file to ensure proper formatting. Also select a unit/office.</p>

            <x-dropdown_select label="Unit/Office" class="form-control w-full col-span-2" name="unit_office_template" :options="$UNITS" valueKey="unit_code" labelKey="unit_name" placeholder="Select Unit">
                <x-slot:iconSlot><x-lucide-building-2 class="w-[var(--s-icon)] h-[var(--s-icon)]"/></x-slot:iconSlot>
            </x-dropdown_select>

            <div class="flex justify-end gap-3 mt-3">
                <x-button variant="ghost" type="button" class="close-modal">Cancel</x-button>
                <x-button variant="primary" id="export_template" data-mode="{{ $encryption->encrypt('export') }}">
                    <x-lucide-download class="w-[var(--s-icon)] h-[var(--s-icon)]"/> Download Template
                </x-button>
            </div>
        </div>
    </div>

    {{-- IMPORT MODAL --}}
    <div id="modalImport" class="fixed inset-0  hidden flex items-center justify-center  backdrop-blur-sm font-inter" style="z-index: 20">
        <div id="importModalContainer" class="bg-[var(--system-background)] w-[520px] rounded-xl shadow-xl p-6 relative transition-all duration-300 ease-in-out">
            <button type="button" class="close-modal absolute top-5 right-5 text-gray-500 hover:text-[var(--secondary-text)]">✕</button>
            <h2 class="text-2xl font-semibold mb-2">Import Patient Records</h2>
            <p class="text-sm text-[var(--secondary-text)]  mb-4">Upload the official system-generated template (.xlsx only).</p>

            <div id="import_step_1">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-[var(--secondary-text)] mb-1">Upload Excel File (.xlsx only)</label>
                    <input type="file" id="import_file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div class="flex justify-end gap-3 mt-4">
                    <x-button variant="ghost" type="button" class="close-modal">Cancel</x-button>
                    <x-button variant="primary" id="validate_import" data-mode="{{ $encryption->encrypt('validate') }}">Validate File</x-button>
                </div>
            </div>

            <!-- STEP 2 -->

            <div id="import_step_2" class="hidden">
                <div class="bg-[var(--secondary-system-background)] border rounded-lg p-4 text-sm">
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
                    <table class="min-w-full text-xs border-collapse h-full font-inter text-[var(--secondary-color)]">
                        <thead class="bg-[var(--tertiary-system-background)] sticky top-0 z-10">
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
                        <tbody id="preview_table_body" class="bg-[var(--secondary-system-background)]">
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-between mt-4">
                    <x-button variant="ghost" id="back_to_upload">
                        Back
                    </x-button>
                    <x-button id="confirm_import"
                              data-mode="{{ $encryption->encrypt('confirm') }}"
                              class="bg-green-600 text-white">
                        Confirm & Save
                    </x-button>
                </div>
            </div>
            <div id="import_loading" class="hidden text-center text-sm text-[var(--secondary-text)] mt-4">Processing...</div>
        </div>
    </div>
</x-app-layout>



<script>
    window.page = { token : "{{$TOKEN}}" }
</script>
