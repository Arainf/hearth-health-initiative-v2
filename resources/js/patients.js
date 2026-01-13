import $ from "jquery";
import DataTable from "datatables.net-dt";

window.$ = window.jQuery = $;

/* -----------------------------------------------------------
   HELPERS
----------------------------------------------------------- */

// Build filter params (SINGLE SOURCE OF TRUTH)
function getFilterParams() {
    const birth_from = document.getElementById("birth-from")?.value || "";
    const birth_to   = document.getElementById("birth-to")?.value || "";
    const age_min    = document.getElementById("age-min")?.value || "";
    const age_max    = document.getElementById("age-max")?.value || "";
    const genders    = [...document.querySelectorAll(".gender-filter:checked")]
        .map(el => el.value);

    return { birth_from, birth_to, age_min, age_max, genders };
}

// Update filter badge
function updateFilterCount() {
    const { birth_from, birth_to, age_min, age_max, genders } = getFilterParams();

    let count = 0;
    if (birth_from || birth_to) count++;     // Birthdate filter
    if (age_min || age_max) count++;          // Age filter
    if (genders.length > 0) count++;          // Gender filter

    const badge = document.getElementById("filter-count");
    badge.textContent = count;
    badge.classList.toggle("hidden", count === 0);
}


/* -----------------------------------------------------------
   DATATABLE
----------------------------------------------------------- */

function showLoading() {
    $('#tableLoading').removeClass('hidden');
}

function hideLoading() {
    $('#tableLoading').addClass('hidden');
}


const table = $("#patients").DataTable({
    serverSide: true,
    processing: false,
    pageLength: 15,
    order: [[1, "asc"]],

    // ✅ REQUIRED FOR ALIGNMENT
    scrollY: 'calc(100vh - 260px)',
    scrollCollapse: true,
    autoWidth: false,
    responsive: false,

    paging: true,
    fixedHeader: false,

    dom: `
        <"datatable-wrapper"
            <"datatable-body"t>
            <"datatable-footer"p>
        >
    `,

    ajax: {
        url: "/table/patients",
        type: "GET",
        data: function (d) {
            showLoading();
            return Object.assign(d, getFilterParams());
        },
        complete: hideLoading
    },

    // ✅ SINGLE SOURCE OF WIDTH TRUTH
   columnDefs: [
    { targets: 0, width: "4%",  className: "text-center" },
    { targets: 1, width: "24%" },
    { targets: 2, width: "10%", className: "text-center" },
    { targets: 3, width: "10%", className: "text-center" },
    { targets: 4, width: "14%", className: "text-center" },
    { targets: 5, width: "10%", className: "text-center" },
    { targets: 6, width: "18%", className: "text-center" },
    { targets: 7, width: "10%", className: "text-end" }
],


    columns: [
        { data: 0, orderable: false, searchable: false },
        { data: 1, orderable: false },
        { data: 2, orderable: false },
        { data: 3, orderable: false, searchable: false },
        { data: 4, orderable: false },
        { data: 5,
            orderable: false,
            render: val => {
                if(val === "Male") return `<span class="badge badge-male"> <i class="fa-solid fa-mars"></i> Male</span>`;
                if(val === "Female") return `<span class="badge badge-female"> <i class="fa-solid fa-venus"></i> Female</span>`;
                return `<span class="badge badge-needs-attention">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            Needs Attention
                        </span>`;
            }
        },
        {
            data: 6,
            orderable: false,
            render: val => {
                const d = new Date(val);
                return isNaN(d)
                    ? ""
                    : d.toLocaleDateString("en-US", {
                        year: "numeric",
                        month: "long",
                        day: "numeric"
                    });
            }
        },
        { data: 7, orderable: false, searchable: false }
    ],

   initComplete: function () {
        hideLoading();
        this.api().columns.adjust();
    },
    drawCallback: hideLoading
});



window.table = table;


$(window).on("resize", function () {
    if (window.table) {
        window.table.columns.adjust();
    }
});


/* -----------------------------------------------------------
   SEARCH
----------------------------------------------------------- */

$("#record-search").on("input", function () {
    table.search(this.value).draw();
});

/* -----------------------------------------------------------
   FILTER PANEL
----------------------------------------------------------- */

