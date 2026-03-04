<x-app-layout>
@vite(['resources/css/rich-editor/editor.css', 'resources/js/page/generated.js', 'resources/css/app.css', 'resources/js/app.js',])
    <div class="flex flex-col h-screen bg-[var(--clr-surface-a20)] overflow-hidden font-inter relative">

        <div class="flex items-center justify-between px-6 py-2 border-b bg-[var(--clr-surface-a30)] z-20 shadow-sm">
            <div class="flex items-center gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Generated Report</h3>
                </div>
            </div>

            <div class="flex gap-3">
                {{-- EDIT BUTTON --}}
                <button id="panelEditBtn" class="hhi-btn hhi-btn-edit-neutral px-4 text-md flex items-center">
                    <x-lucide-edit class="w-3 h-3 mr-1"/> <span>Edit</span>
                </button>

                {{-- BACK BUTTON --}}
                <button type="button" onclick="handleBack()" class="hhi-btn hhi-btn-back px-4 text-md flex items-center shadow-sm">
                    <x-lucide-chevron-left class="w-4 h-4 mr-1"/> <span>Back</span>
                </button>

                @if($record->status_id == '1')
                    <a href="{{route('page', ['token' => $encryption->encrypt('doctor') , 'id' => $record->record_id , 'mode' => $encryption->encrypt('print')])}}" target="_blank" class="hhi-btn hhi-btn-edit-neutral px-4 text-md flex items-center">
                        <x-lucide-printer class="w-3 h-3 mr-1"/> <span>Print</span>
                    </a>
                @endif

            </div>
        </div>

        <x-rich-editor.toolbox />

        <div class="flex flex-row h-full relative editor-container overflow-y-auto self-center mt-4 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] ">
            <div class="bg-white w-[8.27in]  font-[Arial] mr-6 h-fit text-black " id="editorPage" >
                <x-rich-editor.header/>

                <div class="px-10 py-3">
                    <x-rich-editor.subheader :record="$record" />
                    <div id="content" x-ref="editor" class="prose max-w-none"></div>
                    <x-rich-editor.footer/>
                </div>
            </div>
        </div>

        <div
            id="panelFooter"
            class="
                hidden
                fixed bottom-6 right-6
                px-6 py-4
                flex justify-end gap-4
                z-30
                bg-white/80 backdrop-blur-md
                border border-gray-200
                rounded-xl shadow-lg
                animate-[fadeIn_.2s_ease-out]
            "
        >
            <button
                id="panelSaveBtn"
                class="px-6 py-3 hhi-btn hhi-btn-save text-md flex items-center"
            >
                <i data-lucide="save" class="w-3 h-3 mr-2"></i>
                Save
            </button>

            @if($record->status_id != 1)
                <button
                    id="panelSaveApproveBtn"
                    class="px-6 py-3 hhi-btn hhi-btn-save-approve text-md flex items-center"
                >
                    <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                    Save & Approve
                </button>
            @endif
        </div>
    </div>
</x-app-layout>
    <script>
        window.reportConfig = {
            id: "{{ $record->record_id }}",
            token: "{{ $TOKEN }}",
            modeSave: "{{ $MODE_SAVE }}",
            modeApprove: "{{ $MODE_APPROVE }}",
            initialContent: `{!! $record->generated_text !!}`
        };
    </script>
