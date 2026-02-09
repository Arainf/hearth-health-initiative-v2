<section class="bg-[#121212] rounded-xl shadow-sm p-6">
    <header class="mb-4">
        <h2 class="text-lg font-semibold text-white">
            AI Settings
        </h2>
        <p class="text-sm text-gray-400">
            Configure your OpenAI API key and default prompt.
        </p>
    </header>

    {{-- Toggle --}}
    <div class="flex items-center justify-between mb-4">
        <span class="text-sm font-medium text-gray-400">
            Enable editing
        </span>

        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" id="aiToggle" class="sr-only peer">
            <div class="w-11 h-6 bg-gray-500 rounded-full peer
                        peer-focus:outline-none
                        peer-checked:bg-blue-600
                        after:content-['']
                        after:absolute after:top-[2px] after:left-[2px]
                        after:bg-[#121212] after:rounded-full
                        after:h-5 after:w-5
                        after:transition-all
                        peer-checked:after:translate-x-full">
            </div>
        </label>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <input type="hidden" name="section" value="ai">

        {{-- Locked fields --}}
        <fieldset id="aiFieldset" disabled
                  class="opacity-50 pointer-events-none transition">

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">
                    OpenAI API Key
                </label>
                <input
                    type="password"
                    name="openai_api_key"
                    placeholder="sk-********************************"
                    class="w-full rounded-lg bg-[#121212] border-gray-400/20
                           focus:border-blue-500 focus:ring-blue-500"
                />
                <p class="text-xs text-gray-500 mt-1">
                    Leave blank to keep existing key.
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-1">
                    Default Prompt
                </label>
                <textarea
                    name="ai_prompt"
                    rows="4"
                    class="w-full rounded-lg bg-[#121212] border-gray-400/20
                           focus:border-blue-500 focus:ring-blue-500"
                    placeholder="You are a helpful medical assistant..."
                >{{ old('ai_prompt', auth()->user()->ai_prompt) }}</textarea>
            </div>
        </fieldset>

        <div class="flex justify-end">
            <x-secondary-button
                type="submit"
                id="saveAiBtn"
                disabled
                class="opacity-50">
                Save AI Settings
            </x-secondary-button>
        </div>
    </form>
</section>

{{-- SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('aiToggle');
        const fieldset = document.getElementById('aiFieldset');
        const saveBtn = document.getElementById('saveAiBtn');

        if (!toggle || !fieldset || !saveBtn) return;

        function syncState() {
            if (toggle.checked) {
                fieldset.disabled = false;
                fieldset.classList.remove('opacity-50', 'pointer-events-none');
                saveBtn.disabled = false;
                saveBtn.classList.remove('opacity-50');
            } else {
                fieldset.disabled = true;
                fieldset.classList.add('opacity-50', 'pointer-events-none');
                saveBtn.disabled = true;
                saveBtn.classList.add('opacity-50');
            }
        }

        // Initial lock state
        syncState();

        toggle.addEventListener('change', syncState);
    });
</script>
