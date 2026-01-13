@vite(['resources/css/table.css','resources/js/patients-show.js'])


<style>
    table.dataTable thead tr > th:nth-child(8) {
        text-align: center !important;
    }
</style>

<x-app-layout>
<div class="w-full h-full relative">

    <div class="bg-white rounded-xl shadow-sm p-4 h-full">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold tracking-wide">
                {{ $patient->last_name }}, {{ $patient->first_name }} - Records
            </h2>

            <button
                class="hhi-btn hhi-btn-back"
                onclick="window.location='{{ route('patient') }}'"
            >
                Back
            </button>
        </div>

        {{-- TABLE --}}
        <table id="patient-records-table" class="table datatable w-full bg-[#f9fbfc]">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Total Cholesterol<br><span class="text-[10px]">(mg/dL)</span></th>
                    <th>HDL<br><span class="text-[10px]">(mg/dL)</span></th>
                    <th>Systolic BP<br><span class="text-[10px]">(mmHg)</span></th>
                    <th>FBS<br><span class="text-[10px]">(mg/dL)</span></th>
                    <th>HbA1c<br><span class="text-[10px]">(%)</span></th>
                    <th>Risks</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Evaluation</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>

    {{-- CENTER FLOATING MODAL --}}
    <div
        id="reportModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40"
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
            {{-- MODAL HEADER --}}
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

            {{-- MODAL BODY --}}
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

</div>
</x-app-layout>
