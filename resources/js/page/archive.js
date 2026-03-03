import $ from "jquery";
import DataTable from "datatables.net-dt";

import { setupYearFilterRecords } from "@/filters/filter-year.js";
import { setupStatusFilterRecords } from "@/filters/filter-status.js";
import { createIcons, icons } from "lucide";

window.$ = window.jQuery = $;

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
const table = $("#archive-table").DataTable({
    serverSide: true,
    processing: false, // Custom loading modal handled in ajax.data
    pageLength: 20,

    scrollY: "calc(100vh - 230px)",
    scrollCollapse: true,

    autoWidth: false,
    paging: true,
    info: false,
    lengthChange: false,

    // Sorted by created_at (index 4)
    order: [[4, "desc"]],

    dom: `
        <"datatable-wrapper"
            <"datatable-body"t>
            <"datatable-footer"p>
        >
    `,

    ajax: {
        url: "/table/" + window.page.token,
        type: "GET",
        data: d => {
            d.search = $('#record-search').val();
            d.year   = typeof yearFilterValue !== 'undefined' ? yearFilterValue : 'all';
            d.unit   = $('#unit_office').val();

            // Trigger your loading modal if it exists
            if (typeof showLoading === "function") showLoading();
        },
        complete: () => {
            if (typeof hideLoading === "function") hideLoading();
        }
    },

    columnDefs: [
        { targets: 0, width: "25%" }, // Patient (HTML from PHP)
        { targets: 1, width: "25%", className: "text-center" }, // Unit
        { targets: 2, width: "20%", className: "text-center" }, // Staff ID
        { targets: 3, width: "10%", className: "text-center" }, // Created At
        { targets: 4, width: "20%", className: "text-end" }    // Actions (HTML from PHP)
    ],

    columns: [
        {
            data: "patient",
            orderable: true
        },
        {
            data: "unit",
            orderable: false
        },
        {
            data: "staff",
            orderable: false
        },
        {
            data: "deleted_at",
            orderable: true
        },
        {
            data: "actions",
            orderable: false,
            searchable: false
        }
    ]
});

$('#archive-table').on('draw.dt', function () {
    if (typeof createIcons === "function") {
        createIcons({ icons });
    }
});

/* ===============================
   UNIFIED ARCHIVE ACTION LISTENER
================================ */
$(document).on('click', '#archive-table .action-btn', function(e) {
    const btn = $(this);
    const id = btn.data('id');
    const patientId = btn.data('patient');
    const mode = btn.data('mode');

    const originalHtml = btn.html();

    // Logic for counting records or restoring
    if (mode === 'count-records' || btn.find('[data-lucide="layers"]').length) {
        // You can add an AJAX call here to fetch total records for patientId
        console.log("Fetching total records for encrypted Patient ID:", patientId);
        return;
    }

    let config = {
        title: "Restore Record",
        message: "Are you sure you want to restore this record from the archive?",
        confirmText: "Restore",
        danger: false
    };

    if (btn.hasClass('hhi-btn-delete')) {
        config = {
            title: "Permanent Delete",
            message: "This action cannot be undone. Delete this record permanently?",
            confirmText: "Delete",
            danger: true
        };
    }

    openConfirmModal(config, () => {
        $.ajax({
            url: `/update/` + window.page.token,
            type: "PUT",
            data: { id: id, mode: mode },
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
            beforeSend: function() {
                btn.prop('disabled', true).addClass('opacity-50');
                btn.html('<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>');
                createIcons({ icons });
            },
            success: function(response) {
                table.ajax.reload(null, false);
            },
            error: function() {
                btn.prop('disabled', false).removeClass('opacity-50').html(originalHtml);
                createIcons({ icons });
            }
        });
    });
});

window.table = table;


setupYearFilterRecords( yearFilterValue);
setupStatusFilterRecords(statusFilterValue);


/* ===============================
   ARCHIVE ACTIONS
================================ */

// Function to handle the confirmation logic
window.openConfirmModal = function(config, onConfirm) {
    const modal = $('#confirmModal');
    const confirmBtn = $('#modalConfirmBtn');

    // Set Content
    $('#modalTitle').text(config.title || 'Are you sure?');
    $('#modalMessage').text(config.message || '');
    confirmBtn.text(config.confirmText || 'Confirm');

    // Handle Danger/Warning Styling
    if (config.danger) {
        confirmBtn.removeClass('bg-blue-600 hover:bg-blue-700').addClass('bg-red-600 hover:bg-red-700');
        $('#modalIconContainer').removeClass('bg-blue-100 text-blue-600').addClass('bg-red-100 text-red-600');
        $('#modalIcon').attr('data-lucide', 'alert-triangle');
    } else {
        confirmBtn.removeClass('bg-red-600 hover:bg-red-700').addClass('bg-blue-600 hover:bg-blue-700');
        $('#modalIconContainer').removeClass('bg-red-100 text-red-600').addClass('bg-blue-100 text-blue-600');
        $('#modalIcon').attr('data-lucide', 'rotate-ccw');
    }

    // Refresh Lucide Icons inside modal
    if (typeof createIcons === "function") createIcons({ icons });

    // Show Modal
    modal.removeClass('hidden');

    // Setup Click Event (off() prevents multiple bindings)
    confirmBtn.off('click').on('click', function() {
        closeConfirmModal();
        if (typeof onConfirm === 'function') onConfirm();
    });
};

window.closeConfirmModal = function() {
    $('#confirmModal').addClass('hidden');
};
$(document).on('click', '.restore-record', function(e) {
    const btn = $(this);
    const id = btn.data('patient');
    const mode = btn.data('mode');

    openConfirmModal({
        title: "Restore Patient",
        message: "Are you sure you want to restore this patient and their records?",
        confirmText: "Restore",
        danger: false
    }, () => {
        $.ajax({
            url: `/update/` + window.page.token,
            type: "PUT",
            data: { id: id, mode: mode },
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
            beforeSend: function() {
                btn.prop('disabled', true);
                btn.html('<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>');
                createIcons({ icons });
            },
            success: function(response) {
                table.ajax.reload(null, false);
                // Optional: Show success toast
            },
            error: function() {
                alert("Restoration failed.");
                table.ajax.reload(null, false);
            }
        });
    });
});
