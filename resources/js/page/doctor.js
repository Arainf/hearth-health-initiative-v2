import $ from "jquery";
import DataTable from "datatables.net-dt";
import { setupYearFilterRecords } from "@/filters/filter-year.js";
import { setupStatusFilterRecords } from "@/filters/filter-status.js";
import { createTiptapEditor } from "@/tip-tap/index.js";
import { formatExpandedRow } from "@/utilities/table-expanded-form.js";
import { createIcons, icons} from "lucide";

let reportEditor = null

window.$ = window.jQuery = $;

const user_id = document.body.dataset.user;
const generatingRecords = new Set();



function showSkeleton(){
    $('#approved-skeleton').removeClass('hidden');
    $('#approved-content').addClass('opacity-0');
    $('#not-evaluated-skeleton').removeClass('hidden');
    $('#not-evaluated-content').addClass('opacity-0');
    $('#pending-skeleton').removeClass('hidden');
    $('#pending-content').addClass('opacity-0');
}

function hideSkeleton(){
    $('#approved-skeleton').addClass('hidden');
    $('#approved-content').removeClass('opacity-0');
    $('#not-evaluated-skeleton').addClass('hidden');
    $('#not-evaluated-content').removeClass('opacity-0');
    $('#pending-skeleton').addClass('hidden');
    $('#pending-content').removeClass('opacity-0');
}


let statusFilterValue = null;
let yearFilterValue =  new Date().getFullYear();
$(document).on('click', '.status-filter-dropdown-item', function () {
    statusFilterValue = $(this).data('value');
});
$(document).on('click', '.year-filter-dropdown-item', function () {
    yearFilterValue = $(this).data('value');
});

/* ===============================
   DATATABLE INIT
================================ */
const table = $("#records-table").DataTable({
    serverSide: true,
    processing: true,
    pageLength: 20,

    scrollY: "calc(100vh - 400px)",
    scrollCollapse: true,

    autoWidth: true,
    paging: true,
    info: true,
    lengthChange: false,
    language: {
        infoFiltered: ""
    },
    dom: `
        <"datatable-wrapper"
            <"datatable-body" t>
            <"datatable-footer"i p>
        >
    `,

    ajax: {
        url: "/table/" + window.page.token,
        type: "GET",
        data: d => {
            d.search = $('#record-search').val();
            d.status = statusFilterValue || 'all';
            d.year   = yearFilterValue;
            d.unit = $('#unit_office').val();
            showSkeleton()
        },

        dataSrc: function (json) {
            hideSkeleton()
            if (json.statuses) {

                let pending = 0;
                let notEvaluated = 0;
                let approved = 0;



                json.statuses.forEach(status => {
                    const count = status.count || 0;
                    const name = status.status_name?.toLowerCase() || '';

                    if (name === 'pending') {
                        pending = count;
                    } else if (name === 'not evaluated') {
                        notEvaluated = count;
                    } else if (name === 'approved') {
                        approved = count;
                    }
                });

                $("#pending-count").text(pending);
                $("#not-evaluated-count").text(notEvaluated);
                $("#approved-count").text(approved);
            }

            $('#pendingYear , #evaluatedYear , #approveYear').text(yearFilterValue);
            $('#pendingUnit, #evaluateUnit, #approveUnit')
                .text($('#unit_office').val() || 'All Units');


            return json.data;
        },

    },


    columnDefs: [
        { targets: 0, width: "35%" },
        { targets: 1, width: "15%" },
        { targets: 2, width: "15%" },
        { targets: 3, width: "10%" },
        { targets: 4, width: "20%" },
        { targets: 5, width: "20%" }
    ],

    columns: [
        { data: "patient" },
        { data: "unit" },
        { data: "staff" },
        { data: "created_at" },
        { data: "status", orderable: false },
        { data: "actions", orderable: false, className: "text-center" }
    ]


});

$('#records-table').on('draw.dt', function () {
    createIcons({ icons });
});

window.table = table;
$(document).on(
    "click",
    "#search-button",
    function () {
        table.ajax.reload();
    }
);

$(document).on(
    "click",
    "#reset-filters",
    function () {
        const currentYear = new Date().getFullYear();

        $("#record-search").val('');
        $("#status-filter-label").text('All');
        $("#year-filter-label").text(currentYear);
        $('#unit_office').val('').trigger('change');

        statusFilterValue = 'all';
        yearFilterValue = currentYear;
        table.ajax.reload();
    }
);



