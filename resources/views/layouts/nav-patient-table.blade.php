@vite(['resources/css/table.css'])


<style>
    #patients-nav tbody td {
        text-align: start !important; /* blue-50 */
    }
    /* Active (selected) patient row */
    #patients-nav tbody tr td.active-patient {
        background-color: #b8d7ff !important; /* blue-50 */
        z-index: 100 !important;
    }

    /* Hover disabled when table is locked */
    #patients-nav.table-locked tbody tr:hover {
        background-color: inherit;
        cursor: not-allowed;
    }

    /* Disable pointer events when locked */
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
        h-full
        rounded-xl
        border
        bg-[#FCFDFE]
        shadow-lg
        border-gray-100
        flex
        flex-col
        justify-between
        transition-all
        duration-300
        overflow-hidden
    "
>


    <div class="flex flex-col gap-4 p-5  h-full">

        <x-filter_search id="record-search" placeholder="Search Patient" width="w-full" />
        <button
            id="changePatientBtn"
            class="hidden mt-3 px-4 py-2 text-sm font-medium
           bg-gray-100 hover:bg-gray-200
           border border-gray-300 rounded-md">
            Change Patient
        </button>

        <table id="patients-nav" class="dataTable table">
            <thead>
                <tr>
                    <th>Patients</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>


    </div>

</nav>
