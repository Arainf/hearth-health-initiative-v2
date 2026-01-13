import $ from 'jquery';

let action = "search";
window.$ = window.jQuery = $;
let currentPatientId = null;

function togglePatientOverlay(show) {
    $('#patientOverlay').toggleClass('hidden', !show);
}

function toggleFamilyOverlay(show) {
    $('#familyOverlay').toggleClass('hidden', !show);
}

function showLoading() {
    $('#tableLoading').removeClass('hidden');
}

function hideLoading() {
    $('#tableLoading').addClass('hidden');
}


/* ----------------------
   Helpers: enable/disable form regions
   ---------------------- */
function setPatientReadonly(isReadonly) {
    // text inputs
    $('#patientForm').find('input[type="text"], input[type="number"], input[type="date"]').prop('readonly', isReadonly);
    // radio inputs
    $('#patientForm')
        .find('input[type="radio"]')
        .toggleClass('radio-readonly', isReadonly);

    // show/hide edit button appropriately
    if (isReadonly) {
        $('#editPatientBtn').removeClass('hidden');
    } else {
        $('#editPatientBtn').addClass('hidden');
    }
}

function setFamilyReadonly(isReadonly) {
    $('#familyForm').find('input[type="radio"]').prop('disabled', isReadonly);
    if (isReadonly) {
        $('#editFamilyBtn').removeClass('hidden');
    } else {
        $('#editFamilyBtn').addClass('hidden');
    }
}

/* ----------------------
   BMI calc
   ---------------------- */
function calcAndSetBMI() {
    const w = parseFloat($('#weight').val());
    const h = parseFloat($('#height').val());
    if (!w || !h) {
        $('#bmi').val('');
        return;
    }
    const hm = h / 100;
    const bmi = w / (hm * hm);
    if (isFinite(bmi)) {
        $('#bmi').val(bmi.toFixed(2));
    } else {
        $('#bmi').val('');
    }
}

/* ----------------------
   Render search results
   ---------------------- */
async function submitFormAjax() {
    const form = document.getElementById('mainForm');
    const action = form.getAttribute('action') || window.location.href;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // gather form data to JSON
    const formDataObj = {};
    const fd = new FormData(form);
    for (const [k, v] of fd.entries()) {
        formDataObj[k] = v;
    }

    showLoading();
    try {
        const res = await fetch(action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formDataObj),
        });

        if (!res.ok) {
            const text = await res.text();
            throw new Error(`Save failed: ${res.status} ${text}`);
        }

        let json = null;
        try { json = await res.json(); } catch (e) { /* ignore */ }

        // show modal
        hideLoading();
        $('#saveModal').removeClass('hidden');

    } catch (err) {
        console.error('Save error', err);
        alert('Failed to save record. See console for details.');
    }
}



/* ----------------------
   Event wiring (document ready)
   ---------------------- */
$(document).ready(function () {

    // Edit toggles
    $('#editPatientBtn').on('click', function () {
        setPatientReadonly(false);
        togglePatientOverlay(false);
    });

    $('#editFamilyBtn').on('click', function () {
        setFamilyReadonly(false);
        toggleFamilyOverlay(false);
    });

    // Always keep BMI calculated when user edits weight/height
    $('#weight, #height').on('input change', function () {
        calcAndSetBMI();
    });

    // Save button: AJAX submit
    $('#saveBtn').on('click', function (e) {
        e.preventDefault();
        submitFormAjax();
    });

    // Modal buttons
    $('#createAnotherBtn').on('click', function () {
        // Close modal
        $('#saveModal').addClass('hidden');

        // Clear patient & form
        clearPatientForm();

        // Unlock table for new selection
        unlockPatientsTable();

        table.ajax.reload();

        // Reset action mode if needed
        action = "search";

        // UX nicety
        $('#record-search-input')
            .val('')
            .attr('placeholder', 'Search patient');

        // Optional: scroll to top or focus search
        $('#record-search-input').focus();
    });


    $('#goDashboardBtn').on('click', function () {
        window.location.href = '/dashboard';
    });

    $('#goDashboardBtn2').on('click', function () {
        window.location.href = '/dashboard';
    });

    const params = new URLSearchParams(window.location.search);
    const patient = params.get('patient');
    const record = params.get('record');

    if (patient) {
        action = "search";
        loadPatientAndLock(patient);


        // Optional UX hint (recommended)
        $('#record-search-input')
            .attr('placeholder', 'Patient pre-selected — search to change');
    }

    if(record){
        action = "create";
        loadPatientAndLock(record);


        // Optional UX hint (recommended)
        $('#record-search-input')
            .attr('placeholder', 'Patient pre-selected — search to change');
    }


    // make sure initial state is editable (no patient selected)
    setPatientReadonly(false);
    setFamilyReadonly(false);
});


