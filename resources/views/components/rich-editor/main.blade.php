@vite('resources/css/rich-editor/editor.css')

<div
    id="generatedPanel"
    class="

        fixed inset-0
        z-50
        transform translate-x-full
        transition-transform duration-300 ease-in-out
        flex flex-col bg-[var(--clr-surface-a20)]
    "
>
    <!-- HEADER -->
    <div class="flex font-inter items-center justify-between px-6 py-2 border-b bg-[var(--clr-surface-a30)]">
        <div>
            <h3 class="text-lg font-semibold">Generated Report</h3>
            <p class="text-sm text-[var(--text-secondary)]" id="panelRecordId"></p>
        </div>

        <div class="flex gap-3">
            <button
                id="panelEditBtn"
                class="hhi-btn hhi-btn-edit-neutral px-4 text-md"

            >
                <x-lucide-edit class="w-3 h-3 mr-1"/>
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
    <x-rich-editor.toolbox />
    <div class="flex flex-row h-full relative editor-container overflow-y-auto self-center mt-4 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] ">
        <div class="bg-white w-[8.27in]  font-[Arial] mr-6 h-fit text-black " id="editorPage" >
            <x-rich-editor.header/>
            <div class="px-10 py-3">
                <x-rich-editor.subheader/>
                <div id="content" x-ref="editor" class="prose max-w-none"></div>
                <x-rich-editor.footer/>
            </div>
        </div>
    </div>



    <!-- FOOTER -->
    <div
        id="panelFooter"
        class="
        hidden
        absolute bottom-0
        px-6 py-4
        flex justify-end gap-4
        self-end
        w-[max-content]
        rounded-lg
    "
    >
        <button
            id="panelSaveBtn"
            class="px-6 py-3 hhi-btn hhi-btn-save text-md"
        >
            <x-lucide-save class="w-3 h-3 mr-2" />
            Save
        </button>

        <button
            id="panelSaveApproveBtn"
            class="px-6 py-3 hhi-btn hhi-btn-save-approve text-md"
        >
            <x-lucide-check class="w-3 h-3 mr-1"/>
            Save &amp; Approve
        </button>
    </div>
</div>
