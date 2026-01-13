import $ from "jquery";
import DataTable from "datatables.net-dt";

window.$ = window.jQuery = $;

const table = $("#patients-nav").DataTable({
    serverSide: false,
    processing: false,
    order: [[1, "asc"]],

    autoWidth: false,
    responsive: false,
    pageLength: -1,          // 🔥 show ALL rows
    lengthMenu: [
        [-1],
        ["All"]
    ],

    scrollY: "calc(100vh - 200px)",
    scrollCollapse: true,
    paging: false,
    fixedHeader: false,

    dom: `
        <"datatable-wrapper"
            <"datatable-body"t>
            <"datatable-footer">
        >
    `,

    ajax: {
        url: "/table/patientsNav",
        type: "GET",

    },

    // ✅ SINGLE SOURCE OF WIDTH TRUTH
    columnDefs: [
        { targets: 0, width: "100%" }
    ],


    columns: [
        { data: 0, orderable: false },
    ],

    initComplete: function () {
        this.api().columns.adjust();
    },

});


window.table = table;

$("#record-search , #last_name, #first_name, #middle_name").on("input", function () {
    table.search(this.value).draw();
});