setupYearFilterRecords( yearFilterValue);
setupStatusFilterRecords(statusFilterValue);



/* ===============================
   ROW EXPAND
================================ */
$("#records-table tbody").on("click", ".row-toggle", function (e) {
    e.stopPropagation();

    const button = $(this);
    const tr = button.closest("tr");
    const row = table.row(tr);
    const url = button.data("url");

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass("shown");

        button.html(`<i data-lucide="chevron-down" class="w-4 h-4"></i>`);
        createIcons({ icons });
        return;
    }

    if (button.data("loading")) return;

    button.data("loading", true);

    // LOADER
    button.html(`
        <i data-lucide="loader" class="w-4 h-4 animate-spin"></i>
    `);
    createIcons({ icons });

    fetch(url)
        .then(res => res.json())
        .then(data => {
            row.child(formatExpandedRow(data)).show();
            tr.addClass("shown");

            // Restore chevron rotated
            button.html(`
                <i data-lucide="chevron-down" class="w-4 h-4 rotate-180"></i>
            `);
            createIcons({ icons });
        })
        .catch(err => console.error(err))
        .finally(() => {
            button.data("loading", false);
        });
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
    const mode = $(this).data('mode');
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
        url: "/update/" +  window.page.token,
        type: 'PUT',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            id: recordId,
            mode: mode,
            ...data
        }),
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
   EVALUATE RECORD
================================ */
let evaluationQueue = [];

$(document).on('click', '.evaluate-btn', async function (e) {
    e.stopPropagation();

    const recordId = $(this).data('record-id');
    const recordMode = $(this).data('record-mode');
    const $btn = $(this);

    // Prevent double-click on same row
    if (generatingRecords.has(recordId)) return;

    generatingRecords.add(recordId);

    // 🆕 Add to queue
    evaluationQueue.push(recordId);
    updateQueueBadges();

    // 🔥 Immediate UI Feedback
    $(`#generateBtn-${recordId}`).addClass("is-active").removeClass("hidden");
    $(`#actionsBtn-${recordId}`).addClass("is-hidden");

    // Make button relative for badge positioning
    $btn.addClass('relative');

    // Spinner
    $btn.prop('disabled', true)
        .html(`
            <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
        `);

    createIcons({ icons });

    try {
        await $.ajax({
            url: `/store/` + window.page.token,
            method: 'POST',
            contentType: "application/json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
            },
            data: JSON.stringify({
                id: recordId,
                mode: recordMode
            })
        });

        finishEvaluation(recordId, $btn);

        // Reload table without resetting pagination
        table.ajax.reload(null, false);

    } catch (xhr) {

        const errorMsg =
            xhr.responseJSON?.error ||
            xhr.responseJSON?.message ||
            'Failed to generate evaluation';

        alert(errorMsg);

        finishEvaluation(recordId, $btn, true);
    }
});

function finishEvaluation(recordId, $btn, isError = false) {

    generatingRecords.delete(recordId);

    // Remove from queue
    evaluationQueue = evaluationQueue.filter(id => id !== recordId);

    // Restore UI
    $(`#generateBtn-${recordId}`).removeClass("is-active").addClass("hidden");
    $(`#actionsBtn-${recordId}`).removeClass("is-hidden");

    // Restore button icon
    $btn.prop('disabled', false)
        .html(`
            <i data-lucide="brain" class="w-4 h-4"></i>
        `);

    createIcons({ icons });

    updateQueueBadges();
}

function updateQueueBadges() {

    $('.evaluate-btn').each(function () {

        const $btn = $(this);
        const id = $btn.data('record-id');
        const index = evaluationQueue.indexOf(id);

        // Remove old badge
        $btn.find('.eval-order-badge').remove();

        if (index !== -1) {
            $btn.append(`
                <span class="eval-order-badge absolute -top-1 -right-1
                             bg-red-500 text-white text-[10px] font-semibold
                             rounded-full w-4 h-4 flex items-center justify-center">
                    ${index + 1}
                </span>
            `);
        }
    });
}



/**
 * Open Panel Logic
 * */

