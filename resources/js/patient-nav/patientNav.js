import $ from "jquery";
import DataTable from "datatables.net-dt";

window.$ = window.jQuery = $;

const table = $("#patients-nav").DataTable({
    serverSide: false,
    processing: true,
    order: [[0, "asc"]],
    autoWidth: false,
    responsive: false,
    pageLength: -1,

    scrollY: "calc(100vh - 200px)",
    scrollCollapse: true,
    paging: true,
    fixedHeader: false,

    deferLoading: 0,
    dom: `
     <"datatable-wrapper" <"datatable-body"t> <"datatable-footer"> >
    `,

    ajax: {
        url: window.page.table,
        type: "GET",
        data: function (d) {
            d.unit_code = $('#unit_office').val();
        }
    },

    columnDefs: [
        { targets: 0, width: "100%" }
    ],

    columns: [
        { data: 0, orderable: false },
    ],

    language: {
        emptyTable: "Please select a unit first"
    },

    initComplete: function () {
        this.api().columns.adjust();
    },
});

$('#record-search')
    .addClass('search-disabled')
    .prop('readonly', true);

$('#unit_office').on('change', function () {

    const unit = $(this).val();

    if (!unit) {
        table.settings()[0].oLanguage.sEmptyTable = "Please select a unit first";
        table.clear().draw();

        $('#record-search')
            .addClass('search-disabled')
            .prop('readonly', true);

        return;
    }

    table.settings()[0].oLanguage.sEmptyTable = "No patients found for this unit";
    table.ajax.reload();

    $('#record-search')
        .removeClass('search-disabled')
        .prop('readonly', false);
});



window.table = table;

$("#record-search , #last_name, #first_name, #middle_name").on("input", function () {
    table.search(this.value).draw();
});


