import $ from "jquery";
import DataTablerd from "datatables.net-dt";
// optional: filters if you add a search bar later
// import { setupSearchFilterAccounts } from "@/filters/filter-search.js";

window.$ = window.jQuery = $;

/* ===============================
   STATE & URL MANAGEMENT (Doctor-style)
================================ */
const state = {
    unit_group: new URLSearchParams(window.location.search).get("unit_group") || "all",
    search: new URLSearchParams(window.location.search).get("search") || ""
};

const pending = {
    unit_group: null,
    search: null
};

function updateURL() {
    const params = new URLSearchParams();
    if (state.unit_group && state.unit_group !== "all") params.set("unit_group", state.unit_group);
    if (state.search) params.set("search", state.search);

    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.pushState({}, "", newUrl);
}

/* ===============================
   LOADING
================================ */
function showLoading() {
    $("#talbleLoading")      // note: id in unit.blade.php is "talbleLoading"
        .removeClass("hidden -z-10")
        .addClass("z-50");
}

function hideLoading() {
    $("#talbleLoading")
        .addClass("hidden -z-10")
        .removeClass("z-50");
}

/* ===============================
   FILTER BUTTON STATE
================================ */
const $searchInput = $("#record-search");

function stageFilter(type, value) {
    pending[type] = value;
}

function applyFilters(filters = {}) {
    if (filters.unit_group !== undefined) {
        state.unit_group = filters.unit_group;
        pending.unit_group = null;
    } else if (pending.unit_group !== null) {
        state.unit_group = pending.unit_group;
        pending.unit_group = null;
    }

    if (filters.search !== undefined) {
        state.search = filters.search;
        pending.search = null;
    } else if (pending.search !== null) {
        state.search = pending.search;
        pending.search = null;
    }

    updateURL();
    table.ajax.reload();
}

window.applyPendingFilters = function () {
    applyFilters({
        unit_group: pending.unit_group !== null ? pending.unit_group : undefined,
        search: pending.search !== null ? pending.search : undefined
    });
};

window.resetFilters = function () {
    state.unit_group = "all";
    state.search = "";

    pending.unit_group = null;
    pending.search = null;

    // Reset UI
    $searchInput.val("");
    $("#unit-group-filter-label").text("all unit groups");

    updateURL();
    table.ajax.reload();
};

/* ===============================
   DATATABLE INIT
================================ */
const table = $("#units-table").DataTable({
    serverSide: true,
    processing: false,
    searching: false,
    pageLength: 20,
    lengthChange: false,
    info: false,
    autoWidth: false,
    paging: true,

    scrollY: "calc(100vh - 260px)",
    scrollCollapse: true,

    order: [[0, "asc"]],

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
            d.search = state.search || "";
            d.unit_group = state.unit_group || "all";
            showLoading();
        },
        complete: hideLoading
    },

    columns: [
        { data: "unit_name",       width: "40%" },
        { data: "unit_abbr",       width: "20%" },
        { data: "unit_group_name", width: "20%" },
        {
            data: null,
            orderable: false,
            width: "10%",
            className: "text-center",
            render: r => {
                const id = typeof r.id === 'string' ? r.id.replace(/'/g, "\\'") : r.id;
                return `
                <div class="flex gap-2 justify-center">
                    <button class="hhi-btn hhi-btn-edit text-xs"
                            onclick="event.stopPropagation(); window.editUnit('${id}');">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </button>
                    <button class="hhi-btn hhi-btn-delete text-xs"
                            onclick="event.stopPropagation(); window.deleteUnit('${id}');">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </div>
            `;
            }
        }
    ]
});

window.table = table;
window.stageFilter = stageFilter;

/* ===============================
   WIRE UP FILTER UI (Doctor-style)
================================ */
// Seed UI from URL state (like Doctor)
$searchInput.val(state.search);
if (state.unit_group && state.unit_group !== "all") {
    // try to reflect label from selected option (best-effort)
    const $match = $(`#unit-group-filter-menu .unit-group-filter-dropdown-item[data-value="${state.unit_group}"]`);
    if ($match.length) $("#unit-group-filter-label").text($match.text().trim());
}

// stage search (apply only on Search button click)
$searchInput.off("input").on("input", () => {
    const searchTerm = ($searchInput.val() || "").trim();
    stageFilter("search", searchTerm);
});

// Dropdown open/close + stage unit_group filter (Doctor-style, but custom)
(function setupUnitGroupFilter() {
    const name  = "unit-group-filter";
    const btn   = document.getElementById(`${name}-btn`);
    const menu  = document.getElementById(`${name}-menu`);
    const label = document.getElementById(`${name}-label`);
    const items = menu?.querySelectorAll(`.${name}-dropdown-item`);

    if (!btn || !menu || !label || !items) return;

    // Toggle menu on button click
    btn.addEventListener("click", (e) => {
        e.stopPropagation();
        menu.classList.toggle("hidden");
    });

    // Close on outside click
    window.addEventListener("click", () => {
        menu.classList.add("hidden");
    });

    // Handle selection
    items.forEach((item) => {
        item.addEventListener("click", () => {
            const value = item.dataset.value;
            const text  = item.textContent.trim();

            label.textContent = text;
            menu.classList.add("hidden");

            stageFilter("unit_group", value);
        });
    });
})();

// Handle back/forward browser navigation like Doctor
window.addEventListener("popstate", () => {
    const params = new URLSearchParams(window.location.search);
    applyFilters({
        unit_group: params.get("unit_group") || "all",
        search: params.get("search") || ""
    });
});

/* ===============================
   CONFIRM MODAL
================================ */
let confirmCallback = null;

function openConfirmModal({ title, message, confirmText, danger }, cb) {
    confirmCallback = cb;
    $("#confirmTitle").text(title);
    $("#confirmMessage").text(message);
    $("#confirmOkBtn").text(confirmText)
        .toggleClass("bg-red-600", danger)
        .toggleClass("bg-blue-600", !danger);

    $("#confirmModal").removeClass("hidden");
}

$("#confirmCancelBtn").on("click", () => {
    $("#confirmModal").addClass("hidden");
    confirmCallback = null;
});

$("#confirmOkBtn").on("click", () => {
    if (confirmCallback) confirmCallback();
    $("#confirmModal").addClass("hidden");
});

/* ===============================
   ACTIONS
================================ */
window.editUnit = (id) => {
    window.location.href = `/units/${id}/edit`;
};

window.deleteUnit = (id) => {
    openConfirmModal(
        {
            title: "Delete Unit",
            message: "This action cannot be undone.",
            confirmText: "Delete",
            danger: true
        },
        () => {
            $.ajax({
                url: `/api/units/delete/${id}`,
                type: "DELETE",
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: () => table.ajax.reload(null, false),
                error: () => alert("Error deleting unit")
            });
        }
    );
};