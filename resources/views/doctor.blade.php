@vite(['resources/css/table.css','resources/js/doctor.js'])

<x-app-layout>
    <div class="relative flex flex-col h-screen bg-[#f9fbfc] px-2 pt-2 overflow-hidden">
        <!-- HEADER -->
        <div class="flex flex-col border-b border-gray-300 bg-white px-6 py-3 z-20 sticky top-0">
            <div class="flex justify-between items-center mb-4">
                <p class="circular text-lg tracking-tighter">Doctor Dashboard</p>


                <x-filter_search id="record-search" placeholder="Search record" width="w-80" />

                <div class="flex flex-row gap-1">
                    <x-filter_year id="year-filter" />
                    <x-filter_status id="status-filter" />
                    <x-filter_reset />
                </div>
            </div>
        </div>

        <!-- OVERVIEW CARDS -->
        <div class="px-6 py-4 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Pending Card -->
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/50 flex items-center justify-center loading-overlay hidden">
                        <div class="w-6 h-6 border-2 border-amber-400 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-amber-700 mb-1">Pending Records</p>
                            <p class="text-3xl font-bold text-amber-900" id="pending-count">0</p>
                        </div>
                        <div class="bg-amber-200 rounded-full p-3">
                            <i class="fa-solid fa-clock text-amber-700 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Not Evaluated Card -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/50 flex items-center justify-center loading-overlay hidden">
                        <div class="w-6 h-6 border-2 border-gray-400 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Not Evaluated</p>
                            <p class="text-3xl font-bold text-gray-900" id="not-evaluated-count">0</p>
                        </div>
                        <div class="bg-gray-200 rounded-full p-3">
                            <i class="fa-solid fa-file-circle-question text-gray-700 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Approved Card -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/50 flex items-center justify-center loading-overlay hidden">
                        <div class="w-6 h-6 border-2 border-green-400 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-700 mb-1">Approved Records</p>
                            <p class="text-3xl font-bold text-green-900" id="approved-count">0</p>
                        </div>
                        <div class="bg-green-200 rounded-full p-3">
                            <i class="fa-solid fa-check-circle text-green-700 text-2xl"></i>
                        </div>
                    </div>
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

    <!-- RIGHT SLIDE PANEL (Generated Report Editor) -->
   <x-rich-editor.main/>


    <!-- SUCCESS MODAL -->
    <div
        id="successModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 backdrop-blur-sm"
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
    function printRow(patientId){
        window.open(`/export/pdf/${patientId}`, "_blank");
    }
</script>
