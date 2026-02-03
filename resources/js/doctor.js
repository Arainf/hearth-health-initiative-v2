import $ from "jquery";
import DataTable from "datatables.net-dt";
import { setupYearFilterRecords } from "@/filters/filter-year.js";
import { setupStatusFilterRecords } from "@/filters/filter-status.js";
import { createTiptapEditor } from "@/tip-tap/index.js";
import { formatExpandedRow } from "@/utilities/table-expanded-form.js";
import { loadAiStatus } from "@/utilities/ai-status.js";


let reportEditor = null

window.$ = window.jQuery = $;

let aiAccess = false;
let aiReady = false;
let table;

async function loadAiStatus() {
    try {
        const response = await fetch("/api/ai/status", {
            headers: {
                Accept: "application/json"
            }
        });

        if (!response.ok) {
            console.warn("Failed to fetch AI status.");
            return;
        }

        const data = await response.json();
        aiAccess = data.ai_access === true || data.ai_access === 1;
        aiReady = data.ai_ready === true || data.ai_ready === 1;
    } catch (error) {
        console.warn("Unable to load AI status.", error);
    }
}
const user_id = document.body.dataset.user;

// Current record being edited/approved
let originalContent = '';
let changeContent = '';
let isEditMode = false;

/* ===============================
   STATE & URL MANAGEMENT
================================ */
const state = {
    status: new URLSearchParams(window.location.search).get('status') || 'all',
    year: new URLSearchParams(window.location.search).get('year') || new Date().getFullYear(),
    search: new URLSearchParams(window.location.search).get('search') || ''
};

const originalState = {
    status: state.status,
    year: state.year,
    search: state.search
};


const generatingRecords = new Set();

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
   SIDE PANEL
================================ */

let currentGeneratedId = null;
let currentRecordId = null;
let originalPanelContent = ``;
let panelEditMode = false;


document.addEventListener('DOMContentLoaded', () => {
    reportEditor = createTiptapEditor({
        element: document.querySelector('[x-ref="editor"]'),
        content: originalPanelContent,
        editable: false,
    });

    // ✅ expose ONLY what the UI needs
    window.ReportEditor = {
        undo: () => reportEditor.undo(),
        redo: () => reportEditor.redo(),
        toggleBold: () => reportEditor.toggleBold(),
        toggleItalic: () => reportEditor.toggleItalic(),
        toggleUnderline: () => reportEditor.toggleUnderline(),
        toggleStrike: () => reportEditor.toggleStrike(),
        toggleHeading: l => reportEditor.toggleHeading(l),
        toggleSize: s => reportEditor.toggleSize(s),
        toggleBulletList: () => reportEditor.toggleBulletList(),
        toggleOrderedList: () => reportEditor.toggleOrderedList(),
        setAlign: a => reportEditor.setAlign(a),
        setEditable: v => reportEditor.setEditable(v),
        getHTML: () => reportEditor.getHTML(),
        isActive: (a, opts) => reportEditor.isActive(a, opts),
        canUndo: () => reportEditor.canUndo(),
        canRedo: () => reportEditor.canRedo(),
        setContent: html => reportEditor.setContent(html),
    };
});


$(`.prose`).on('change', function (){
    changeContent = $(this).getContent();
    console.log(originalContent);
    console.log(changeContent);
    if(originalContent !== changeContent){
        console.log("Not Equal")
        console.log(originalContent);
        console.log(changeContent);
    }
});


function openGeneratedPanel() {
    $("#generatedPanel").removeClass("translate-x-full");
}

function closeGeneratedPanel() {
    clearSection();
    $("#generatedPanel").addClass("translate-x-full");
    $(`.view-generated-btn`).html('<i class="fa-solid fa-magnifying-glass"></i>');
    contentFillers(false);
    editBtnState(true);
    panelEditMode = false;
    currentGeneratedId = null;
    currentRecordId = null;
}
function contentFillers(enabled) {
    $("#panelContent").attr("contenteditable", enabled).html(originalPanelContent);
    // $("#panelFooter").toggleClass("hidden", !enabled);
}

function editBtnState(isEditing) {
    isEditMode = !isEditing;

    reportEditor.setEditable(isEditMode);

    // $("#panelFooter").toggleClass("hidden", !isEditMode);
    if (isEditMode) {
        $("#editorToolbar")
            .css("display", "flex");
    } else {
        $("#editorToolbar")
            .css("display", "none");
    }


    $("#panelEditBtn").html(
        isEditMode
            ? '<i class="fa-solid fa-times mr-1"></i> Cancel'
            : '<i class="fa-solid fa-edit mr-1"></i> Edit'
    );

    // CANCEL → restore original
    if (!isEditMode) {
        reportEditor.setContent(originalPanelContent);
    }
}