$(document).on("click", ".view-generated-btn", function (e) {
    e.stopPropagation();

    const btn = $(this);

    $('.hhi-btn-view').prop('disabled', true);

    btn
        .addClass('is-loading')
        .html(`
            <i data-lucide="loader" class="w-4 h-4 animate-spin"></i>
            <span class="ml-1">Preparing</span>
        `)
        .prop('disabled', true);

    createIcons({ icons }); // re-render lucide icons


    const url = btn.data('url');


    setPanelLoading(true);
    contentFillers(false);

    fetch(url)
        .then(res => res.json())
        .then(res => {
            originalPanelContent = res.generated_text || "No generated content.";
            reportEditor.setContent(originalPanelContent);
            currentRecordId = res.record_id;
            modeSave = res.mode_save;
            modeSaveApprove = res.mode_save_and_approve;
            setSection(res);
            openGeneratedPanel();

            const isApproved = res.status_id === 1;
            if (isApproved) {
                $("#panelSaveApproveBtn").addClass("hidden").prop('disabled', true);
            } else {
                $("#panelSaveApproveBtn").removeClass("hidden").prop('disabled', false);
            }

            $('.hhi-btn-view').prop('disabled', false);

            btn
                .removeClass('is-loading')
                .html(`<i data-lucide="search" class="w-4 h-4"></i>`)
                .prop('disabled', false);

            createIcons({ icons }); // re-render icon again
        })
        .catch(() => {
            $("#panelContent").text("Failed to load content.");

            btn
                .removeClass('is-loading')
                .html(`<i data-lucide="search" class="w-4 h-4"></i>`)
                .prop('disabled', false);

            createIcons({ icons });
        });
});



$("#panelSaveBtn").on("click", function () {
    if (!currentRecordId) return;

    const $btn = $(this);
    const content = reportEditor.getHTML();

    if (content.trim() === originalPanelContent.trim()) {
        alert("No changes to save.");
        return;
    }

    $btn.prop("disabled", true).text("Saving…");

    $.ajax({
        url: `/store/` + window.page.token,
        method: "POST",
        contentType: "application/json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        data: JSON.stringify({ content, id : currentRecordId, mode : modeSave }), // ✅ payload in request body
        success: function (data) {

            if (!data.success && !data.updated) {
                alert("Server did not confirm save.");
                return;
            }

            originalPanelContent = content;
            closeGeneratedPanel();

            showSuccess("Saved", "Generated report updated.");
            table.ajax.reload(null, false);
        },
        error: function (xhr) {
            console.error("Save error:", xhr);
            alert("Failed to save report. Please try again.");
        },
        complete: function () {
            $btn.prop("disabled", false).text("Save");
        }
    });
});

$("#panelSaveApproveBtn").on("click", function () {

    if (!currentRecordId) return;

    const $btn = $(this);
    const content = reportEditor.getHTML();

    $btn.prop("disabled", true).text("Saving & approving…");

    $.ajax({
        url: `/store/` + window.page.token,
        method: "POST",
        contentType: "application/json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        data: JSON.stringify({
            id: currentRecordId,
            mode: modeSaveApprove,
            approved: user_id,
            content: content
        }),

        success: function (response) {

            if (!response.success) {
                alert(response.error || "Operation failed.");
                return;
            }

            closeGeneratedPanel();
            showSuccess("Approved", "Record saved and approved.");
            $("#panelFooter").addClass("hidden");
            table.ajax.reload();
        },

        error: function (xhr) {
            console.error("Save & approve error:", xhr);
            alert("Failed to save and approve.");
        },

        complete: function () {
            $btn.prop("disabled", false).text("Save & Approve");
        }
    });
});
/* ==========================================
   IMPORT & TEMPLATE MODAL LOGIC (jQuery)
   ========================================== */

// Handle opening modals
$('#btnOpenTemplate').on('click', function() {
    $('#modalTemplate').removeClass('hidden').addClass('flex');
});

$('#btnOpenImport').on('click', function() {
    $('#modalImport').removeClass('hidden').addClass('flex');
    resetImportModal();
});

// Close modals
$('.close-modal').on('click', function() {
    $(this).closest('.fixed').addClass('hidden').removeClass('flex');
});

// Click outside to close
$(window).on('click', function(e) {
    if ($(e.target).hasClass('fixed')) {
        $(e.target).addClass('hidden').removeClass('flex');
    }
});

function resetImportModal() {
    $('#import_step_1').removeClass('hidden');
    $('#import_step_2').addClass('hidden');
    $('#import_loading').addClass('hidden');
    // Shrink modal back to original size
    $('#importModalContainer').removeClass('w-[95vw] max-w-7xl h-[85vh]').addClass('w-[520px]');
    $('#import_file').val('');
    $("#validate_import").prop("disabled", true);
}

