import $ from "jquery";
import DataTable from "datatables.net-dt";

import { setupYearFilterRecords } from "@/filters/filter-year.js";
import { setupStatusFilterRecords } from "@/filters/filter-status.js";

window.$ = window.jQuery = $;

/* ===============================
   STATE & URL MANAGEMENT
================================ */
const state = {
    status: new URLSearchParams(window.location.search).get('status') || 'all',
    year: new URLSearchParams(window.location.search).get('year') || 'all',
    search: new URLSearchParams(window.location.search).get('search') || ''
};

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
    return (state.status && state.status !== 'all') ||
           (state.year && state.year !== 'all') ||
           (state.search && state.search.trim() !== '');
}

function syncFilterButton() {
    setFilterButtonMode(hasActiveFilters() ? 'reset' : 'search');
}

function resetFilters() {
    // Clear state
    state.status = 'all';
    state.year = 'all';
    state.search = '';

    // Clear pending
    pending.status = null;
    pending.year = null;
    pending.search = null;

    // Reset UI elements
    $searchInput.val('');
    $("#status-filter-label").text('All');
    $("#year-filter-label").text('All Years');

    // Update URL
    updateURL();

    // Update button state
    syncFilterButton();

    // Reload status filter with all years
    if (window.refreshStatusFilter) {
        window.refreshStatusFilter('all');
    }

    // Reload table
    table.ajax.reload();
}

/* ===============================
   STAGE FILTERS (Don't apply yet)
================================ */
function stageFilter(type, value) {
    pending[type] = value;
    syncFilterButton();
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

    // Update UI
    syncFilterButton();

    // Apply filters to DataTable
    table.ajax.reload();
}

/* ===============================
   APPLY ALL PENDING FILTERS
================================ */
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
const table = $("#archive-table").DataTable({
    serverSide: true,
    processing: false,
    pageLength: 20,

    scrollY: "calc(100vh - 230px)",
    scrollCollapse: true,

    autoWidth: false,
    paging: true,
    info: false,
    lengthChange: false,

    order: [[2, "desc"]],

    dom: `
        <"datatable-wrapper"
            <"datatable-body"t>
            <"datatable-footer"p>
        >
    `,

    ajax: {
        url: "/table/archive-records",
        type: "GET",
        data: d => {
            d.search = state.search || '';
            d.status = state.status || 'all';
            d.year   = state.year || 'all';
            showLoading();
        },
        complete: hideLoading
    },

    columnDefs: [
        { targets: 0, width: "40%" },
        { targets: 1, width: "20%" },
        { targets: 2, width: "20%" },
        { targets: 3, width: "10%" }
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
            className: "text-center",
            render: s => {
                const c =
                    s === "approved" ? "green" :
                    s === "pending"  ? "orange" : "gray";

                return `
                    <span class="px-2 py-1 text-xs rounded-full
                        bg-${c}-100 text-${c}-800 capitalize">
                        ${s}
                    </span>
                `;
            }
        },
        {
            data: "created_at",
            render: d =>
                new Date(d).toLocaleDateString("en-US", {
                    year: "numeric",
                    month: "short",
                    day: "numeric"
                })
        },
        {
            data: null,
            orderable: false,
            className: "text-center",
            render: r => {
                const hasGenerated = r.generated_id && r.generated_id !== null && r.generated_id !== '';
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

                return `
                <div class="flex items-center justify-center gap-1">
                    ${printBtn}

                    <button class="hhi-btn hhi-btn-secondary icon-only row-toggle"
                            title="Show details">
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
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
window.applyPendingFilters = applyPendingFilters;
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
   EXPANDED ROW TEMPLATE
================================ */
function formatExpandedRow(data) {
    return `
        <div class="bg-white border border-gray-200 rounded-lg p-4 text-sm space-y-4 relative">
            <div class="grid grid-cols-2 gap-4">
                ${renderReadonlyInput("Cholesterol", data.cholesterol)}
                ${renderReadonlyInput("HDL", data.hdl_cholesterol)}
                ${renderReadonlyInput("Systolic BP", data.systolic_bp)}
                ${renderReadonlyInput("FBS", data.fbs)}
                ${renderReadonlyInput("HbA1c", data.hba1c)}
            </div>

            <div class="flex gap-3 pt-2">
                ${renderRiskBadge("Hypertension", data.hypertension)}
                ${renderRiskBadge("Diabetes", data.diabetes)}
                ${renderRiskBadge("Smoking", data.smoking)}
            </div>
        </div>
    `;
}

function renderReadonlyInput(label, value) {
    return `
        <div>
            <label class="text-xs text-gray-500">${label}</label>
            <input class="w-full mt-1 px-3 py-2 text-sm bg-gray-50 border rounded"
                   value="${value ?? ""}" disabled />
        </div>
    `;
}

function renderRiskBadge(label, checked) {
    const cls = checked
        ? "badge-needs-attention"
        : "badge-outline text-gray-400 border-gray-300";

    const icon = checked
        ? '<i class="fa-solid fa-check"></i>'
        : '<i class="fa-solid fa-xmark"></i>';

    return `
        <span class="badge badge-sm ${cls}" title="${label}">
            ${icon} ${label}
        </span>
    `;
}

/* ===============================
   EVENT HANDLERS
================================ */
// Search input - stage changes, don't apply
$searchInput.off('input').on('input', () => {
    const searchTerm = $searchInput.val().trim();
    stageFilter('search', searchTerm);
});

// Handle search/reset button
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

// Handle browser back/forward
window.addEventListener('popstate', () => {
    const params = new URLSearchParams(window.location.search);
    applyFilters({
        status: params.get('status') || 'all',
        year: params.get('year') || 'all',
        search: params.get('search') || ''
    });
});

/* ===============================
   INIT
================================ */
$(document).ready(() => {
    // Initialize search input from state
    if (state.search) {
        $searchInput.val(state.search);
    }

    // Sync filter button state
    syncFilterButton();
});