function hydratePatientForm(p) {
    if (!p || !p.id) {
        console.warn('Invalid patient data for hydration');
        return;
    }

    currentPatientId = p.id;

    toggleFamilyOverlay(true);
    togglePatientOverlay(true);

    /* --------------------
       BASIC PATIENT INFO
    -------------------- */
    $('#patient_id').val(p.id);
    $('#first_name').val(p.first_name ?? '');
    $('#last_name').val(p.last_name ?? '');
    $('#middle_name').val(p.middle_name ?? '');
    $('#suffix').val(p.suffix ?? '');
    $('#age').val(p.age ?? '');
    $('#unit').val(p.unit ?? '');
    $('#contact').val(p.phone_number ?? '');
    $('#birth_date').val(p.birth_date ?? '');


    /* --------------------
       HEIGHT / WEIGHT / BMI
    -------------------- */
    $('#weight').val(p.weight ?? '');
    $('#height').val(p.height ?? '');
    $('#bmi').val(p.bmi ?? '');
    calcAndSetBMI();

    /* --------------------
       SEX
    -------------------- */
    if (p.sex) {
        $(`input[name="sex"][value="${p.sex}"]`).prop('checked', true);
    }

    /* --------------------
       FAMILY HISTORY
    -------------------- */
    const history = p.family_history ?? {};
    const yn = v => (v ? 'y' : 'n');

    $(`input[name="family_hypertension"][value="${yn(history.Hypertension)}"]`).prop('checked', true);
    $(`input[name="family_heart-attack-under-60y"][value="${yn(history.Heart_Attack)}"]`).prop('checked', true);
    $(`input[name="family_diabetes-mellitus"][value="${yn(history.Diabetes)}"]`).prop('checked', true);
    $(`input[name="family_cholesterol"][value="${yn(history.Cholesterol)}"]`).prop('checked', true);

    /* --------------------
       LOCK FORM (view-only)
    -------------------- */
    setPatientReadonly(true);
    setFamilyReadonly(true);

    $('#searchResults').addClass('hidden');
    $('#record-search-input, #magnifying').addClass('hidden');
}


function clearPatientForm() {

    // --------------------
    // BASIC PATIENT INFO
    // --------------------
    $('#patient_id').val('');
    $('#first_name').val('');
    $('#last_name').val('');
    $('#middle_name').val('');
    $('#suffix').val('');
    $('#age').val('');
    $('#birth_date').val('');
    $('#unit').val('');
    $('#contact').val('');

    // --------------------
    // HEIGHT / WEIGHT / BMI
    // --------------------
    $('#weight').val('');
    $('#height').val('');
    $('#bmi').val('');

    // --------------------
    // SEX
    // --------------------
    $('input[name="sex"]').prop('checked', false);

    // --------------------
    // FAMILY HISTORY
    // --------------------
    $('#familyForm input[type="radio"]').prop('checked', false);

    // --------------------
    // RISK FACTORS
    // --------------------
    $('#total_cholesterol').val('');
    $('#hdl_cholesterol').val('');
    $('#systolic_bp').val('');
    $('#fbs').val('');
    $('#hba1c').val('');

    $('input[name="hypertension_tx"]').prop('checked', false);
    $('input[name="diabetes_m"]').prop('checked', false);
    $('input[name="smoker"]').prop('checked', false);

    // --------------------
    // UI STATE
    // --------------------
    togglePatientOverlay(false);
    toggleFamilyOverlay(false);

    setPatientReadonly(false);
    setFamilyReadonly(false);

    // Restore search UI
    $('#record-search-input, #magnifying').removeClass('hidden');
}

let isTableLocked = false;

function lockPatientsTable() {
    isTableLocked = true;
    $('#patients-nav').addClass('table-locked');
    $('#changePatientBtn').removeClass('hidden');
}

function unlockPatientsTable() {
    isTableLocked = false;
    $('#patients-nav').removeClass('table-locked');
    $('#patients-nav tbody tr').removeClass('active-patient');
    $('#changePatientBtn').addClass('hidden');
}



$('#patients-nav tbody').on('click', 'tr', function () {
    if (isTableLocked) return;

    const rowData = table.row(this).data();
    if (!rowData) return;

    const patient = rowData[1]; // hiddenData

    // Highlight active row
    $('#patients-nav tbody tr td').removeClass('active-patient');
    $(this).find('td').addClass('active-patient');

    // Lock table
    lockPatientsTable();

    // Hydrate form
    hydratePatientForm(patient);
});

$('#changePatientBtn').on('click', function () {
    $('.active-patient').removeClass('active-patient');
    unlockPatientsTable();
    clearPatientForm();
});

$('#patientForm input, #familyForm input').on('input change', function () {
    if (!isTableLocked && currentPatientId) {
        lockPatientsTable();
    }
});
