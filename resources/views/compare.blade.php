@vite(['resources/css/table.css','resources/js/compare-records.js'])

<x-app-layout>
    <div class="relative flex flex-col h-screen bg-[#f9fbfc] overflow-hidden">
        <!-- HEADER -->
        <div class="flex flex-col border-b border-gray-300 bg-white px-4 sm:px-6 py-3 sm:py-4 z-20 sticky top-0">
            <div class="flex items-center justify-between gap-2 sm:gap-3">
                <p class="circular text-base sm:text-lg tracking-tighter text-[#0A2540]">
                    Compare Records
                </p>

                <a
                    href="{{ route('dashboard') }}"
                    class="hhi-btn hhi-btn-back tracking-tighter circular text-xs sm:text-sm"
                >
                    Records
                </a>
            </div>

            <!-- SEARCH BAR -->
            <div class="flex items-center gap-2 sm:gap-3 mt-3">
                <div class="relative flex-1 max-w-md" id="searchWrapper">
                    <x-filter_search id="record-search" placeholder="Search patient name..." width="w-full" />
                    <div id="searchSpinner" class="hidden absolute right-3 top-2.5">
                        <i class="fa-solid fa-circle-notch fa-spin spinner text-gray-400"></i>
                    </div>
                </div>

            </div>
        </div>

        <!-- PATIENT SEARCH RESULTS AREA -->
        <div id="patientResultsArea" class="hidden border-b border-gray-200 bg-white px-4 sm:px-6 py-3 sm:py-4">
            <div class="text-xs text-gray-500 mb-2 sm:mb-3">Search Results</div>
            <div id="patientResultsList" class="flex flex-wrap gap-2 sm:gap-3">
                <!-- Patient cards will be injected here -->
            </div>
        </div>

        <!-- SELECTED PATIENT INFO -->
        <div id="selectedPatientInfo" class="hidden border-b border-gray-200 bg-white px-4 sm:px-6 py-3 sm:py-4">
            <div class="flex items-center justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-sm sm:text-base text-gray-900 truncate" id="selectedPatientName"></div>
                    <div class="text-xs text-gray-500 mt-1" id="selectedPatientDetails"></div>
                </div>
                <button id="clearPatientBtn" class="hhi-btn hhi-btn-secondary text-xs flex-shrink-0">
                    <i class="fa-solid fa-times mr-1"></i> Clear
                </button>
            </div>
        </div>

        <!-- RECORDS CAROUSEL AREA -->
        <div id="recordsCarouselArea" class="flex-1 bg-[#e7e8ea] overflow-hidden flex flex-col">
            <div id="recordsCarousel" class="flex-1 overflow-x-auto overflow-y-hidden px-3 sm:px-4 md:px-6 py-4 sm:py-6">
                <div id="recordsContainer" class="flex gap-3 sm:gap-4 h-full items-start">
                
                </div>
            </div>
        </div>
    </div>

    <!-- RECORD MODAL -->
    <div
        id="recordModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 backdrop-blur-sm"
    >
        <div
            id="modalContainer"
            class="
                bg-white
                w-full max-w-4xl
                max-h-[90vh]
                rounded-xl
                shadow-2xl
                flex flex-col
                animate-[fadeIn_.15s_ease-out]
                mx-4
            "
        >
            <!-- MODAL HEADER -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <div class="flex items-center gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800" id="modalRecordTitle">
                            Record Details
                        </h3>
                        <div class="text-xs text-gray-500 mt-1" id="modalRecordDate"></div>
                    </div>
                    <button
                        id="enterCompareModeBtn"
                        class="hhi-btn hhi-btn-primary text-xs hidden"
                        title="Compare with another record"
                    >
                        <i class="fa-solid fa-columns mr-1"></i>
                        Compare
                    </button>
                </div>

                <button
                    id="closeRecordModal"
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

            <!-- MODAL BODY - Single Record View -->
            <div
                id="modalRecordContent"
                class="
                    px-6 py-4
                    text-sm text-gray-700
                    overflow-y-auto
                    whitespace-pre-line
                    leading-relaxed
                    flex-1
                "
            >
                Loading…
            </div>

            <!-- MODAL BODY - Comparison View -->
            <div
                id="modalCompareContent"
                class="hidden flex-1 overflow-hidden flex flex-col"
            >
                <!-- Comparison Header -->
                <div class="flex items-center justify-between px-6 py-3 border-b bg-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <div class="text-xs font-semibold text-gray-500 uppercase">Record 1</div>
                            <div class="text-sm font-medium text-gray-900" id="compareRecord1Title"></div>
                        </div>
                        <div class="text-gray-400">
                            <i class="fa-solid fa-arrows-left-right"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs font-semibold text-gray-500 uppercase">Record 2</div>
                            <div class="text-sm font-medium text-gray-900" id="compareRecord2Title"></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <select id="compareRecordSelect"
                                class="border rounded px-3 py-1.5 text-xs text-gray-700 focus:ring-2 focus:ring-blue-500">
                            <!-- options injected -->
                        </select>
                        <button
                            id="exitCompareModeBtn"
                            class="hhi-btn hhi-btn-secondary text-xs"
                            title="Exit comparison mode"
                        >
                            <i class="fa-solid fa-times mr-1"></i>
                            Exit Compare
                        </button>
                    </div>
                </div>

                <!-- Comparison Content -->
                <div class="flex-1 overflow-y-auto flex">
                    <div class="flex-1 border-r border-gray-200 p-6 overflow-y-auto">
                        <div id="compareRecord1Content" class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">
                            Loading…
                        </div>
                    </div>
                    <div class="flex-1 p-6 overflow-y-auto">
                        <div id="compareRecord2Content" class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">
                            <div class="text-center py-12 text-gray-400">
                                <i class="fa-solid fa-hand-pointer text-3xl mb-3"></i>
                                <div class="text-sm font-medium mb-1">Select a record to compare</div>
                                <div class="text-xs">Click on any record card below</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Patient Result Cards */
        .patient-result-card {
            padding: 10px 14px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            min-width: 180px;
            flex: 0 0 auto;
        }

        @media (min-width: 768px) {
            .patient-result-card {
                min-width: 200px;
                padding: 10px 16px;
            }
        }

        .patient-result-card:hover {
            border-color: #2563eb;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.15);
            transform: translateY(-1px);
        }

        .patient-result-card.selected {
            border-color: #2563eb;
            background: #eff6ff;
        }

        /* Record Card Styles */
        .record-card {
            min-width: 280px;
            max-width: 320px;
            height: calc(100vh - 240px);
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: all 0.2s ease;
            cursor: pointer;
            flex: 0 0 auto;
        }

        @media (min-width: 768px) {
            .record-card {
                min-width: 320px;
                max-width: 380px;
                height: calc(100vh - 280px);
            }
        }

        @media (min-width: 1024px) {
            .record-card {
                min-width: 360px;
                max-width: 400px;
            }
        }

        .record-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            border-color: #2563eb;
        }

        .record-card-header {
            padding: 12px 14px;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(to bottom, #f8fafc, #ffffff);
        }

        @media (min-width: 768px) {
            .record-card-header {
                padding: 16px;
            }
        }

        .record-card-body {
            flex: 1;
            overflow-y: auto;
            padding: 12px 14px;
        }

        @media (min-width: 768px) {
            .record-card-body {
                padding: 16px;
            }
        }

        .record-card-footer {
            padding: 10px 14px;
            border-top: 1px solid #e5e7eb;
            background: #f8fafc;
        }

        @media (min-width: 768px) {
            .record-card-footer {
                padding: 12px 16px;
            }
        }

        .record-metric {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .record-metric:last-child {
            border-bottom: none;
        }

        .record-metric-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
        }

        .record-metric-value {
            font-size: 13px;
            color: #111827;
            font-weight: 600;
        }

        .risk-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .risk-badge.active {
            background: #fef2f2;
            color: #b91c1c;
        }

        .risk-badge.inactive {
            background: #f3f4f6;
            color: #6b7280;
        }

        /* Carousel Scrollbar */
        #recordsCarousel::-webkit-scrollbar {
            height: 8px;
        }

        #recordsCarousel::-webkit-scrollbar-track {
            background: #e5e7eb;
            border-radius: 4px;
        }

        #recordsCarousel::-webkit-scrollbar-thumb {
            background: #9ca3af;
            border-radius: 4px;
        }

        #recordsCarousel::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        /* Modal Content - Single Column */
        #modalRecordContent {
            column-count: 1;
            column-gap: 0;
            font-size: 14px;
            line-height: 1.8;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-2px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* iPad and Tablet Optimizations */
        @media (min-width: 768px) and (max-width: 1024px) {
            .record-card {
                min-width: 300px;
                max-width: 340px;
                height: calc(100vh - 260px);
            }

            .patient-result-card {
                min-width: 190px;
            }
        }

        /* Comparison Mode Styles */
        #modalCompareContent .flex > div {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f3f4f6;
        }

        #modalCompareContent .flex > div::-webkit-scrollbar {
            width: 6px;
        }

        #modalCompareContent .flex > div::-webkit-scrollbar-track {
            background: #f3f4f6;
        }

        #modalCompareContent .flex > div::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        #modalCompareContent .flex > div::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Record card highlight in comparison mode */
        .record-card.ring-2 {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-ring {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }

        /* Mobile Optimizations */
        @media (max-width: 767px) {
            .record-card {
                min-width: 260px;
                max-width: 300px;
                height: calc(100vh - 220px);
            }

            .patient-result-card {
                min-width: 160px;
                padding: 8px 12px;
            }

            #modalContainer.max-w-7xl {
                max-width: 95vw;
            }

            #modalCompareContent .flex {
                flex-direction: column;
            }

            #modalCompareContent .flex > div {
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }
        }
    </style>
</x-app-layout>
