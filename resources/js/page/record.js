import $ from 'jquery';

let action = "search";
window.$ = window.jQuery = $;
let currentPatientId = null;


function showLoading() {
    $('#tableLoading').removeClass('hidden');
    $('#saveBtn').prop('disabled', true);
    $('#saveText').addClass('opacity-0');
    $('#saveSpinner').removeClass('hidden');
}

function hideLoading() {
    $('#tableLoading').addClass('hidden');
    $('#saveBtn').prop('disabled', false);
    $('#saveText').removeClass('opacity-0');
    $('#saveSpinner').addClass('hidden');
}


/* ----------------------
   Helpers: enable/disable form regions
   ---------------------- */
function setPatientReadonly(isReadonly, type = null) {

    const form = $('#patientForm');

    // text / number / date inputs
        form.find('input[type="text"], input[type="number"], input[type="date"]')
        .prop('readonly', isReadonly);

    // radio inputs (visual only)
    form.find('input[type="radio"]')
        .prop('disabled', isReadonly)  // 🔥 actually disable them
        .toggleClass('radio-readonly', isReadonly);

    // 🔥 select dropdowns
    form.find('select')
        .prop('disabled', isReadonly);

    // Button + icon logic
    if (type) {
        $('#editPatientBtn').addClass('hidden');
        $('#patientIcon').addClass('fa-lock').removeClass('fa-pen-to-square');
    } else {
        if (isReadonly) {
            $('#editPatientBtn').removeClass('hidden');
            $('#patientIcon').addClass('fa-lock').removeClass('fa-pen-to-square');
        } else {
            $('#patientIcon').removeClass('fa-lock').addClass('fa-pen-to-square');
        }
    }
}


function setFamilyReadonly(isReadonly, type) {
    $('#familyForm').find('input[type="radio"]').prop('disabled', isReadonly);

    if (type){
        $('#editFamilyBtn').addClass('hidden');
        $('#familyIcon').addClass('fa-lock').removeClass('fa-pen-to-square');
    } else {
        if (isReadonly) {
            $('#editFamilyBtn').removeClass('hidden');
            $('#familyIcon').addClass('fa-lock').removeClass('fa-pen-to-square');
        } else {
            $('#familyIcon').removeClass('fa-lock').addClass('fa-pen-to-square');
        }
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

    const formDataObj = {};
    const fd = new FormData(form);
    for (const [k, v] of fd.entries()) {
        formDataObj[k] = v;
    }

    clearValidationErrors();
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

        // 🔴 Handle Validation Errors
        if (res.status === 422) {
            const errorData = await res.json();
            hideLoading();
            showValidationErrors(errorData.errors);
            return;
        }

        if (!res.ok) {
            throw new Error(`Save failed: ${res.status}`);
        }

        await res.json().catch(() => {});

        hideLoading();

        // ✅ Show Success Modal
        document.getElementById('saveModal').classList.remove('hidden');

        setPatientReadonly(false, 1);
        setFamilyReadonly(false);
        unlockPatientsTable();
        clearPatientForm();

    } catch (err) {
        hideLoading();

        console.error('Save error', err);
        alert('Failed to save record.');
    }
}


function showValidationErrors(errors) {
    Object.keys(errors).forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (!input) return;

        input.classList.add('border-red-500');

        const msg = document.createElement('p');
        msg.className =
            'text-xs text-red-500 mt-1 validation-error';
        msg.innerText = errors[field][0];

        const wrapper = input.closest('.w-full');
        if (wrapper) {
            wrapper.appendChild(msg);
        }
    });

    const firstError = document.querySelector('.border-red-500');
    if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function clearValidationErrors() {
    document.querySelectorAll('.validation-error').forEach(el => el.remove());
    document.querySelectorAll('.border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
    });
}





/* ----------------------
   Event wiring (document ready)
   ---------------------- */
