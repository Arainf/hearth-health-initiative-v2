@vite(['resources/css/table.css'])

@php
    $UNITS = $dropdown->units();
@endphp

<style>
    #patients-nav tbody td {
        text-align: start !important;
    }

    #patients-nav tbody tr td.active-patient {
        background-color: #b8d7ff !important;
        z-index: 100 !important;
    }

    #patients-nav.table-locked tbody tr:hover {
        background-color: inherit;
        cursor: not-allowed;
    }

    #patients-nav.table-locked tbody {
        cursor: not-allowed !important;
        pointer-events: none;
        opacity: 0.85;
        background-color: #0f172a !important;
    }
</style>

<nav
    class="
        w-full
        bg-[var(--clr-surface-a0)]
        border-[var(--clr-surface-a30)]
        h-full
        rounded-xl
        border
        shadow-lg
        flex
        flex-col
        justify-between
        transition-all
        duration-300
        overflow-hidden
        font-inter
    "
>

    <div class="flex flex-col gap-4 p-5 h-full">

        <!-- TOP ROW -->
        <div class="flex flex-row gap-1 items-center">

            <!-- Search -->
            <div
                x-show="!sidebarCollapsed"
                x-transition:enter.delay.225ms
                class="w-full"
            >
                <x-filter_search
                    id="record-search"
                    placeholder="Search Patient"
                    width="w-full"

                />
            </div>

            <!-- Add Button -->
            <div
                x-show="!sidebarCollapsed"
                x-transition:enter.delay.225ms
            >
            </div>

            <!-- Toggle Button (Always Visible) -->
            <button
                @click="sidebarCollapsed = !sidebarCollapsed;
                setTimeout(() => {
                    window.table?.columns.adjust();
                }, 320);"
                class="p-2 rounded-lg hover:bg-[var(--clr-surface-a20)] transition z-50"
                title="Toggle sidebar"
            >
                <x-lucide-chevron-right
                    class="w-6 h-6 text-[var(--badge-disabled-text)] transition-transform duration-300"
                    ::class="sidebarCollapsed ? 'rotate-0 pr-1' : 'rotate-180 pl-1'"
                />
            </button>

        </div>

        <!-- Dropdown -->
        <div
            x-show="!sidebarCollapsed"
            x-transition:enter.delay.225ms
        >
            <x-form.dropdown
                class="w-full form-control form-control-sm"
                label="Unit"
                name="unit_office"
                :options="$UNITS"
                valueKey="unit_name"
                labelKey="unit_name"
                placeholder="Select Unit"
            />
        </div>

        <!-- Change Patient -->
        <button
            id="changePatientBtn"
            x-show="!sidebarCollapsed"
            x-transition:enter.delay.225ms
            class="mt-3 px-4 py-2 text-sm font-medium
                   bg-gray-100 hidden hover:bg-gray-200
                   border border-gray-300 rounded-md"
        >
            Change Patient
        </button>

        <!-- Table -->
        <table
            id="patients-nav"

            x-show="!sidebarCollapsed"
            x-transition:enter.delay.225ms
            class="dataTable table"
        >
            <thead>
            <tr>
                <th>Patients</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>

</nav>