document.getElementById("filter-btn").onclick = e => {
    e.stopPropagation();
    document.getElementById("filter-panel").classList.toggle("hidden");
};

document.addEventListener("click", e => {
    const panel = document.getElementById("filter-panel");
    const btn = document.getElementById("filter-btn");
    if (!panel.contains(e.target) && !btn.contains(e.target)) {
        panel.classList.add("hidden");
    }
});

// Apply filters
document.getElementById("apply-filters").onclick = () => {
    const { age_min, age_max } = getFilterParams();

    if (age_min && age_max && Number(age_min) > Number(age_max)) {
        alert("Invalid age range");
        return;
    }

    table.ajax.reload();
    updateFilterCount();
    document.getElementById("filter-panel").classList.add("hidden");
};

// Reset filters
const $filterBtn = $("#reset-filters");
const $resetIcon = $("#filter-reset-icon");
const $searchIcon = $("#filter-search-icon");

$filterBtn
    .attr("data-mode", "reset")
    .removeClass("bg-blue-600 hover:bg-blue-700 border-blue-600 text-white")
    .addClass("bg-gray-100 hover:bg-gray-200 border-gray-300 text-gray-700");

$searchIcon.addClass("hidden").css("display", "none");
$resetIcon.removeClass("hidden").css("display", "");

function resetPatientsTable() {
    // 1. Clear all filter inputs
    document.querySelectorAll("#filter-panel input").forEach(i => {
        if (i.type === "checkbox" || i.type === "radio") {
            i.checked = false;
        } else {
            i.value = "";
        }
    });



    $("#record-search").val("");

    table.search("");
    table.page(0);

    table.ajax.reload(null, true);

    updateFilterCount();
    setFilterButtonMode("search");

    document.getElementById("filter-panel").classList.add("hidden");
}


document.getElementById("reset-filters").onclick = () => {
    resetPatientsTable();
};


$("#record-search").on("input", function () {
    const val = this.value;

    if (!val) {
        resetPatientsTable();
        return;
    }

    table.search(val).draw();
});


/* -----------------------------------------------------------
   ACTION MENU CLEANUP
----------------------------------------------------------- */

function removeFloatingMenus() {
    $(".floating-action-menu").remove();
}

$(document).on("click touchstart", e => {
    if (
        !$(e.target).closest(".action-toggle").length &&
        !$(e.target).closest(".floating-action-menu").length
    ) {
        removeFloatingMenus();
    }
});

$(window).on("scroll resize", removeFloatingMenus);

/* -----------------------------------------------------------
   DELETE FLOW
----------------------------------------------------------- */

let deleteTargetId = null;

window.deletePatient = id => {
    deleteTargetId = id;
    $("#deleteModal").removeClass("hidden");
};

$("#cancelDelete").on("click", () => {
    deleteTargetId = null;
    $("#deleteModal").addClass("hidden");
    // Reset delete button state
    const $confirmBtn = $("#confirmDelete");
    $confirmBtn.prop('disabled', false).html('Delete');
});

$("#confirmDelete").on("click", () => {
    if (!deleteTargetId) return;

    const $confirmBtn = $("#confirmDelete");
    const originalText = $confirmBtn.html();

    // Show loading state
    $confirmBtn.prop('disabled', true)
               .html('<i class="fa-solid fa-spinner fa-spin mr-2"></i>Deleting...');

    $.ajax({
        url: `/api/patient/delete/${deleteTargetId}`,
        type: "DELETE",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        success: () => {
            table.ajax.reload(null, false);
            deleteTargetId = null;
            $("#deleteModal").addClass("hidden");
            // Reset button
            $confirmBtn.prop('disabled', false).html(originalText);
        },
        error: (xhr) => {
            const errorMsg = xhr.responseJSON?.error || "Failed to delete patient.";
            alert(errorMsg);
            // Reset button
            $confirmBtn.prop('disabled', false).html(originalText);
        }
    });
});

$("#deleteModal").on("click", e => {
    if (e.target === e.currentTarget) {
        deleteTargetId = null;
        $("#deleteModal").addClass("hidden");
    }
});

/* -----------------------------------------------------------
   NAVIGATION
----------------------------------------------------------- */

window.viewPatient = id =>
    (window.location.href = `patients/${id}`);

window.editPatient = id =>
    (window.location.href = `patients/${id}/edit`);


