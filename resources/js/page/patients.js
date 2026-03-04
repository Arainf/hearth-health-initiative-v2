import $ from "jquery";
import DataTable from "datatables.net-dt";
import { createIcons, icons} from "lucide";

window.$ = window.jQuery = $;

/* -----------------------------------------------------------
   DATATABLE
----------------------------------------------------------- */

const table = $("#patients").DataTable({
    serverSide: true,
    processing: true,
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
        url: "/table/" + window.page.token ,
        type: "GET",
        data: d => {
            d.unit = $('#unit_office').val();
            d.search_name = $('#record-search').val();
        },

    },

    // ✅ SINGLE SOURCE OF WIDTH TRUTH
   columnDefs: [
    { targets: 0, width: "4%",  className: "text-center" },
    { targets: 1, width: "19%" },
    { targets: 2, width: "15%", className: "text-center" },
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
        {
            data: 5,
            orderable: false,
            render: val => {

                if (val === "Male") {
                    return `
                <span class="badge badge-male  gap-1">
                    <i data-lucide="mars" class="w-4 h-4"></i>
                    Male
                </span>
            `;
                }

                if (val === "Female") {
                    return `
                <span class="badge badge-female  gap-1">
                    <i data-lucide="venus" class="w-4 h-4"></i>
                    Female
                </span>
            `;
                }

                return `
            <span class="badge badge-needs-attention gap-1">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                Needs Attention
            </span>
        `;
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
    language: {
        emptyTable: "Please select a unit first"
    },

    initComplete: function () {
        const api = this.api();
        setTimeout(() => {
            api.columns.adjust().draw();
        }, 100);
    }
});

$('#patients').on('draw.dt', function () {
    createIcons({ icons });
});

$(document).on("click", "#search-button", function () {
    const unit = $('#unit_office').val();
    const name = $('#record-search').val();

    // Visual feedback for the user
    if (!unit && !name) {
        table.settings()[0].oLanguage.sEmptyTable = "Please select a unit or enter a name first";
        table.clear().draw();
        return;
    }

    table.settings()[0].oLanguage.sEmptyTable = "No patients found for this search";

    // This is the ONLY thing that should trigger the server request
    table.ajax.reload();
});

$(document).on(
    "click",
    "#reset-filters",
    function () {

        $("#record-search").val('');
        $('#unit_office').val('').trigger('change');

        table.ajax.reload();
    }
);




window.table = table;


// $(window).on("resize", function () {
//     if (window.table) {
//         window.table.columns.adjust();
//     }
// });


/* -----------------------------------------------------------
   SEARCH
----------------------------------------------------------- */



// /* -----------------------------------------------------------
//    FILTER PANEL
// ----------------------------------------------------------- */
//
// document.getElementById("filter-btn").onclick = e => {
//     e.stopPropagation();
//     document.getElementById("filter-panel").classList.toggle("hidden");
// };
//
// document.addEventListener("click", e => {
//     const panel = document.getElementById("filter-panel");
//     const btn = document.getElementById("filter-btn");
//     if (!panel.contains(e.target) && !btn.contains(e.target)) {
//         panel.classList.add("hidden");
//     }
// });
//
// // Apply filters
// document.getElementById("apply-filters").onclick = () => {
//     const { age_min, age_max } = getFilterParams();
//
//     if (age_min && age_max && Number(age_min) > Number(age_max)) {
//         alert("Invalid age range");
//         return;
//     }
//
//     table.ajax.reload();
//     updateFilterCount();
//     document.getElementById("filter-panel").classList.add("hidden");
// };
//
// // Reset filters
// const $filterBtn = $("#reset-filters");
// const $resetIcon = $("#filter-reset-icon");
// const $searchIcon = $("#filter-search-icon");
//
// $filterBtn
//     .attr("data-mode", "reset")
//     .removeClass("bg-blue-600 hover:bg-blue-700 border-blue-600 text-white")
//     .addClass("bg-gray-100 hover:bg-gray-200 border-gray-300 text-gray-700");
//
// $searchIcon.addClass("hidden").css("display", "none");
// $resetIcon.removeClass("hidden").css("display", "");
//
// function resetPatientsTable() {
//     // 1. Clear all filter inputs
//     document.querySelectorAll("#filter-panel input").forEach(i => {
//         if (i.type === "checkbox" || i.type === "radio") {
//             i.checked = false;
//         } else {
//             i.value = "";
//         }
//     });
//
//
//
//     $("#record-search").val("");
//
//     table.search("");
//     table.page(0);
//
//     table.ajax.reload(null, true);
//
//     updateFilterCount();
//     setFilterButtonMode("search");
//
//     document.getElementById("filter-panel").classList.add("hidden");
// }
//
//
// document.getElementById("reset-filters").onclick = () => {
//     resetPatientsTable();
// };
//
//
// $("#record-search").on("input", function () {
//     const val = this.value;
//
//     if (!val) {
//         resetPatientsTable();
//         return;
//     }
//
//     table.search(val).draw();
// });
//
//
// /* -----------------------------------------------------------
//    ACTION MENU CLEANUP
// ----------------------------------------------------------- */
//
// function removeFloatingMenus() {
//     $(".floating-action-menu").remove();
// }
//
// $(document).on("click touchstart", e => {
//     if (
//         !$(e.target).closest(".action-toggle").length &&
//         !$(e.target).closest(".floating-action-menu").length
//     ) {
//         removeFloatingMenus();
//     }
// });
//
// $(window).on("scroll resize", removeFloatingMenus);

// /* -----------------------------------------------------------
//    DELETE FLOW
// ----------------------------------------------------------- */
//
// let deleteTargetId = null;
//
// window.deletePatient = id => {
//     deleteTargetId = id;
//     $("#deleteModal").removeClass("hidden");
// };
//
// $("#cancelDelete").on("click", () => {
//     deleteTargetId = null;
//     $("#deleteModal").addClass("hidden");
//     // Reset delete button state
//     const $confirmBtn = $("#confirmDelete");
//     $confirmBtn.prop('disabled', false).html('Delete');
// });
//
// $("#confirmDelete").on("click", () => {
//     if (!deleteTargetId) return;
//
//     const $confirmBtn = $("#confirmDelete");
//     const originalText = $confirmBtn.html();
//
//     // Show loading state
//     $confirmBtn.prop('disabled', true)
//                .html('<i class="fa-solid fa-spinner fa-spin mr-2"></i>Deleting...');
//
//     $.ajax({
//         url: `/api/patient/delete/${deleteTargetId}`,
//         type: "DELETE",
//         headers: {
//             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
//         },
//         success: () => {
//             table.ajax.reload(null, false);
//             deleteTargetId = null;
//             $("#deleteModal").addClass("hidden");
//             // Reset button
//             $confirmBtn.prop('disabled', false).html(originalText);
//         },
//         error: (xhr) => {
//             const errorMsg = xhr.responseJSON?.error || "Failed to delete patient.";
//             alert(errorMsg);
//             // Reset button
//             $confirmBtn.prop('disabled', false).html(originalText);
//         }
//     });
// });
//
// $("#deleteModal").on("click", e => {
//     if (e.target === e.currentTarget) {
//         deleteTargetId = null;
//         $("#deleteModal").addClass("hidden");
//     }
// });

$(document).on('click', '.hhi-btn-delete', function (e) {
    e.preventDefault();

    const button = $(this);
    const encryptedId = button.data('id');
    const mode = button.data('more'); // encrypted 'delete'

    if (!encryptedId) return;

    if (!confirm('Are you sure you want to delete this patient?')) {
        return;
    }

    $.ajax({
        // ✅ 1. Point to the specific delete route
        url: '/delete/' + window.page.token,
        // ✅ 2. Use the DELETE method to match your Route::delete()
        type: 'DELETE',
        data: {
            id: encryptedId,
            mode: mode
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            button.prop('disabled', true);
        },
        success: function (response) {
            // Reload table without resetting pagination
            table.ajax.reload(null, false);
            alert(response.message || 'Deleted successfully.');
        },
        error: function (xhr) {
            alert(xhr.responseJSON?.message || 'Delete failed.');
        },
        complete: function () {
            button.prop('disabled', false);
        }
    });
});
