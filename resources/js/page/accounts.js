import $ from "jquery";
import DataTable from "datatables.net-dt";
import { setupSearchFilterAccounts } from "@/filters/filter-search.js";
import { createIcons, icons } from "lucide";

window.$ = window.jQuery = $;

/* ===============================
   LOADING STATE
================================ */
function showLoading() {
    $("#loadingModal").removeClass("hidden -z-10").addClass("z-50");
}

function hideLoading() {
    $("#loadingModal").addClass("hidden -z-10").removeClass("z-50");
}

/* ===============================
   DATATABLE INIT
================================ */
const table = $("#accounts-table").DataTable({
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
    order: [[4, "desc"]],
    dom: `<"datatable-wrapper"<"datatable-body"t><"datatable-footer"p>>`,
    ajax: {
        url: "/table/" + window.page.token,
        type: "GET",
        data: d => {
            d.search = $("#record-search").val();
            showLoading();
        },
        complete: hideLoading
    },
    columns: [
        {
            data: "is_admin",
            width: "10%",
            className: "text-center",
            render: (data, type, row) => {
                const isAdmin = row.is_admin == 1;
                const isDoctor = row.is_doctor == 1;
                return `<span class="px-3 py-1 text-xs rounded-full font-semibold ${isAdmin ? "badge badge-admin" : isDoctor ? "badge badge-doctor" : "badge badge-user"}">${isAdmin ? "Admin" : isDoctor ? "Doctor" : "User"}</span>`;
            }
        },
        { data: "name", width: "22%" },
        { data: "username", width: "22%" },
        {
            data: "ai_access",
            width: "14%",
            className: "text-center",
            render: v => `<span class="px-3 py-1 text-xs rounded-full font-semibold ${v ? "badge badge-enabled" : "badge badge-disabled"}">${v ? "Enabled" : "Disabled"}</span>`
        },
        {
            data: "created_at",
            width: "16%",
            render: d => new Date(d).toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric" })
        },
        {
            data: "actions", // ✅ Fetches the flexbox buttons from PHP
            orderable: false,
            width: "16%",
            className: "text-end"
        }
    ]
});

// Re-render icons on table draw
$('#accounts-table').on('draw.dt', function () {
    createIcons({ icons });
});

/* ===============================
   SEARCH FILTER
================================ */
setupSearchFilterAccounts(table);

/* ===============================
   UNIFIED ACTION LISTENER
================================ */
$(document).on('click', '#accounts-table .action-btn', function(e) {
    const btn = $(this);
    const id = btn.data('id');
    const mode = btn.data('mode');
    const value = btn.data('value');

    // Save the original icon HTML to restore it later
    const originalHtml = btn.html();

    let config = {
        title: "Update Account",
        message: "Are you sure you want to perform this action?",
        confirmText: "Proceed",
        danger: false
    };

    if (btn.hasClass('hhi-btn-delete')) {
        config = {
            title: "Delete Account",
            message: "This is permanent. Proceed?",
            confirmText: "Delete",
            danger: true
        };
    }

    openConfirmModal(config, () => {
        $.ajax({
            url: `/update/` + window.page.token,
            type: "PUT",
            data: { id: id, mode: mode, value: value },
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
            beforeSend: function() {
                // ✅ Start Animation: Disable button and show spinner
                btn.prop('disabled', true).addClass('opacity-70 cursor-not-allowed');
                btn.html('<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>');
                createIcons({ icons }); // Initialize the new loader icon
            },
            success: function() {
                // Table reload will usually refresh the buttons, but we restore just in case
                table.ajax.reload(null, false);
            },
            error: function() {
                alert("An error occurred. Please try again.");
                // ✅ Restore button if request fails
                btn.prop('disabled', false).removeClass('opacity-70 cursor-not-allowed');
                btn.html(originalHtml);
                createIcons({ icons });
            }
        });
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
        .toggleClass("bg-red-600", !!danger)
        .toggleClass("bg-blue-600", !danger);

    $("#confirmModal").removeClass("hidden");
}

$("#confirmCancelBtn, #confirmOkBtn").on("click", function() {
    if ($(this).attr('id') === 'confirmOkBtn' && confirmCallback) {
        confirmCallback();
    }
    $("#confirmModal").addClass("hidden");
    confirmCallback = null;
});