$(document).ready(function () {

    // Edit toggles
    $('#editPatientBtn').on('click', function () {
        setPatientReadonly(false);
    });

    $('#editFamilyBtn').on('click', function () {
        setFamilyReadonly(false);
    });

    // Always keep BMI calculated when user edits weight/height
    $('#weight, #height').on('input change', function () {
        calcAndSetBMI();
    });

    // Save button: AJAX submit
    $('#saveBtn').on('click', function (e) {
      showLoading();
        e.preventDefault();
        submitFormAjax();
    });

    // Modal buttons
    $('#createAnotherBtn').on('click', function () {
        $('#saveModal').addClass('hidden');

        clearPatientForm();

        // Unlock table for new selection
        unlockPatientsTable();

        table.ajax.reload();

        // Reset action mode if needed
        action = "search";

        // UX nicety
        $('#record-search-input')
            .val('')
            .attr('placeholder', 'Search patient').focus();

    });


    const params = new URLSearchParams(window.location.search);
    const patient = params.get('patient');
    const record = params.get('record');

    if (patient) {
        action = "search";

        // Optional UX hint (recommended)
        $('#record-search-input')
            .attr('placeholder', 'Patient pre-selected — search to change');
    }

    if(record){
        action = "create";

        // Optional UX hint (recommended)
        $('#record-search-input')
            .attr('placeholder', 'Patient pre-selected — search to change');
    }


    // make sure initial state is editable (no patient selected)
    setPatientReadonly(false, 1);
    setFamilyReadonly(false);
});


function hydratePatientForm(p) {
    if (!p.id) {
        console.warn('Invalid patient data for hydration');
        return;
    }

    currentPatientId = p.id;

    /* --------------------
       BASIC PATIENT INFO
    -------------------- */
    $('#patient_id').val(p.id);
    $('#first_name').val(p.first_name ?? '');
    $('#last_name').val(p.last_name ?? '');
    $('#middle_name').val(p.middle_name ?? '');
    $('#suffix').val(p.suffix ?? '');
    $('#age').val(p.age ?? '');
    $('#contact').val(p.phone_number ?? '');
    $('#birth_date').val(p.birthday ?? '');
    $('select[name="unit_code"]').val(p.unit).trigger('change');
    $('#weight').val(p.weight ?? '');
    $('#height').val(p.height ?? '');
    $('#bmi').val(p.bmi ?? '');
    calcAndSetBMI();
    if (p.sex) {
        $(`input[name="sex"][value="${p.sex}"]`).prop('checked', true);
    }
    const yn = v => (v ? 'y' : 'n');
    $(`input[name="family_hypertension"][value="${yn(p.hypertension)}"]`).prop('checked', true);
    $(`input[name="family_heart-attack-under-60y"][value="${yn(p.heart_attack_under_60y)}"]`).prop('checked', true);
    $(`input[name="family_diabetes-mellitus"][value="${yn(p.diabetes_mellitus)}"]`).prop('checked', true);
    $(`input[name="family_cholesterol"][value="${yn(p.cholesterol)}"]`).prop('checked', true);
    setPatientReadonly(true);
    setFamilyReadonly(true);
    $('#searchResults').addClass('hidden');
    $('#record-search-input, #magnifying').addClass('hidden');
}


function clearPatientForm() {
    $('#patient_id').val('');
    $('#first_name').val('');
    $('#last_name').val('');
    $('#middle_name').val('');
    $('#suffix').val('');
    $('#age').val('');
    $('#birth_date').val('');
    $('#unit').val('');
    $('#contact').val('');
    $('#weight').val('');
    $('#height').val('');
    $('#bmi').val('');
    $('input[name="sex"]').prop('checked', false);
    $('#familyForm input[type="radio"]').prop('checked', false);
    $('#total_cholesterol').val('');
    $('#hdl_cholesterol').val('');
    $('#systolic_bp').val('');
    $('#fbs').val('');
    $('#hba1c').val('');
    $('input[name="hypertension_tx"]').prop('checked', false);
    $('input[name="diabetes_m"]').prop('checked', false);
    $('input[name="smoker"]').prop('checked', false);
    setPatientReadonly(false, 1);
    setFamilyReadonly(false, 1);
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

    const patient = rowData[1]; // minimalData

    $('#patients-nav tbody tr td').removeClass('active-patient');
    $(this).find('td').addClass('active-patient');

    lockPatientsTable();

    // 🔥 Fetch full patient data
    fetchPatient(patient.id);
});


function fetchPatient(id) {
    $.ajax({
        url: window.page.table + '/?id=' + id,
        type: 'GET',
        success: function (data) {
            hydratePatientForm(data);
        },
        error: function () {
            alert('Failed to load patient data.');
        }
    });
}


$('#changePatientBtn').on('click', function () {
    $('.active-patient').removeClass('active-patient');
    setPatientReadonly(false,1);
    setFamilyReadonly(false,1);
    unlockPatientsTable();
    clearPatientForm();
});

$('#patientForm input, #familyForm input').on('input change', function () {
    if (!isTableLocked && currentPatientId) {
        lockPatientsTable();
    }
});
