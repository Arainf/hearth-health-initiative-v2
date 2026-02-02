export function formatExpandedRow(data) {
    const recordId = data.id;
    let extendedActions = ``;

    if(!data.generated_id){
        extendedActions =`
            <!-- ACTIONS -->
            <div id="extended-actions-${recordId}"  class="absolute top-3 right-3 flex gap-2 ">
                <button class="hhi-btn hhi-btn-edit text-xs toggle-edit" data-record-id="${recordId}">
                    <i class="fa-solid fa-pen mr-1"></i> Edit
                </button>
                <button class="hhi-btn hhi-btn-primary text-xs hidden save-record-btn" data-record-id="${recordId}">
                    Save
                </button>
                <button class="hhi-btn hhi-btn-secondary text-xs hidden cancel-edit-btn" data-record-id="${recordId}">
                    Cancel
                </button>
            </div>`
    }

    return `
        <div class="bg-[var(--clr-surface-a10)] border border-[var(--clr-surface-a30)] rounded-lg p-4 text-sm relative record-edit-container"
             data-record-id="${recordId}">
           ${extendedActions}
            <!-- MAIN GRID -->
            <div class="grid gap-6" style="grid-template-columns: repeat(3, 1fr); ">

                <!-- LEFT: INPUTS (2 columns) -->
                <div class="col-span-2 grid grid-cols-2 gap-4">
                    ${renderEditableInput("Cholesterol", "cholesterol", data.cholesterol, recordId)}
                    ${renderEditableInput("HDL", "hdl_cholesterol", data.hdl_cholesterol, recordId)}

                    ${renderEditableInput("Systolic BP", "systolic_bp", data.systolic_bp, recordId)}
                    ${renderEditableInput("FBS", "fbs", data.fbs, recordId)}

                    <div class="col-span-2">
                        ${renderEditableInput("HbA1c", "hba1c", data.hba1c, recordId)}
                    </div>
                </div>

                <!-- RIGHT: RISK FACTORS -->
                <div class="space-y-4 col-start-3">
                    ${renderRiskRadio("Hypertension Tx", "hypertension", data.hypertension, recordId)}
                    ${renderRiskRadio("Diabetes M", "diabetes", data.diabetes, recordId)}
                    ${renderRiskRadio("Current Smoker", "smoking", data.smoking, recordId)}
                </div>
            </div>
        </div>
    `;
}


function renderEditableInput(label, fieldName, value, recordId) {
    return `
        <div>
            <label class="block min:text-md text-[var(--clr-text-a30)] mb-1 text-start">${label}</label>
            <input type="number"
                   step="0.01"
                   class="record-field w-full px-3 py-2 text-sm
                          bg-[var(--clr-surface-a0)]
                          text-[var(--clr-text-a0)]
                          border border-[var(--clr-surface-a30)]
                          rounded
                          disabled:bg-[var(--clr-surface-a10)]
                          disabled:text-[var(--clr-text-a50)]"
                   data-field="${fieldName}"
                   data-record-id="${recordId}"
                   value="${value ?? ""}"
                   disabled />
        </div>
    `;
}

function renderRiskRadio(label, fieldName, value, recordId) {
    const yesChecked = value ? 'checked' : '';
    const noChecked = !value ? 'checked' : '';

    return `
        <div>
            <div class="text-sm font-medium text-[var(--clr-text-a20)] mb-2 text-start">
                ${label}
            </div>

            <div class="flex gap-4">
                <!-- YES -->
                <label class="
                    flex items-center gap-3 px-3 py-2 rounded-lg border
                    text-base font-medium select-none
                    radio-readonly border-[var(--clr-surface-a30)]
                ">
                    <input type="radio"
                           class="risk-field radio-readonly radio-yes w-5 h-5"
                           name="${fieldName}-${recordId}"
                           data-field="${fieldName}"
                           value="1"
                           ${yesChecked}>
                    Yes
                </label>

                <!-- NO -->
                <label class="
                    flex items-center gap-3 px-3 py-2 rounded-lg border
                    text-base font-medium select-none
                    radio-readonly border-[var(--clr-surface-a30)]
                ">
                    <input type="radio"
                           class="risk-field radio-readonly radio-no w-5 h-5"
                           name="${fieldName}-${recordId}"
                           data-field="${fieldName}"
                           value="0"
                           ${noChecked}>
                    No
                </label>
            </div>
        </div>
    `;
}
