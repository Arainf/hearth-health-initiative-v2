@vite('resources/css/rich-editor/editor.css')

<div
    id="generatedPanel"
    class="
        fixed inset-0
        z-50
        transform translate-x-full
        transition-transform duration-300 ease-in-out
        flex flex-col bg-gray-200
    "
>
    <!-- HEADER -->
    <div class="flex items-center justify-between px-6 py-2 border-b bg-gray-50">
        <div>
            <h3 class="text-lg font-semibold">Generated Report</h3>
            <p class="text-sm text-gray-500" id="panelRecordId"></p>
        </div>

        <div class="flex gap-3">
            <button
                id="panelEditBtn"
                class="hhi-btn hhi-btn-edit-neutral px-4 text-md"

            ><i class="fa-solid fa-edit mr-1"></i>
                Edit
            </button>

            <button
                id="closeGeneratedPanel"
                class="px-4 py-3 hhi-btn hhi-btn-close text-md"
            >
                ✕
            </button>
        </div>
    </div>
    <x-rich-editor.toolbox/>
    <div class="flex flex-row h-full relative editor-container overflow-y-auto self-center mt-4 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] ">
        <div class="bg-white w-[8.27in] mr-6 h-fit" id="editorPage" >
            <x-rich-editor.header/>
            <div class="px-10 py-8">
                <div x-ref="editor" class="prose max-w-none"></div>
            </div>
        </div>

        <div class="ruler" id="pageRuler"></div>
    </div>

{{--    <div class="flex gap-4 h-full relative editor-container overflow-y-auto self-center mt-4 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] ">--}}
{{--        <!-- EDITOR -->--}}
{{--        <div class="flex-1">--}}
{{--            <div id="editorPage">--}}
{{--                <x-rich-editor.header/>--}}
{{--                <div class="px-10 py-8">--}}
{{--                    <div x-ref="editor" class="prose max-w-none"></div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="ruler" id="pageRuler"></div>--}}
{{--        <!-- PRINT PREVIEW -->--}}
{{--        <div class="w-[8.27in] bg-gray-100 border-l">--}}
{{--            <iframe--}}
{{--                id="printPreview"--}}
{{--                class="w-full h-full bg-white shadow"--}}
{{--            ></iframe>--}}
{{--        </div>--}}
{{--    </div>--}}





    <!-- FOOTER -->
    <div
        id="panelFooter"
        class="px-6 py-4 border-t bg-transparent  flex justify-end gap-4 hidden"
    >

        <button
            id="panelSaveBtn"
            class="px-6 py-3 hhi-btn hhi-btn-save text-md "
        >
            <i class="fas fa-save mr-2"></i>
            Save
        </button>

        <button
            id="panelSaveApproveBtn"
            class="px-6 py-3 hhi-btn hhi-btn-save-approve text-md "
        >
            <i class="fa-solid fa-check"></i>
            Save & Approve
        </button>
    </div>
</div>
