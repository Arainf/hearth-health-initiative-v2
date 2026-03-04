import $ from "jquery";
import DataTablerd from "datatables.net-dt";
import { createIcons, icons } from "lucide";

window.$ = window.jQuery = $;

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
        url: "/table/" + window.page.token,
        type: "GET",
        data: d => {
            // Check these IDs match your Blade x-filter_search and x-dropdown_select
            d.search = $('#filter-search').val();
            d.unit_group = $("#unit-group-filter").val();
        },
    },
    columns: [
        { data: "unit_name",       width: "40%" },
        { data: "unit_abbr",       width: "20%" },
        { data: "unit_group_name", width: "20%" },
        {
            data: "actions",
            width: "20%",
            orderable: false,
            className: "text-center"
        }
    ]
});

// Re-render Lucide icons after every table draw
$('#units-table').on('draw.dt', function () {
    createIcons({ icons });
});

/* ===============================
   SEARCH & FILTER
================================ */
$(document).on("click", "#search-button", function () {
    table.ajax.reload();
});

$(document).on("click",   "#reset-filters",  function () {
    $('#filter-search').val('');
    $('#unit-group-filter').val('').trigger('change');
    table.ajax.reload();
});

/* ===============================
   CONFIRM MODAL LOGIC
================================ */
let confirmCallback = null;

function openConfirmModal({ title, message, confirmText, danger }, cb) {
    confirmCallback = cb;
    $("#confirmTitle").text(title || "Confirm Action");
    $("#confirmMessage").text(message || "Are you sure you want to proceed?");
    $("#confirmOkBtn").text(confirmText || "Confirm")
        .toggleClass("bg-red-600", danger)
        .toggleClass("bg-blue-600", !danger);

    $("#confirmModal").removeClass("hidden").addClass("flex");
}

/* ===============================
   UNIT CRUD (MODAL & AJAX)
================================ */
$(document).ready(function() {
    const modal = $('#unitModal');
    const form = $('#unit-form');

    // --- 1. OPEN CREATE MODAL ---
    $(document).on('click', '#open-create-modal', function() {
        $('#modalTitle').text('Create New Unit');
        form[0].reset();
        $('#method-field').empty();
        $('#mode-plain').val('store');
        $('#form-mode-enc').val(window.encodings.store);
        $('#form-id-enc').val('');
        modal.removeClass("hidden").addClass("flex");
    });

    // --- 2. OPEN EDIT MODAL (POPULATE) ---
    $(document).on('click', '.edit-office', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        const groupCode = $(this).data('group-code');

        // Get row data directly from DataTable instance
        const rowData = table.row($(this).closest('tr')).data();

        $('#modalTitle').text('Edit Unit');
        $('#input_unit_name').val(rowData.unit_name);
        $('#input_unit_abbr').val(rowData.unit_abbr);
        $('#input_unit_group_code').val(groupCode);

        $('#method-field').html('<input type="hidden" name="_method" value="PUT">');
        $('#mode-plain').val('update');
        $('#form-mode-enc').val(window.encodings.update);
        $('#form-id-enc').val(id);

        modal.removeClass('hidden').addClass('flex');
    });

    // --- 3. SUBMIT (STORE/UPDATE) ---
    form.on('submit', function(e) {
        e.preventDefault();
        const mode = $('#mode-plain').val();
        const url = (mode === 'store') ? `/store/${window.page.token}` : `/update/${window.page.token}`;

        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function(res) {
                modal.addClass('hidden').removeClass('flex');
                table.ajax.reload(null, false);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Show first error message
                    alert(Object.values(xhr.responseJSON.errors)[0][0]);
                } else {
                    alert("An error occurred while saving.");
                }
            }
        });
    });

    // --- 4. DELETE ---
    $(document).on('click', '.delete-office', function() {
        const id = $(this).data('id');
        console.log("im here")
        openConfirmModal({
            title: "Delete Unit",
            message: "Are you sure you want to delete this unit? This cannot be undone.",
            confirmText: "Delete",
            danger: true
        }, () => {
            $.ajax({
                url: `/delete/${window.page.token}`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE',
                    id: id,
                    mode: window.encodings.delete
                },
                success: function() {
                    table.ajax.reload(null, false);
                    $("#confirmModal").addClass("hidden").removeClass("flex");
                }
            });
        });
    });

    // --- CLOSE HANDLERS ---
    $(document).on('click', '.closeModal, #confirmCancelBtn', function() {
        modal.addClass('hidden').removeClass('flex');
        $("#confirmModal").addClass("hidden").removeClass("flex");
        confirmCallback = null;
    });

    // Confirm OK handler
    $("#confirmOkBtn").on("click", function() {
        if (confirmCallback) confirmCallback();
    });
});
