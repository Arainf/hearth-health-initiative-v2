<x-app-layout>
@vite(['resources/css/rich-editor/editor.css', 'resources/css/app.css', 'resources/js/app.js',])
<div class="flex flex-col bg-[var(--bg)] h-full overflow-hidden font-inter relative">

    <div class="flex items-center justify-between px-6 py-2 border-b bg-[var(--bg-light)] z-20 shadow-sm">
        <div class="flex items-center gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Generated Report</h3>
            </div>
        </div>

        <div class="flex gap-3">
            @if($record->status_id == '1')
                <a href="{{route('page', ['token' => $encryption->encrypt('doctor') , 'id' => $record->record_id , 'mode' => $encryption->encrypt('print')])}}" target="_blank" class="hhi-btn hhi-btn-edit-neutral px-4 text-md flex items-center">
                    <x-lucide-printer class="w-3 h-3 mr-1"/> <span>Print</span>
                </a>
            @endif
            <button onclick="handleBack()"
               class="hhi-btn hhi-btn-back px-4 text-md flex items-center no-underline">
                <x-lucide-chevron-left class="w-4 h-4 mr-1"/> <span>Back</span>
            </button>
        </div>
    </div>

    <div class="flex flex-row h-full relative editor-container overflow-y-auto self-center mt-4 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] ">
        <div class="bg-white w-[8.27in]  font-[Arial] mr-6 h-fit text-black " id="editorPage" >
            <x-rich-editor.header/>

            <div class="px-10 py-3">
                <x-rich-editor.subheader :record="$record" />
                <div id="content" class="mt-2">
                    {!! $record->generated_text !!}
                </div>
                <x-rich-editor.footer/>
            </div>
        </div>
    </div>

</div>
    </x-app-layout>

<script>
    // Configuration object to bridge Blade data to JavaScript
    window.reportConfig = {
        token: "{{ $encryption->encrypt('patient') }}", // Default fallback module
        initialContent: `{!! addslashes($record->generated_text) !!}`,
        // Check if user is a doctor to determine if edit mode logic should apply
        isDoctor: {{ auth()->user()->is_Doctor() ? 'true' : 'false' }}
    };

    window.handleBack = function() {
        // 1. Check for unsaved changes
        // 'reportEditor' assumes you are using TipTap or a similar editor defined globally
        if (window.reportConfig.isDoctor && typeof reportEditor !== 'undefined') {
            const currentContent = reportEditor.getHTML();

            if (currentContent.trim() !== window.reportConfig.initialContent.trim()) {
                if (!confirm("You have unsaved changes. Are you sure you want to go back?")) {
                    return;
                }
            }
        }

        // 2. Navigation Logic
        // If we have a valid internal history, go back to preserve DataTable state
        if (document.referrer && document.referrer.indexOf(window.location.host) !== -1) {
            history.back();
        } else {
            // Fallback: Redirect to the record list using the encrypted token
            window.location.href = `/page/${window.reportConfig.token}`;
        }
    };
</script>
