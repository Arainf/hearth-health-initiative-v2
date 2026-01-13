<div id="recordOverlay"
     class="fixed inset-0 z-50 bg-black/50 hidden h-full opacity-0 transition-opacity duration-300">

    <div id="recordSidebar"
         class="absolute top-0 right-0 h-full w-[450px] bg-white shadow-2xl border-l border-gray-200 translate-x-full transition-[transform,width] duration-300 overflow-y-auto">

        {{-- 🧭 Header --}}
        <div class="sticky top-0 flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white">
            <div class="flex flex-col">
                <h2 id="reference-number" class="text-lg font-semibold text-gray-800 leading-tight"></h2>
                <p class="text-sm text-gray-500 mt-1">Record details</p>
            </div>
            <button id="closeSidebar" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>

        {{-- ⚙️ Sidebar Content --}}
        <div class="flex flex-row">

            {{-- LEFT SIDE — Record Sections --}}
            <div id="record-sections" class="flex flex-col flex-1">

                {{-- General Info --}}
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">General Information</h2>
                    <div class="flex flex-col gap-2 text-sm">
                        <p class="flex justify-between">
                            <span class="text-gray-500">Created</span>
                            <span id="created-at" class="font-medium text-gray-800"></span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-gray-500">Status</span>
                            <span id="status" class="font-medium text-gray-800 capitalize"></span>
                        </p>
                    </div>
                </div>

                {{-- Patient Info --}}
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Patient Information</h2>
                    <div class="space-y-3">
                        <div class="flex items-start justify-between">
                            <p id="patient-name" class="text-xl text-gray-800 font-bold leading-snug"></p>
                            <div class="flex gap-2 text-gray-500 text-sm font-medium">
                                <span id="height"></span>
                                <span id="weight"></span>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <div class="text-sm text-gray-600 space-y-1">
                                <p class="flex gap-1 font-medium">
                                    <span id="unit"></span>
                                    <span>&#8226;</span>
                                    <span id="phone"></span>
                                </p>
                                <p class="flex gap-1">
                                    <span id="gender"></span>
                                    <span id="age"></span>
                                </p>
                            </div>
                            <p id="bmi" class="text-sm text-gray-500 font-medium"></p>
                        </div>
                    </div>
                </div>

                {{-- Health Metrics --}}
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Health Metrics</h2>
                    <div class="flex flex-col gap-2 text-sm">
                        <p class="flex justify-between"><span class="text-gray-500">Total Cholesterol</span><span id="cholesterol" class="font-medium text-gray-800"></span></p>
                        <p class="flex justify-between"><span class="text-gray-500">HDL Cholesterol</span><span id="hdl" class="font-medium text-gray-800"></span></p>
                        <p class="flex justify-between"><span class="text-gray-500">Systolic BP</span><span id="systolic" class="font-medium text-gray-800"></span></p>
                        <p class="flex justify-between"><span class="text-gray-500">FBS</span><span id="fbs" class="font-medium text-gray-800"></span></p>
                        <p class="flex justify-between"><span class="text-gray-500">HbA1c</span><span id="hba" class="font-medium text-gray-800"></span></p>
                    </div>
                </div>

                {{-- Existing Conditions --}}
                <div id="recordContent" class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Existing Conditions</h2>
                    <div class="flex flex-wrap gap-2 text-sm text-gray-700">
                        <div class="text-gray-500">Select a record to view details.</div>
                    </div>
                </div>

                {{-- 🧠 AI-Generated Analysis (Compact) --}}
                <div id="analysisSection" class="relative px-6 py-6 bg-white">

                    <span class="flex flex-row justify-between w-full  items-center align-middle ">
                        <h2 class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Generated Analysis</h2>
                         {{-- Buttons --}}
                        <div class="flex justify-center mt-4 gap-2">
                            <button id="evaluateBtn"
                                    class="hidden px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Evaluate Record
                            </button>

                            <button id="enlargeBtnOpen"
                                    class="hidden flex items-center gap-2 px-4 py-2 text-sm text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition">
                                <i class="fa-solid fa-up-right-and-down-left-from-center text-xs"></i>
                            </button>
                        </div>
                    </span>

                    {{-- Content --}}
                    <div id="generatedContent" class="relative max-h-96 overflow-hidden text-sm text-gray-800 leading-relaxed transition-all">
                        <div class="whitespace-pre-line generatedText"></div>
                        <div id="gradientFade"
                             class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
                    </div>


                </div>
            </div>

            {{-- 🧩 Expanded Right Section --}}
            <div id="analysisSectionRight"
                 class="relative hidden px-6 py-6 w-full h-screen bg-white border-l border-gray-200">
                <span class="flex flex-row w-full items-center align-middle justify-between">
                    <h2 class="text-xs font-semibold text-gray-500 mb-2 contents text-center uppercase tracking-wide">Analysis</h2>

                    <span class="flex flex-row gap-2">
                         <x-secondary-button
                             id="approve_button"
                             class="
                                approve_button_text
                                bg-[#9ca3af20]
                                text-[#9ca3af]
                                border border-[#9ca3af40]
                                hover:bg-[#16a34a20]
                                hover:text-[#16a34a]
                                hover:border-[#16a34a40]
                                transition-all duration-150 ease-out
                            "
                                                 >
                            Approve
                        </x-secondary-button>

                        <x-secondary-button id="edit_button"
                            class="edit_generated_text"
                         >
                            Edit
                        </x-secondary-button>

                         <button id="enlargeBtnClose"
                                 class="hidden flex items-center gap-2 px-4 py-2 text-sm text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition">
                            <i class="fa-solid fa-down-left-and-up-right-to-center text-xs"></i>
                        </button>
                    </span>
                </span>

                {{-- Editable area --}}
                <div id="generatedContentExpanded" class="relative h-full overflow-auto text-sm text-gray-800 leading-relaxed bg-white p-4">
                    <div class="whitespace-pre-line generatedText"></div>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="approveModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">

    <div class="bg-white rounded-xl w-[420px] p-6 shadow-xl">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
            Confirm Approval
        </h3>

        <p class="text-sm text-gray-600 mb-4">
            ⚠️ Once approved, this record can no longer be edited.
            <br><br>
            Are you sure you want to continue?
        </p>

        <div class="flex justify-end gap-3">
            <button id="cancelApprove"
                    class="px-4 py-2 text-sm rounded-md bg-gray-100 hover:bg-gray-200">
                Cancel
            </button>

            <button id="confirmApprove"
                    class="px-4 py-2 text-sm rounded-md
                           bg-[#16a34a20] text-[#16a34a]
                           hover:bg-[#16a34a] hover:text-white
                           transition-all">
                Yes, Approve
            </button>
        </div>
    </div>
</div>


{{-- 🎨 Styles --}}
<style>
    .sidebar-open {
        transform: translateX(0) !important;
    }

    .overlay-visible {
        opacity: 1 !important;
    }

    /* Expanded Mode */
    .expanded-sidebar {
        width: 1200px !important;
        display: flex;
        flex-direction: column;
        transition: width 0.3s ease-in-out;
    }

    .expanded-sidebar #record-sections {
        width: 40%;
        border-right: 1px solid #e5e7eb;
        overflow-y: auto;
    }

    .expanded-sidebar #analysisSectionRight {
        display: block !important;
        width: 60%;
        overflow-y: auto;
    }

    .expanded-sidebar #generatedContent {
        max-height: none !important;
    }

    .expanded-sidebar #gradientFade {
        display: none !important;
    }
</style>
