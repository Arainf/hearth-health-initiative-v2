@vite(['resources/css/table.css','resources/js/edit.js'])

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fadeIn {
        animation: fadeIn .2s ease-out;
    }
</style>

<x-app-layout>

    <script>
        window.recordStatus = @json($record->status_id ?? null);
    </script>

    <div class="flex flex-col h-full bg-[#f9fbfc]">

        <div class="flex justify-between items-center px-6 py-3 border-b bg-white sticky top-0 z-30">
            <p class="circular text-lg tracking-tighter">Edit Document</p>

            <div class="flex flex-row gap-2">
                <x-secondary-button href="{{ route('dashboard') }}">
                    Dashboard
                </x-secondary-button>

                <x-secondary-button
                    id="SaveOutput"
                    :class="($record->status_id ?? 0) === 1 ? 'opacity-50 pointer-events-none' : ''"
                >
                    Save
                </x-secondary-button>

                <x-secondary-button id="PrintOutput">
                    Print
                </x-secondary-button>
            </div>
        </div>

        <div class="h-full w-full p-12">
            <div id="editableArea"
                 contenteditable="{{ ($record->status_id ?? 0) !== 1 ? 'true' : 'false' }}"
                 class="w-full h-auto p-12 bg-white border rounded-xl shadow-sm text-[16px] leading-relaxed focus:outline-none"
                 style="white-space: pre-wrap;">
            </div>
        </div>
    </div>

    <!-- SAVE SUCCESS MODAL -->
    <div id="saveSuccessModal"
         class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-[380px] animate-fadeIn">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Saved Successfully</h2>
            <p class="text-gray-600 text-sm mb-6">
                Your document has been saved. What would you like to do next?
            </p>
            <div class="flex justify-end gap-2">
                <button id="stayHereBtn"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-sm">
                    Stay Here
                </button>
                <button id="goBackBtn"
                        class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm">
                    Back to Dashboard
                </button>
            </div>
        </div>
    </div>

    <!-- APPROVAL WARNING MODAL -->
    <div id="approveWarningModal"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-[420px] animate-fadeIn">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">
                Confirm Approval
            </h2>
            <p class="text-sm text-gray-600 mb-5">
                ⚠️ Saving this document will <b>approve</b> it.<br>
                Once approved, this document <b>can no longer be edited</b>.
            </p>
            <div class="flex justify-end gap-3">
                <button id="cancelApproveSave"
                        class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">
                    Cancel
                </button>
                <button id="confirmApproveSave"
                        class="px-4 py-2 rounded-lg bg-[#16a34a20] text-[#16a34a]
                               hover:bg-[#16a34a] hover:text-white transition-all text-sm">
                    Yes, Save & Approve
                </button>
            </div>
        </div>
    </div>

</x-app-layout>
