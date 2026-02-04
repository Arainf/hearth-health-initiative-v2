import $ from "jquery";
import DataTable from "datatables.net-dt";

import { setupYearFilterRecords } from "@/filters/filter-year.js";
import { setupStatusFilterRecords } from "@/filters/filter-status.js";
import { formatExpandedRow } from "@/utilities/table-expanded-form.js";

window.$ = window.jQuery = $;

const ai_Access = document.body.dataset.aiAccess === '1';
const ai_Ready = document.body.dataset.aiReady === '1';


/* ===============================
   STATE & URL MANAGEMENT
================================ */
const state = {
    status: new URLSearchParams(window.location.search).get('status') || 'all',
    year: new URLSearchParams(window.location.search).get('year') || new Date().getFullYear(),
    search: new URLSearchParams(window.location.search).get('search') || ''
};

const generatingRecords = new Set();


// Pending state for staged filter changes (not yet applied)
const pending = {
    status: null,
    year: null,
    search: null
};

function updateURL() {
    const params = new URLSearchParams();
    if (state.status && state.status !== 'all') params.set('status', state.status);
    if (state.year && state.year !== 'all') params.set('year', state.year);
    if (state.search) params.set('search', state.search);

    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.pushState({}, '', newUrl);
}

/* ===============================
   LOADING
================================ */


/* ===============================
   FILTER BUTTON STATE
================================ */
const $filterBtn = $("#reset-filters");
const $searchInput = $("#record-search");


window.resetFilters = function () {
    const currentYear = new Date().getFullYear();

    // Reset state
    state.status = 'all';
    state.year   = currentYear;
    state.search = '';

    // Clear pending
    pending.status = null;
    pending.year   = null;
    pending.search = null;

    // Reset UI
    $searchInput.val('');
    $("#status-filter-label").text('All');
    $("#year-filter-label").text(currentYear);

    updateURL();

    // Reload dependent filters
    if (window.refreshStatusFilter) {
        window.refreshStatusFilter(currentYear);
    }

    table.ajax.reload();
}


/* ===============================
   STAGE FILTERS (Don't apply yet)
================================ */
function stageFilter(type, value) {
    pending[type] = value;
}

/* ===============================
   APPLY FILTERS (SAFE)
================================ */
function applyFilters(filters = {}) {
    // Update state from filters or pending
    if (filters.status !== undefined) {
        state.status = filters.status;
        pending.status = null;
    } else if (pending.status !== null) {
        state.status = pending.status;
        pending.status = null;
    }

    if (filters.year !== undefined) {
        state.year = filters.year;
        pending.year = null;
    } else if (pending.year !== null) {
        state.year = pending.year;
        pending.year = null;
    }

    if (filters.search !== undefined) {
        state.search = filters.search;
        pending.search = null;
    } else if (pending.search !== null) {
        state.search = pending.search;
        pending.search = null;
    }

    // Update URL
    updateURL();

    // Apply filters to DataTable
    table.ajax.reload();
}

/* ===============================
   APPLY ALL PENDING FILTERS
================================ */
window.applyPendingFilters = function () {
    applyFilters({
        status: pending.status !== null ? pending.status : undefined,
        year: pending.year !== null ? pending.year : undefined,
        search: pending.search !== null ? pending.search : undefined
    });
}