$("#panelEditBtn").on("click", function() {
    editBtnState(isEditMode);
});

$("#closeGeneratedPanel").on("click", () => {
    originalPanelContent = ` `;
    closeGeneratedPanel();
});

$("#saveAndApproveBtn").on("click", function() {
    $("#approveModal").removeClass("hidden");
});

$("#cancelApproveBtn").on("click", function() {
    $("#approveModal").addClass("hidden");
});

function showSuccess(title, message) {
    $("#successTitle").text(title);
    $("#successMessage").text(message);
    $("#successModal").removeClass("hidden");
}

$("#closeSuccessBtn").on("click", function() {
    $("#successModal").addClass("hidden");
});

$("#closeModal").on("click", function() {
    $("#reportModal").addClass("hidden");
    currentRecordId = null;
    originalContent = '';
    isEditMode = false;
});

$("#cancelEditBtn").on("click", function() {
    $("#editToggleBtn").click();
});


$("#reportModal").on("click", function(e) {
    if (e.target === this) {
        $(this).addClass("hidden");
        currentRecordId = null;
        originalContent = '';
        isEditMode = false;
    }
});


function setPanelLoading(isLoading) {
    $("#panelSkeleton").toggle(isLoading);
    $("#panelContent").toggleClass("opacity-50", isLoading);
}

/* ===============================
   VIEW/EDIT GENERATED TEXT
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

$(document).on("click", ".view-generated-btn", function (e) {
    e.stopPropagation();
    $('.hhi-btn-view').prop('disabled', true);

    $(this)
        .addClass('is-loading')
        .html('<i class="fa-solid fa-spinner fa-spin mr-1"></i><span>Preparing</span>').prop('disabled' , true);

    const $tr = $(this).closest("tr");
    const rowData = table.row($tr).data();

    setSection(rowData);
    currentGeneratedId = rowData.generated_id;
    currentRecordId = rowData.id


    $("#panelRecordId").text(`Generated ID #${currentGeneratedId}`);

    setPanelLoading(true);
    contentFillers(false);

    fetch(`/api/getGeneratedContent/${currentGeneratedId}`)
        .then(res => res.json())
        .then(res => {
            originalPanelContent = res.generated_text || "No generated content.";
            reportEditor.setContent(originalPanelContent);

            openGeneratedPanel();
            const isApproved = res.status_id === 1;
            if (isApproved) {
                $("#panelSaveApproveBtn").addClass("hidden").prop('disabled', true);
            } else {
                $("#panelSaveApproveBtn").removeClass("hidden").prop('disabled', false);
            }

            $('.hhi-btn-view').prop('disabled', false);
            $(this)
                .removeClass('is-loading')
                .html('<i class="fa-solid fa-magnifying-glass"></i>');

        })
        .catch(() => {
            $("#panelContent").text("Failed to load content.");
        })
        .finally(() => {
            setPanelLoading(false);
        });
});


// Save only
$("#panelSaveBtn").on("click", async function () {
    if (!currentRecordId) return;

    const $btn = $(this);
    // const content = $("#panelContent").text();
    const content = reportEditor.getHTML();

    if (content.trim() === originalPanelContent.trim()) {
        alert("No changes to save.");
        return;
    }

    try {
        $btn.prop("disabled", true).text("Saving…");

        const res = await fetch(`/api/saveRecord/${currentRecordId}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            body: JSON.stringify({ content })
        });

        if (!res.ok) {
            throw new Error(`Save failed (${res.status})`);
        }

        const data = await res.json();

        if (!data.success && !data.updated) {
            throw new Error("Server did not confirm save");
        }

        originalPanelContent = content;
        closeGeneratedPanel();

        showSuccess("Saved", "Generated report updated.");
        table.ajax.reload(null, false);

    } catch (err) {
        console.error("Save error:", err);
        alert("Failed to save report. Please try again.");
    } finally {
        $btn.prop("disabled", false).text("Save");
    }
});


// Save & approve
$("#panelSaveApproveBtn").on("click", async function () {
    if (!currentGeneratedId || !currentRecordId) return;

    const $btn = $(this);
    // const content = $("#panelContent").text();

    const content = reportEditor.getHTML();

    try {
        $btn.prop("disabled", true).text("Saving & approving…");

        // 1️⃣ Save content
        const saveRes = await fetch(`/api/saveRecord/${currentRecordId}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            body: JSON.stringify({ content })
        });

        if (!saveRes.ok) {
            throw new Error(`Save failed (${saveRes.status})`);
        }

        const saveData = await saveRes.json();

        if (!saveData.success && !saveData.updated) {
            throw new Error("Server did not confirm save");
        }

        // 2️⃣ Approve record
        const approveRes = await fetch(`/api/statusUpdate`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            body: JSON.stringify({
                id: currentRecordId,
                status: 1,
                approved: user_id
            })
        });

        if (!approveRes.ok) {
            throw new Error(`Approve failed (${approveRes.status})`);
        }

        closeGeneratedPanel();
        showSuccess("Approved", "Record saved and approved.");

        table.ajax.reload();
        loadStatusCounts();

    } catch (err) {
        console.error("Save & approve error:", err);
        alert("Failed to save and approve. Please try again.");
    } finally {
        $btn.prop("disabled", false).text("Save & Approve");
    }
});
/* ===============================
   LOADING
================================ */
function showLoading() {
    $("#tableLoading")
        .removeClass("hidden -z-10")
        .addClass("z-50");
}

