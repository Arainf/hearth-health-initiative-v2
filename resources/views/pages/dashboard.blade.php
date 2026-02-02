@vite(['resources/css/table.css','resources/js/records.js'])

<x-app-layout>
    <div class=" relative flex flex-col h-screen bg-[#f9fbfc] px-2 pt-2 overflow-hidden">
        <!-- HEADER -->
        <div class="flex flex-col border-b border-gray-300 bg-white px-6 py-3 z-20 sticky top-0">
            <div class="flex justify-between items-center">
                <p class="circular text-lg tracking-tighter">Heart Health Records</p>

                <x-filter_search id="record-search" placeholder="Search record" width="w-80" />

                <div class="flex flex-row gap-1">
                    <x-dropdown
                        name="year-filter"
                        :options="$years"
                        selected="{{$currentYear}}"
                        all-value="all"
                        all-display="all years"
                        class="dropdown form-control"
                    />

                    <x-dropdown
                        name="status-filter"
                        :options="$status"
                        selected="all status"
                        all-value="all"
                        all-display="all status"

                        value-key="id"
                        label-key="status_name"
                        count-key="count"
                        class="dropdown form-control"
                    />
                    <x-filter_reset />
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

    <x-rich-editor.main/>

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


    function  printRow(patientId){
        window.open(`/export/pdf/${patientId}`, "_blank");
    }


</script>