/* ===============================
   DATATABLE INIT
================================ */
const table = $("#records-table").DataTable({
    serverSide: true,
    processing: true,
    pageLength: 20,

    scrollY: "calc(100vh - 230px)",
    scrollCollapse: true,

    autoWidth: false,
    paging: true,
    info: false,
    lengthChange: false,

    dom: `
        <"datatable-wrapper"
            <"datatable-body"t>
            <"datatable-footer"p>
        >
    `,

    ajax: {
        url: window.page.table,
        type: "GET",
        data: d => {
            d.search = state.search || '';
            d.status = state.status || Date.now();
            d.year   = state.year || 'all';
        },
    },

    columnDefs: [
        { targets: 0, width: "35%" },
        { targets: 1, width: "15%" },
        { targets: 2, width: "10%" },
        { targets: 3, width: "20%" },
        { targets: 4, width: "10%" }
    ],

    columns: [
        {
            data: "patient",
            render: p => `
                <div class="leading-tight">
                    <div class="font-medium text-gray-900 text-sm">
                        (${p.unit}) ${p.last_name}, ${p.first_name} ${p.middle_name ?? ""}
                    </div>
                    <div class="text-xs text-gray-500">${p.age} y.o.</div>
                </div>
            `
        },
        {
            data: "status.status_name",
            orderable: false
        },
        {
            data: "staff",
            orderable: false
        },
        {
            data: "created_at",
            render: d =>
                new Date(d).toLocaleDateString("en-US", {
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                })
        },
        {
            data: null,
            orderable: false,
            className: "text-center",
            render: r => {
                const hasGenerated = r.generated_id && r.generated_id !== null && r.generated_id !== '';
                const hasDoctorApproval = r.doctor !== null && r.doctor !== '';

                const generatingButton = `
                <div id="generateBtn-${r.id}" class="generate-btn hidden">
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                    Generating
                </div>`

                const printBtn = hasGenerated
                    ? `<button class="hhi-btn hhi-btn-view icon-only"
                            title="Print"
                            onclick="printRow('${r.id}')">
                        <i class="fa-solid fa-print"></i>
                    </button>`
                    : `<button class="hhi-btn hhi-btn-view icon-only opacity-50 cursor-not-allowed"
                            title="No report available"
                            disabled>
                        <i class="fa-solid fa-print"></i>
                    </button>`;


                // View button - blue (only if generated)
                const viewBtn =
                    `<button class="hhi-btn hhi-btn-view icon-only view-generated-btn" ${!hasGenerated ? "disabled" : ' '}
                            title="${!hasGenerated ? "No Evaluation" : 'View Evaluation'}"
                            data-id="${r.generated_id}">
                       <i class="fa-solid fa-magnifying-glass"></i>
                    </button>`


                // Evaluate button - purple (only if not generated)
                const evaluateBtn = !hasGenerated && ai_Access && ai_Ready
                    ? `<button class="hhi-btn hhi-btn-evaluate icon-only evaluate-btn"
                            title="Evaluate with AI"
                            data-index="${r.counter}"
                            data-record-id="${r.id}">
                        <i class="fa-solid fa-brain"></i>
                    </button>`
                    : '';


                const printBtnStyled = hasDoctorApproval
                    ? `<button class="hhi-btn hhi-btn-print icon-only"
                            title="Print"
                            onclick="printRow('${r.id}')">
                        <i class="fa-solid fa-print"></i>
                    </button>`
                    : hasGenerated ? `<button
                            class="hhi-btn hhi-btn-print icon-only bg-transparent border border-gray-200 text-gray-400 opacity-50 cursor-not-allowed"
                            title="Need Approval from a Doctor"
                            disabled
                        >
                            <i class="fa-solid fa-print"></i>
                        </button>
                        ` : '';

                return `
                <div class="flex flex-col items-center justify-center gap-1">
                    ${generatingButton}
                    <div id="actionsBtn-${r.id}" class="actions-btn">
                        ${viewBtn}
                        ${evaluateBtn}
                        ${printBtnStyled}
                        <button class="hhi-btn hhi-btn-secondary icon-only row-toggle">
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>

                </div>
            `;
            }
        }
    ]
});

window.table = table;

/* ===============================
   FILTER MODULES
================================ */
// Export functions for filter modules to use
window.stageFilter = stageFilter;
window.getCurrentYear = () => state.year;
window.getPendingYear = () => pending.year;

// Initialize filters
setupYearFilterRecords( state.year === 'all' ? null : state.year);
setupStatusFilterRecords(state.status);


