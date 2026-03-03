export function formatExpandedRow(data) {

    const recordId = data.id;
    const mode = data.mode;

    const yesNo = (field, value) => `
        <div class="space-y-1 text-left">
            <div class="text-xs uppercase tracking-wide text-[var(--clr-text-a40)]">
                ${field}
            </div>
            <div class="flex gap-3">
                <label class="flex items-center gap-2 px-3 py-2 rounded-lg border
                              border-[var(--clr-surface-a30)]
                              bg-[var(--clr-surface-a10)] text-sm cursor-pointer radio-readonly">
                    <input type="radio"
                           class="risk-field w-4 h-4 "
                           name="${field}-${recordId}"
                           data-field="${field}"
                           value="1"
                           ${value ? 'checked' : ''}>
                    Yes
                </label>
                <label class="flex items-center gap-2 px-3 py-2 rounded-lg border
                              border-[var(--clr-surface-a30)]
                              bg-[var(--clr-surface-a10)] text-sm cursor-pointer radio-readonly">
                    <input type="radio"
                           class="risk-field w-4 h-4 "
                           name="${field}-${recordId}"
                           data-field="${field}"
                           value="0"
                           ${!value ? 'checked' : ''}>
                    No
                </label>
            </div>
        </div>
    `;

    let actions = ``;

    if (!data.generated_id) {
        actions = `
            <div class="absolute top-3 right-3 flex gap-2">
                <button class="hhi-btn hhi-btn-edit text-xs toggle-edit flex items-center gap-1 toggle-edit"
                        data-record-id="${recordId}">
                    <i data-lucide="pencil" class="w-3 h-3"></i>
                </button>

                <button class="hhi-btn hhi-btn-primary text-xs hidden save-record-btn flex items-center gap-1 save-record-btn"
                        data-record-id="${recordId}" data-mode="${mode}">
                    <i data-lucide="check" class="w-3 h-3"></i>
                </button>

                <button class="hhi-btn hhi-btn-secondary text-xs hidden cancel-edit-btn flex items-center gap-1 cancel-edit-btn"
                        data-record-id="${recordId}">
                    <i data-lucide="x" class="w-3 h-3"></i>
                </button>
            </div>
        `;
    }

    return `
        <div class="font-inter bg-[var(--clr-surface-a0)] p-4 relative text-sm record-edit-container">

            ${actions}

            <div class="grid grid-cols-2 gap-6">

                <!-- LEFT SIDE -->
                <div class="grid grid-cols-2 gap-4">

                    <div class="space-y-1 text-left">
                        <label class="text-xs uppercase tracking-wide text-[var(--clr-text-a40)]">
                            Total Cholesterol
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01"
                                   class="record-field w-full px-3 py-2 text-sm
                                          bg-[var(--clr-surface-a10)]
                                          border border-[var(--clr-surface-a30)]
                                          rounded-lg"
                                   data-field="cholesterol"
                                   data-record-id="${recordId}"
                                   value="${data.cholesterol ?? ''}" disabled>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[var(--clr-text-a50)]">
                                mg/dl
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1 text-left">
                        <label class="text-xs uppercase tracking-wide text-[var(--clr-text-a40)]">
                            HDL Cholesterol
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01"
                                   class="record-field w-full px-3 py-2 text-sm
                                          bg-[var(--clr-surface-a10)]
                                          border border-[var(--clr-surface-a30)]
                                          rounded-lg"
                                   data-field="hdl_cholesterol"
                                   data-record-id="${recordId}"
                                   value="${data.hdl_cholesterol ?? ''}" disabled>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[var(--clr-text-a50)]">
                                mg/dl
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1 text-left">
                        <label class="text-xs uppercase tracking-wide text-[var(--clr-text-a40)]">
                            Systolic BP
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01"
                                   class="record-field w-full px-3 py-2 text-sm
                                          bg-[var(--clr-surface-a10)]
                                          border border-[var(--clr-surface-a30)]
                                          rounded-lg"
                                   data-field="systolic_bp"
                                   data-record-id="${recordId}"
                                   value="${data.systolic_bp ?? ''}" disabled>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[var(--clr-text-a50)]">
                                mmHg
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1 text-left">
                        <label class="text-xs uppercase tracking-wide text-[var(--clr-text-a40)]">
                            FBS
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01"
                                   class="record-field w-full px-3 py-2 text-sm
                                          bg-[var(--clr-surface-a10)]
                                          border border-[var(--clr-surface-a30)]
                                          rounded-lg"
                                   data-field="fbs"
                                   data-record-id="${recordId}"
                                   value="${data.fbs ?? ''}" disabled>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[var(--clr-text-a50)]">
                                mg/dl
                            </span>
                        </div>
                    </div>

                    <div class="col-span-2 space-y-1 text-left">
                        <label class="text-xs uppercase tracking-wide text-[var(--clr-text-a40)]">
                            HbA1c
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01"
                                   class="record-field w-full px-3 py-2 text-sm
                                          bg-[var(--clr-surface-a10)]
                                          border border-[var(--clr-surface-a30)]
                                          rounded-lg"
                                   data-field="hba1c"
                                   data-record-id="${recordId}"
                                   value="${data.hba1c ?? ''}" disabled>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[var(--clr-text-a50)]">
                                %
                            </span>
                        </div>
                    </div>

                </div>

                <!-- RIGHT SIDE -->
                <div class="space-y-4">

                    ${yesNo("hypertension", data.hypertension)}
                    ${yesNo("diabetes", data.diabetes)}
                    ${yesNo("smoking", data.smoking)}

                </div>

            </div>
        </div>
    `;
}
