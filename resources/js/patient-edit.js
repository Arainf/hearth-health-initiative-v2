// resources/js/patient-edit.js
import $ from 'jquery';
window.$ = window.jQuery = $;

let isDirty = false;

/* ================= DIRTY TRACKING ================= */
$('#patientForm').on('input change', 'input, select, textarea', () => {
    if (!isDirty) {
        isDirty = true;
        $('#dirtyIndicator').removeClass('hidden');
        $('#openSaveModal').prop('disabled', false);
    }
});

/* ================= BACK BUTTON WARNING ================= */
$('#backBtn').on('click', () => {
    if (!isDirty || confirm('You have unsaved changes. Leave anyway?')) {
        window.location.href = '/patients';
    }
});

/* ================= TAB / REFRESH WARNING ================= */
window.addEventListener('beforeunload', e => {
    if (isDirty) {
        e.preventDefault();
        e.returnValue = '';
    }
});

/* ================= BMI ================= */
function calcAndSetBMI() {
    const w = parseFloat($('#weight').val());
    const h = parseFloat($('#height').val());
    if (Number.isNaN(w) || Number.isNaN(h) || w <= 0 || h <= 0) {
        $('#bmi').val('');
        return;
    }
    $('#bmi').val((w / Math.pow(h / 100, 2)).toFixed(2));
}

let bmiTimer = null;
$('#weight, #height').on('input', () => {
    clearTimeout(bmiTimer);
    bmiTimer = setTimeout(calcAndSetBMI, 300);
});

/* ================= AGE ================= */
$('#birth_date').on('change', function () {
    const d = new Date(this.value);
    if (isNaN(d)) return;
    const t = new Date();
    let age = t.getFullYear() - d.getFullYear();
    if (t.getMonth() < d.getMonth() || (t.getMonth() === d.getMonth() && t.getDate() < d.getDate())) age--;
    $('#age').val(age);
});

/* ================= SAVE FLOW ================= */
$('#openSaveModal').on('click', () => {
    if (isDirty) $('#confirmModal').removeClass('hidden');
});

$('#cancelSave').on('click', () => {
    $('#confirmModal').addClass('hidden');
});

$('#confirmSave').on('click', () => {
    $('.save-text').addClass('hidden');
    $('.save-loader').removeClass('hidden');
    isDirty = false;
    $('#patientForm')[0].submit();
});