/* ===============================
   ROW EXPAND
================================ */
$("#records-table tbody").on("click", ".row-toggle", function (e) {
    e.stopPropagation();

    const tr   = $(this).closest("tr");
    const row  = table.row(tr);
    const icon = $(this).find("i");

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass("shown");
        icon.removeClass("rotate-180");
    } else {
        row.child(formatExpandedRow(row.data())).show();
        tr.addClass("shown");
        icon.addClass("rotate-180");
    }
});



/* ===============================
   EDIT FUNCTIONALITY
================================ */
let originalRecordData = {};

// Toggle edit mode
$(document).on('click', '.toggle-edit', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const recordId = $(this).data('record-id');
    const $row = $(this).closest('.record-edit-container');

    // Enable inputs
    $row.find('.record-field')
        .prop('disabled', false)
        .addClass('is-editing');

    // Enable radios
    $row.find('.risk-field')
        .removeClass('radio-readonly')
        .addClass('radio-editable');

    $row.find('.risk-field').closest('label')
        .removeClass('radio-readonly')
        .addClass('radio-editable');

    // Helper
    function getRadioBool($row, field) {
        return $row.find(`input[data-field="${field}"]:checked`).val() === '1';
    }

    // Store original values
    originalRecordData[recordId] = {
        cholesterol: $row.find('[data-field="cholesterol"]').val(),
        hdl_cholesterol: $row.find('[data-field="hdl_cholesterol"]').val(),
        systolic_bp: $row.find('[data-field="systolic_bp"]').val(),
        fbs: $row.find('[data-field="fbs"]').val(),
        hba1c: $row.find('[data-field="hba1c"]').val(),
        hypertension: getRadioBool($row, 'hypertension'),
        diabetes: getRadioBool($row, 'diabetes'),
        smoking: getRadioBool($row, 'smoking'),
    };

    // Toggle buttons
    $(this).addClass('hidden');
    $row.find('.save-record-btn').removeClass('hidden');
    $row.find('.cancel-edit-btn').removeClass('hidden');
});


// Cancel edit
$(document).on('click', '.cancel-edit-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const recordId = $(this).data('record-id');
    const $row = $(this).closest('.record-edit-container');
    const original = originalRecordData[recordId];

    function restoreRadio($row, field, originalValue) {
        const target = originalValue ? '1' : '0';
        $row.find(`input[data-field="${field}"]`)
            .prop('checked', false)
            .filter(`[value="${target}"]`)
            .prop('checked', true);
    }

    if (original) {
        $row.find('[data-field="cholesterol"]').val(original.cholesterol);
        $row.find('[data-field="hdl_cholesterol"]').val(original.hdl_cholesterol);
        $row.find('[data-field="systolic_bp"]').val(original.systolic_bp);
        $row.find('[data-field="fbs"]').val(original.fbs);
        $row.find('[data-field="hba1c"]').val(original.hba1c);

        restoreRadio($row, 'hypertension', original.hypertension);
        restoreRadio($row, 'diabetes', original.diabetes);
        restoreRadio($row, 'smoking', original.smoking);
    }

    // Disable inputs & restore theme colors
    $row.find('.record-field')
        .prop('disabled', true)
        .removeClass('is-editing');

    // Restore radios to readonly
    $row.find('.risk-field')
        .addClass('radio-readonly')
        .removeClass('radio-editable');

    $row.find('.risk-field').closest('label')
        .addClass('radio-readonly')
        .removeClass('radio-editable');

    // Toggle buttons
    $row.find('.toggle-edit').removeClass('hidden');
    $row.find('.save-record-btn').addClass('hidden');
    $row.find('.cancel-edit-btn').addClass('hidden');

    delete originalRecordData[recordId];
});


