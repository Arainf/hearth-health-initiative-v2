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

    <div
        class="flex-1 bg-white overflow-y-auto"
        x-data="tiptapEditor('')"
    >
        <x-rich-editor.toolbox/>
        <!-- EDITOR CONTENT -->
        <div class="px-10 py-8  mx-auto w-[8.27in]">
            <div
                x-ref="editor"
                class="prose max-w-none focus:outline-none"
            ></div>
        </div>
    </div>

    <!-- FOOTER -->
    <div
        id="panelFooter"
        class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-4 hidden"
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