function hideLoading() {
    $("#tableLoading")
        .addClass("hidden -z-10")
        .removeClass("z-50");
}

/* ===============================
   LOAD STATUS COUNTS
================================ */
function loadStatusCounts() {
    const year = state.year;
    const apiUrl = year !== 'all'
        ? `/api/getStatusCount?year=${year}&archived=false`
        : `/api/getStatusCount?archived=false`;

    fetch(apiUrl)
        .then(res => res.json())
        .then(data => {
            let pending = 0;
            let notEvaluated = 0;
            let approved = 0;

            data.forEach(status => {
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
        })
        .catch(err => {
            console.error('Error loading status counts:', err);
        });
}


/* ===============================
   FILTER BUTTON STATE
================================ */
const $filterBtn = $("#reset-filters");
const $searchIcon = $("#filter-search-icon");
const $resetIcon = $("#filter-reset-icon");
const $searchInput = $("#record-search");

function setFilterButtonMode(mode) {
    if (mode === "reset") {
        $filterBtn
            .attr("data-mode", "reset")
            .removeClass("bg-blue-600 hover:bg-blue-700 border-blue-600 text-white")
            .addClass("bg-gray-100 hover:bg-gray-200 border-gray-300 text-gray-700");

        $searchIcon.addClass("hidden").css("display", "none");
        $resetIcon.removeClass("hidden").css("display", "");
    } else {
        $filterBtn
            .attr("data-mode", "search")
            .removeClass("bg-gray-100 hover:bg-gray-200 border-gray-300 text-gray-700")
            .addClass("bg-blue-600 hover:bg-blue-700 border-blue-600 text-white");

        $resetIcon.addClass("hidden").css("display", "none");
        $searchIcon.removeClass("hidden").css("display", "");
    }
}

function hasActiveFilters() {
    const currentYear = new Date().getFullYear();

    return (
        (state.status && state.status !== 'all') ||
        (state.year && state.year !== currentYear) ||
        (state.search && state.search.trim() !== '')
    );
}

function syncFilterButton() {
    // Show reset icon if filters are active, search icon if not
    setFilterButtonMode(hasActiveFilters() ? 'reset' : 'search');
}

function resetFilters() {
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
    syncFilterButton();

    // Reload dependent filters
    if (window.refreshStatusFilter) {
        window.refreshStatusFilter(currentYear);
    }

    table.ajax.reload();
}


function stageFilter(type, value) {
    pending[type] = value;
}

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

    // Update UI
    syncFilterButton();
    loadStatusCounts()
    // Apply filters to DataTable
    if (!table) {
        return;
    }
    table.ajax.reload();
}

function applyPendingFilters() {
    applyFilters({
        status: pending.status !== null ? pending.status : undefined,
        year: pending.year !== null ? pending.year : undefined,
        search: pending.search !== null ? pending.search : undefined
    });
}
/* ===============================
   DATATABLE INIT
================================ */
const initTable = async () => {
    await loadAiStatus();

    table = $("#records-table").DataTable({
    serverSide: true,
    processing: false,
    pageLength: 20,

    scrollY: "calc(100vh - 380px)",
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
        url: "/table/records",
        type: "GET",
        data: d => {
            d.search = state.search || '';
            d.status = state.status || Date.now();
            d.year   = state.year || 'all';
            loadStatusCounts();
            showLoading();
        },
        complete: hideLoading
    },

    columnDefs: [
        { targets: 0, width: "35%" },
        { targets: 1, width: "15%" },
        { targets: 2, width: "10%" },
        { targets: 3, width: "20%" },
        { targets: 4, width: "20%" }
    ],

    columns: [
        {
            data: "patient",
            render: function(p) {
                if (!p) return "—";
                return `
                    <div class="leading-tight">
                        <div class="font-medium text-gray-900 dark:text-white text-sm">
                            (${p.unit}) ${p.last_name}, ${p.first_name} ${p.middle_name ?? ""}
                        </div>
                        <div class="text-xs text-gray-500">${p.age} y.o.</div>
                    </div>
                `;
            }
        },
        {
            data: "status.status_name",
            orderable: false,
            className: "CENTER"
        },
        {
            data: "staff",
            render: function(data) {
                return data || "—";
            }
        },
        {
            data: "created_at",
            render: function(data) {
                if (!data) return "—";
                return new Date(data).toLocaleDateString("en-US", {
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                });
            }
        },
        {
            data: null,
            orderable: false,
            className: "text-center",
            render: r => {
                const hasGenerated = r.generated_id && r.generated_id !== null && r.generated_id !== '';
                const hasDoctorApproval = r.doctor  !== null && r.doctor !== '';


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
                    `<button class="hhi-btn hhi-btn-view icon-only view-generated-btn" ${!hasGenerated ? 'disabled' : ' '}
                            title="${!hasGenerated ? "No Evaluation" : 'View Evaluation'}"
                            data-id="${r.generated_id ? r.generated_id : ' '}">
                       <i class="fa-solid fa-magnifying-glass"></i>
                    </button>`


                // Evaluate button - purple (only if not generated)
                const evaluateBtn = !hasGenerated && aiAccess && aiReady
                    ? `<button class="hhi-btn hhi-btn-evaluate icon-only evaluate-btn"
                            title="Evaluate with AI"

                            data-record-id="${r.id}">
                        <i class="fa-solid fa-brain"></i>
                    </button>`
                    : '';



                const printBtnStyled = hasDoctorApproval && hasGenerated
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
};

initTable();


window.stageFilter = stageFilter;
window.applyPendingFilters = applyPendingFilters;
window.getCurrentYear = () => state.year;
window.getPendingYear = () => pending.year;

setupYearFilterRecords( state.year === 'all' ? null : state.year);
setupStatusFilterRecords(state.status);


$searchInput.off('input').on('input', () => {
    const searchTerm = $searchInput.val().trim();
    stageFilter('search', searchTerm);
});

$filterBtn.off('click').on('click', (e) => {
    e.preventDefault();
    const mode = $filterBtn.attr("data-mode");

    if (mode === "reset") {
        // Reset all filters
        resetFilters();
    } else {
        // Search mode - apply all pending filters
        applyPendingFilters();
    }
});

window.addEventListener('popstate', () => {
    const params = new URLSearchParams(window.location.search);
    const currentYear = new Date().getFullYear();

    applyFilters({
        status: params.get('status') || 'all',
        year: params.get('year') || currentYear,
        search: params.get('search') || ''
    });
});




$searchInput.on("input", () => {
    const searchTerm = $searchInput.val().trim();
    stageFilter("search", searchTerm);
    setFilterButtonMode(searchTerm.length > 0 ? 'search' : 'reset');

});

$(document).on(
    "click",
    ".year-filter-dropdown-item, #year-filter-menu .dropdown-item",
    function () {
        const value = $(this).data("value");

        stageFilter("year", value);

        setFilterButtonMode("search");

    }
);


$(document).on(
    "click",
    ".status-filter-dropdown-item, #status-filter-menu .dropdown-item",
    function () {
        const status = $(this).data("value");

        stageFilter("status", status);
        setFilterButtonMode("search");

    }
);



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
   EXPANDED ROW TEMPLATE
================================ */
// function formatExpandedRow(data) {
//     const recordId = data.id;
//     let extendedActions = ``;
//
//     if(!data.generated_id){
//         extendedActions =`
//             <!-- ACTIONS -->
//             <div id="extended-actions-${recordId}"  class="absolute top-3 right-3 flex gap-2 ">
//                 <button class="hhi-btn hhi-btn-outline text-xs toggle-edit" data-record-id="${recordId}">
//                     <i class="fa-solid fa-pen mr-1"></i> Edit
//                 </button>
//                 <button class="hhi-btn hhi-btn-primary text-xs hidden save-record-btn" data-record-id="${recordId}">
//                     Save
//                 </button>
//                 <button class="hhi-btn hhi-btn-secondary text-xs hidden cancel-edit-btn" data-record-id="${recordId}">
//                     Cancel
//                 </button>
//             </div>`
//     }
//
//     return `
//         <div class="bg-[var(--clr-surface-a10)] border border-[var(--clr-surface-a30)] rounded-lg p-4 text-sm relative record-edit-container"
//              data-record-id="${recordId}">
//            ${extendedActions}
//             <!-- MAIN GRID -->
//             <div class="grid gap-6" style="grid-template-columns: repeat(3, 1fr); ">
//
//                 <!-- LEFT: INPUTS (2 columns) -->
//                 <div class="col-span-2 grid grid-cols-2 gap-4">
//                     ${renderEditableInput("Cholesterol", "cholesterol", data.cholesterol, recordId)}
//                     ${renderEditableInput("HDL", "hdl_cholesterol", data.hdl_cholesterol, recordId)}
//
//                     ${renderEditableInput("Systolic BP", "systolic_bp", data.systolic_bp, recordId)}
//                     ${renderEditableInput("FBS", "fbs", data.fbs, recordId)}
//
//                     <div class="col-span-2">
//                         ${renderEditableInput("HbA1c", "hba1c", data.hba1c, recordId)}
//                     </div>
//                 </div>
//
//                 <!-- RIGHT: RISK FACTORS -->
//                 <div class="space-y-4 col-start-3">
//                     ${renderRiskRadio("Hypertension Tx", "hypertension", data.hypertension, recordId)}
//                     ${renderRiskRadio("Diabetes M", "diabetes", data.diabetes, recordId)}
//                     ${renderRiskRadio("Current Smoker", "smoking", data.smoking, recordId)}
//                 </div>
//             </div>
//         </div>
//     `;
// }
//
//
// function renderEditableInput(label, fieldName, value, recordId) {
//     return `
//         <div>
//             <label class="block min:text-md text-[var(--clr-text-a30)] mb-1 text-start">${label}</label>
//             <input type="number"
//                    step="0.01"
//                    class="record-field w-full px-3 py-2 text-sm bg-[var(--clr-surface-a0)] border border-[var(--clr-surface-a30)] rounded disabled:bg-[var(--clr-surface-a10)] disabled:text-[var(--clr-text-a50)]"
//                    data-field="${fieldName}"
//                    data-record-id="${recordId}"
//                    value="${value ?? ""}"
//                    disabled />
//         </div>
//     `;
// }
//
// function renderRiskRadio(label, fieldName, value, recordId) {
//     const yesChecked = value ? 'checked' : '';
//     const noChecked = !value ? 'checked' : '';
//
//     return `
//         <div>
//             <div class="text-sm font-medium text-[var(--clr-text-a20)] mb-2 text-start">
//                 ${label}
//             </div>
//
//             <div class="flex gap-4">
//                 <!-- YES -->
//                 <label class="
//                     flex items-center gap-3 px-3 py-2 rounded-lg border
//                     text-base font-medium select-none
//                     radio-readonly border-[var(--clr-surface-a30)]
//                 ">
//                     <input type="radio"
//                            class="risk-field radio-readonly radio-yes w-5 h-5"
//                            name="${fieldName}-${recordId}"
//                            data-field="${fieldName}"
//                            value="1"
//                            ${yesChecked}>
//                     Yes
//                 </label>
//
//                 <!-- NO -->
//                 <label class="
//                     flex items-center gap-3 px-3 py-2 rounded-lg border
//                     text-base font-medium select-none
//                     radio-readonly border-[var(--clr-surface-a30)]
//                 ">
//                     <input type="radio"
//                            class="risk-field radio-readonly radio-no w-5 h-5"
//                            name="${fieldName}-${recordId}"
//                            data-field="${fieldName}"
//                            value="0"
//                            ${noChecked}>
//                     No
//                 </label>
//             </div>
//         </div>
//     `;
// }
//

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