// Save record
$(document).on('click', '.save-record-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();

    const recordId = $(this).data('record-id');
    const $row = $(this).closest('.record-edit-container');
    const $btn = $(this);


    function boolFromRadio($row, field) {
        const val = $row.find(`input[data-field="${field}"]:checked`).val();
        return val === '1';
    }

    function numOrNull(v) {
        const n = parseFloat(v);
        return Number.isFinite(n) ? n : null;
    }

    // Collect data
    const data = {
        cholesterol: numOrNull($row.find('[data-field="cholesterol"]').val()),
        hdl_cholesterol: numOrNull($row.find('[data-field="hdl_cholesterol"]').val()),
        systolic_bp: numOrNull($row.find('[data-field="systolic_bp"]').val()),
        fbs: numOrNull($row.find('[data-field="fbs"]').val()),
        hba1c: numOrNull($row.find('[data-field="hba1c"]').val()),
        hypertension: boolFromRadio($row, 'hypertension'),
        diabetes: boolFromRadio($row, 'diabetes'),
        smoking: boolFromRadio($row, 'smoking'),
    };


    // Show loading
    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Saving...');

    $.ajax({
        url: `/api/records/${recordId}`,
        type: 'PUT',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(data),
        success: () => {
            // Disable fields
            $row.find('.record-field').prop('disabled', true).removeClass('bg-white border-blue-300').addClass('bg-gray-50');
            $row.find('.risk-field')
                .addClass('radio-readonly')
                .removeClass('radio-editable');

            $row.find('.risk-field').closest('label')
                .addClass('radio-readonly')
                .removeClass('radio-editable');


            // Show/hide buttons
            $row.find('.toggle-edit').removeClass('hidden');
            $row.find('.save-record-btn').addClass('hidden');
            $row.find('.cancel-edit-btn').addClass('hidden');

            // Reload table to reflect changes
            table.ajax.reload(null, false);

            delete originalRecordData[recordId];
        },
        error: (xhr) => {
            const errorMsg = xhr.responseJSON?.error || 'Failed to save record';
            alert(errorMsg);
            $btn.prop('disabled', false).html('<i class="fa-solid fa-save mr-1"></i> Save');
        }
    });
});

/* ===============================
   VIEW GENERATED TEXT MODAL
================================ */

function setSection(data){


    const name = [
        data.patient.first_name ? data.patient.first_name : " " ,
        data.patient.middle_name ? data.patient.middle_name : " ",
        data.patient.last_name ? data.patient.last_name : " " ,
        data.patient.suffix ? data.patient.suffix : " "
    ]
        .filter(Boolean)
        .join(' ');

    $(`.xte`).removeClass("opacity-50");
    $(`#template-date`).text(data.created_at);
    $(`#template-name`).text(name);
    $(`#template-unit`).text(data.patient.unit);
    $(`#template-dob`).text(data.patient.birthday);
    $(`#template-age`).text(data.patient.age);
    $(`#template-weight`).text(data.patient.weight);
    $(`#template-height`).text(data.patient.height);
    $(`#template-bmi`).text(data.patient.bmi);
    $(`#template-contact`).text(data.patient.contact);

    $(`#template-cholesterol`).text(data.cholesterol + ' mg/dl');
    $(`#template-hdl`).text(data.hdl_cholesterol + ' mg/dl');
    $(`#template-bp`).text(data.systolic_bp +  ' mmHg');
    $(`#template-fbs`).text(data.fbs);
    $(`#template-hbac`).text(data.hba1c + '%');
}


function clearSection(){
    $(`.xte`).addClass("opacity-50");
    $(`#template-date`).text('');
    $(`#template-name`).text('');
    $(`#template-unit`).text('');
    $(`#template-dob`).text('');
    $(`#template-age`).text('');
    $(`#template-weight`).text('');
    $(`#template-height`).text('');
    $(`#template-bmi`).text('');
    $(`#template-contact`).text('');

    $(`#template-cholesterol`).text('');
    $(`#template-hdl`).text('');
    $(`#template-bp`).text('');
    $(`#template-fbs`).text('');
    $(`#template-hbac`).text('');
}

let originalPanelContent = ``;
$("#closeGeneratedPanel").on("click", () => {
    originalPanelContent = ` `;
    closeGeneratedPanel();
});

