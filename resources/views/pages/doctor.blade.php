@vite(['resources/css/table.css','resources/js/page/doctor.js'])

<x-app-layout>
    <div class="
    relative flex flex-col h-full px-2 pt-2 overflow-hidden ">

        <!-- HEADER -->
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

        <div class="flex flex-row justify-between mx-4 mb-4 pb-4 border-b border-border ">

            <div class="grid grid-cols-5 grid-rows-2 items-end gap-2 w-[40%] ">

                <div class="w-full col-span-5">
                    <x-filter_search
                        id="record-search"
                        placeholder="Search record"
                        width="w-full"
                    />
                </div>

                <x-dropdown
                    label="Year"
                    name="year-filter"
                    :options="$YEARS"
                    selected="{{$CURRENT}}"
                    all-value="all"
                    all-display="all years"
                    class="dropdown form-control w-full"
                >
                    <x-slot:iconSlot>
                        <x-lucide-calendar-clock class="w-4 h-4"/>
                    </x-slot:iconSlot>
                </x-dropdown>

                <x-dropdown
                    label="Status"
                    name="status-filter"
                    :options="$STATUS"
                    selected="all status"
                    all-value="all"
                    all-display="all status"
                    value-key="id"
                    label-key="status_name"
                    count-key="count"
                    class="dropdown form-control w-full "
                >
                    <x-slot:iconSlot>
                        <x-lucide-badge-info class="w-4 h-4"/>
                    </x-slot:iconSlot>
                </x-dropdown>

                <x-dropdown_select
                    label="Unit/Office"
                    class="form-control w-full col-span-2 "
                    name="unit_office"
                    :options="$UNITS"
                    valueKey="unit_name"
                    labelKey="unit_name"
                    placeholder="Select Unit"
                >
                    <x-slot:iconSlot>
                        <x-lucide-building-2 class="w-4 h-4"/>
                    </x-slot:iconSlot>
                </x-dropdown_select>

                <div class="flex flex-row gap-2">
                    <x-button.search_button  />
                    <x-button.reset_button />
                </div>

            </div>
            <!-- CENTER: SEARCH -->

            <!-- OVERVIEW CARDS -->
            <div class="px-4 pt-4 w-[60%] items-center justify-center font-inter">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                    {{-- ================= Pending ================= --}}
                    <div id="pending-card"
                         class="relative overflow-hidden rounded-xl p-4
                            bg-gradient-to-br
                            from-[var(--badge-pending-bg)]
                            to-[var(--clr-surface-a10)]
                            dark:from-[var(--badge-pending-bg)]
                            dark:to-[var(--clr-surface-a20)]
                            border border-[var(--badge-pending-border)]
                            shadow-sm hover:shadow-md transition">

                        {{-- ===== Skeleton Overlay ===== --}}
                        <div id="pending-skeleton"
                             class="absolute inset-0 z-20 p-4
                                bg-gradient-to-br
                                from-[var(--badge-pending-bg)]
                                to-[var(--clr-surface-a10)]
                                dark:from-[var(--badge-pending-bg)]
                                dark:to-[var(--clr-surface-a20)]
                                animate-pulse">

                            <div class="space-y-3">
                                <div class="h-4 w-32 rounded bg-[var(--clr-surface-a30)]"></div>
                                <div class="h-10 w-20 rounded bg-[var(--clr-surface-a30)]"></div>
                            </div>

                            <div class="mt-5 space-y-2">
                                <div class="h-3 w-28 rounded bg-[var(--clr-surface-a30)]"></div>
                                <div class="h-3 w-40 rounded bg-[var(--clr-surface-a30)]"></div>
                            </div>
                        </div>

                        {{-- ===== Real Content ===== --}}
                        <div id="pending-content"
                             class="relative z-10 opacity-0 transition-opacity duration-300">

                            {{-- Header --}}
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-sm font-medium text-[var(--badge-pending-text)]">
                                        Pending Records
                                    </p>
                                    <p class="text-4xl font-bold text-[var(--text-primary)]"
                                       id="pending-count">
                                        0
                                    </p>
                                </div>

                                <div class="p-3 rounded-full bg-[var(--badge-pending-border)]/30">
                                    <x-lucide-clock
                                        class="absolute w-28 h-28 bottom-[-53%] right-[-15%] blur-sm text-[var(--badge-pending-text)]" />
                                </div>
                            </div>

                            {{-- Extra Info --}}
                            <div class="text-xs text-[var(--text-secondary)] space-y-1">
                                <p>Year: <span class="font-medium" id="pendingYear"> {{ now()->year }}</span></p>
                                <p class="flex items-center gap-1">
                                    <span>Office:</span>
                                    <span id="pendingUnit"
                                          class="font-medium truncate max-w-[160px] inline-block">
                                        All Units
                                    </span>
                                </p>
                            </div>

                        </div>
                    </div>

                    {{-- ================= Not Evaluated ================= --}}
                    <div id="not-evaluated-card"
                         class="relative overflow-hidden rounded-xl p-6
                            bg-gradient-to-br
                            from-[var(--badge-neutral-bg)]
                            to-[var(--clr-surface-a10)]
                            dark:from-[var(--badge-neutral-bg)]
                            dark:to-[var(--clr-surface-a20)]
                            border border-[var(--badge-neutral-border)]
                            shadow-sm hover:shadow-md transition">

                        {{-- ===== Skeleton Overlay ===== --}}
                        <div id="not-evaluated-skeleton"
                             class="absolute inset-0 z-20 p-6
                                bg-gradient-to-br
                                from-[var(--badge-neutral-bg)]
                                to-[var(--clr-surface-a10)]
                                dark:from-[var(--badge-neutral-bg)]
                                dark:to-[var(--clr-surface-a20)]
                                animate-pulse">

                            <div class="flex items-center justify-between mb-4">
                                <div class="space-y-3 w-full">
                                    <div class="h-4 w-32 rounded bg-[var(--clr-surface-a30)]"></div>
                                    <div class="h-10 w-20 rounded bg-[var(--clr-surface-a30)]"></div>
                                </div>
                            </div>

                            <div class="space-y-2 mt-4">
                                <div class="h-3 w-28 rounded bg-[var(--clr-surface-a30)]"></div>
                                <div class="h-3 w-40 rounded bg-[var(--clr-surface-a30)]"></div>
                            </div>
                        </div>

                        {{-- ===== Real Content ===== --}}
                        <div id="not-evaluated-content"
                             class="relative z-10 opacity-0 transition-opacity duration-300">

                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-sm font-medium text-[var(--badge-neutral-text)]">
                                        Not Evaluated
                                    </p>
                                    <p class="text-4xl font-bold text-[var(--text-primary)]"
                                       id="not-evaluated-count">
                                        0
                                    </p>
                                </div>

                                <div class="p-3 rounded-full bg-[var(--badge-neutral-border)]/30">
                                    <x-lucide-circle-question-mark class="absolute w-28 h-28 bottom-[-53%] right-[-15%] blur-sm text-[var(--badge-neutral-text)]" />
                                </div>
                            </div>

                            <div class="text-xs text-[var(--text-secondary)] space-y-1">
                                <p>Year:<span class="font-medium" id="evaluatedYear"> {{ now()->year }}</span></p>
                                <p class="flex items-center gap-1">
                                    <span>Office:</span>
                                    <span id="evaluateUnit"
                                          class="font-medium truncate max-w-[160px] inline-block">
                                        All Units
                                    </span>
                                </p>
                            </div>

                        </div>
                    </div>
                    {{-- ================= Approved ================= --}}
                    <div id="approved-card"
                         class="relative overflow-hidden rounded-xl p-6
                        bg-gradient-to-br
                        from-[var(--badge-approved-bg)]
                        to-[var(--clr-surface-a10)]
                        dark:from-[var(--badge-approved-bg)]
                        dark:to-[var(--clr-surface-a20)]
                        border border-[var(--badge-approved-border)]
                        shadow-sm hover:shadow-md transition">

                        <!-- ===== Skeleton Overlay ===== -->
                        <div id="approved-skeleton"
                             class="absolute inset-0 z-20 p-6
                            bg-gradient-to-br
                            from-[var(--badge-approved-bg)]
                            to-[var(--clr-surface-a10)]
                            dark:from-[var(--badge-approved-bg)]
                            dark:to-[var(--clr-surface-a20)]
                            animate-pulse">

                            <div class="flex items-center justify-between mb-4">
                                <div class="space-y-3 w-full">
                                    <div class="h-4 w-32 rounded bg-[var(--clr-surface-a30)]"></div>
                                    <div class="h-10 w-20 rounded bg-[var(--clr-surface-a30)]"></div>
                                </div>
                            </div>

                            <div class="space-y-2 mt-4">
                                <div class="h-3 w-28 rounded bg-[var(--clr-surface-a30)]"></div>
                                <div class="h-3 w-40 rounded bg-[var(--clr-surface-a30)]"></div>
                            </div>
                        </div>

                        <!-- ===== Actual Content ===== -->
                        <div id="approved-content" class="relative z-10 opacity-0 transition-opacity duration-300">

                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-sm font-medium text-[var(--badge-approved-text)]">
                                        Approved Records
                                    </p>
                                    <p class="text-4xl font-bold text-[var(--text-primary)]"
                                       id="approved-count">
                                        0
                                    </p>
                                </div>

                                <div class="p-3 rounded-full bg-[var(--badge-approved-border)]/30">
                                    <x-lucide-check-circle class="absolute w-28 h-28 bottom-[-53%] right-[-15%] blur-sm text-[var(--badge-approved-text)]" />
                                </div>
                            </div>

                            <div class="text-xs text-[var(--text-secondary)] space-y-1">
                                <p>Year: <span class="font-medium" id="approveYear">{{ now()->year }}</span></p>
                                <p class="flex items-center gap-1">
                                    <span>Office:</span>
                                    <span id="approveUnit"
                                          class="font-medium truncate max-w-[160px] inline-block">
                                        All Units
                                    </span>
                                </p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <!-- RIGHT: FILTERS -->

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
                bg-white/70 dark:bg-[#212121]/70
                h-full w-full
                backdrop-blur-[1px]
                rounded-xl
                hidden
                flex items-center justify-center
                pointer-events-none
            "
        >
            <div class="flex flex-col items-center gap-3">
                <div class="w-8 h-8 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>
                <span class="text-sm text-gray-600 dark:text-gray-300 font-medium">
                    Loading records…
                </span>
            </div>
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
</x-app-layout>



<script>
    window.page = { token : "{{$TOKEN}}" }
</script>
