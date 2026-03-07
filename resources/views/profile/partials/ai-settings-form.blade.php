<section class="space-y-6">

    <header>
        <h2 class="text-xl font-semibold">
            AI Settings
        </h2>

        <p class="text-sm text-[var(--text-muted)]">
            Configure your AI assistant.
        </p>
    </header>

    <div class="flex items-center justify-between">

    <span class="text-sm text-[var(--text-muted)]">
        Enable Editing
    </span>

        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" id="aiToggle" class="sr-only peer">
            <div class="w-10 h-5 bg-gray-500 rounded-full peer-checked:bg-blue-600
after:content-[''] after:absolute after:top-[2px] after:left-[2px]
after:bg-white after:h-4 after:w-4 after:rounded-full
after:transition peer-checked:after:translate-x-5"></div>
        </label>

    </div>

    <form method="POST"
          action="{{ route('profile',['token'=>$encryption->encrypt('edit')]) }}"
          class="space-y-4">

        @csrf
        @method('PATCH')

        <input type="hidden" name="section" value="ai">

        <fieldset id="aiFieldset" disabled class="opacity-50 transition">

            <div>
                <x-input-label for="openai_api_key" value="OpenAI API Key"/>
                <input
                    type="password"
                    name="openai_api_key"
                    class="w-full rounded-lg bg-[var(--bg-light)]  "
                    placeholder="sk-********************************"/>

            </div>

            <div class="mt-2">
                <x-input-label for="ai_prompt" value="Default Prompt"/>
                <textarea
                    name="ai_prompt"
                    rows="4"
                    class="w-full rounded-lg bg-[var(--bg-light)] ">{{ old('ai_prompt',auth()->user()->ai_prompt) }}</textarea>

            </div>

        </fieldset>

        <div class="flex justify-end">

            <x-secondary-button id="saveAiBtn" disabled>
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