// --- Validation & Export Logic ---

$("#export_template").prop("disabled", true);
$("#validate_import").prop("disabled", true);

$("#unit_office_template").on("change", function () {
    if ($(this).val() === "") {
        $("#export_template").prop("disabled", true);
    } else {
        $("#export_template").prop("disabled", false);
    }
});

$("#import_file").on("change", function () {
    if ($(this).val() === "") {
        $("#validate_import").prop("disabled", true);
    } else {
        $("#validate_import").prop("disabled", false);
    }
});

$('#export_template').on('click', function () {
    const unitCode = $('#unit_office_template').val();
    if (!unitCode) return;

    const mode = $(this).data("mode");
    const url = `/page/${window.page.token}?mode=${mode}&unit_code=${unitCode}`;

    window.open(url, '_blank');
});

$('#validate_import').on('click', function () {
    const fileInput = $('#import_file')[0].files[0];
    if (!fileInput) {
        alert('Please select a file.');
        return;
    }

    const mode = $(this).data("mode");
    const token = window.page.token;
    const url = `/store/${token}?mode=${mode}`;

    let formData = new FormData();
    formData.append("file", fileInput);

    const $btn = $(this);
    $btn.html(`<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span class="ml-1">Validating</span>`)
        .prop('disabled', true);

    createIcons({ icons });

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $btn.html(`<span class="ml-1">Validate File</span>`).prop('disabled', false);
            createIcons({ icons });

            // ✅ Update summary
            $("#valid_rows").text(response.valid_rows);
            $("#invalid_rows").text(response.invalid_rows);

            // ✅ Build preview table
            let html = "";
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row) {
                    html += `
                        <tr class="hover:bg-gray-50 h-[50px] divide-x">
                            <td class="px-2 py-1 border-b text-center">${row.row ?? ''}</td>
                            <td class="px-2 py-1 border-b font-medium whitespace-nowrap">${row.full_name ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.birthday ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.sex ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.weight ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.height ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.phone_number ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.hypertension ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.diabetes_mellitus ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.heart_attack_under_60y ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.cholesterol ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.total_cholesterol ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.hdl_cholesterol ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.systolic_bp ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.fbs ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.hba1c ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.hypertension_tx ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.diabetes_m ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.smoking ?? ''}</td>
                            <td class="px-2 py-1 border-b">${row.date_record ?? ''}</td>
                        </tr>
                    `;
                });
            } else {
                html = `<tr><td colspan="21" class="text-center py-3 text-gray-500">No valid rows to preview.</td></tr>`;
            }

            $("#preview_table_body").html(html);

            // ✅ Switch to step 2 & Expand Modal
            $("#import_step_1").addClass("hidden");
            $("#import_step_2").removeClass("hidden");

            // Replaces Alpine "expand_modal" logic
            $('#importModalContainer')
                .removeClass('w-[520px]')
                .addClass('w-[95vw] max-w-7xl h-[85vh]');

            $("#import_loading").addClass("hidden");
        },
        error: function (xhr) {
            alert(xhr.responseJSON?.message || "Import failed.");
            $btn.html(`<span class="ml-1">Validate File</span>`).prop('disabled', false);
            createIcons({ icons });
            $("#import_loading").addClass("hidden");
        }
    });
});

$('#back_to_upload').on('click', function () {
    $("#import_step_2").addClass("hidden");
    $("#import_step_1").removeClass("hidden");

    $("#validate_import").html(`<span class="ml-1">Validate File</span>`).prop('disabled', false);

    // Shrink modal back
    $('#importModalContainer').removeClass('w-[95vw] max-w-7xl h-[85vh]').addClass('w-[520px]');
});

$('#confirm_import').on('click', function () {
    const fileInput = $('#import_file')[0].files[0];
    if (!fileInput) return;

    const mode = $(this).data("mode");
    const token = window.page.token;
    const url = `/store/${token}?mode=${mode}`;

    let formData = new FormData();
    formData.append("file", fileInput);

    const $btn = $(this);
    $("#import_loading").removeClass("hidden");
    $btn.html(`<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span class="ml-1">Saving</span>`)
        .prop('disabled', true);

    createIcons({ icons });

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
        },
        success: function () {
            location.reload();
        },
        error: function () {
            alert("Saving failed.");
            $btn.html(`<span class="ml-1">Confirm & Save</span>`).prop('disabled', false);
            createIcons({ icons });
            $("#import_loading").addClass("hidden");
        }
    });
});