function closeGeneratedPanel() {
    clearSection();
    $("#generatedPanel").addClass("translate-x-full");
    $(`.view-generated-btn`).html('<i class="fa-solid fa-magnifying-glass"></i>');
}
$(document).on('click', '.view-generated-btn', function(e) {
    e.stopPropagation();
    $('.hhi-btn-view').prop('disabled', true);

    $(this)
        .html('<i class="fa-solid fa-spinner fa-spin mr-1"></i>').prop('disabled' , true);

    const $tr = $(this).closest("tr");
    const rowData = table.row($tr).data();
    const generatedId = $(this).data('id');

    setSection(rowData);

    $("#panelRecordId").text(`Generated ID #${generatedId}`);


    fetch(`/api/getGeneratedContent/${generatedId}`)
        .then(res => res.json())
        .then(res => {
            originalPanelContent = res.generated_text || "No generated content.";
            $(`#content`).html(originalPanelContent);
            openGeneratedPanel();
            $('.hhi-btn-view').prop('disabled', false);
            $(this)
                .html('<i class="fa-solid fa-magnifying-glass"></i>');
        })
        .catch(() => {
            $("#panelContent").text("Failed to load content.");
        })
        .finally(() => {
            setPanelLoading(false);
        });

});

function openGeneratedPanel() {
    $(`#panelEditBtn`).hide();
    $("#generatedPanel").removeClass("translate-x-full");
}

function setPanelLoading(isLoading) {
    $("#panelSkeleton").toggle(isLoading);
    $("#panelContent").toggleClass("opacity-50", isLoading);
}

$('#closeModal').on('click', () => {
    $('#reportModal').addClass('hidden');
});

$('#reportModal').on('click', function(e) {
    if (e.target === this) {
        $(this).addClass('hidden');
    }
});

/* ===============================
   EVALUATE RECORD
================================ */


$(document).on('click', '.evaluate-btn', async function (e) {
    e.stopPropagation();

    const recordId = $(this).data('record-id');
    const $btn = $(this);

    // Prevent double-click on same row
    if (generatingRecords.has(recordId)) return;

    generatingRecords.add(recordId);

    // 🔥 IMMEDIATE UI FEEDBACK (same as your old behavior)
    $(`#generateBtn-${recordId}`).addClass("is-active").removeClass("hidden");
    $(`#actionsBtn-${recordId}`).addClass("is-hidden");

    $btn.prop('disabled', true)
        .html('<i class="fa-solid fa-spinner fa-spin"></i>');

    try {
        await $.ajax({
            url: `/api/evaluate/${recordId}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // job finished
        generatingRecords.delete(recordId);

        // restore UI
        $(`#generateBtn-${recordId}`).removeClass("is-active").addClass("hidden");
        $(`#actionsBtn-${recordId}`).removeClass("is-hidden");

        // 🔄 reload AFTER completion (won’t block others)
        table.ajax.reload(null, false);

    } catch (xhr) {
        generatingRecords.delete(recordId);

        const errorMsg =
            xhr.responseJSON?.error ||
            xhr.responseJSON?.message ||
            'Failed to generate evaluation';

        alert(errorMsg);

        // restore UI on error
        $(`#generateBtn-${recordId}`).removeClass("is-active").addClass("hidden");
        $(`#actionsBtn-${recordId}`).removeClass("is-hidden");

        $btn.prop('disabled', false)
            .html('<i class="fa-solid fa-brain"></i>');
    }
});


/* ===============================
   INIT
================================ */

$searchInput.on("input", () => {
    const searchTerm = $searchInput.val().trim();
    stageFilter("search", searchTerm);
});

$(document).on(
    "click",
    ".year-filter-dropdown-item, #year-filter-menu .dropdown-item",
    function () {
        const value = $(this).data("value");
        stageFilter("year", value);
    }
);


$(document).on(
    "click",
    ".status-filter-dropdown-item, #status-filter-menu .dropdown-item",
    function () {
        const status = $(this).data("value");
        stageFilter("status", status);
    }
);
