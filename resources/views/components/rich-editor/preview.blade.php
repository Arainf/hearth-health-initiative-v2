<div class="flex gap-4 h-full">
    <!-- EDITOR -->
    <div class="flex-1">
        <div id="editorPage">
            <x-rich-editor.header/>
            <div class="px-10 py-8">
                <div x-ref="editor" class="prose max-w-none"></div>
            </div>
        </div>
    </div>

    <!-- PRINT PREVIEW -->
    <div class="w-[8.27in] bg-gray-100 border-l">
        <iframe
            id="printPreview"
            class="w-full h-full bg-white shadow"
        ></iframe>
    </div>
</div>
