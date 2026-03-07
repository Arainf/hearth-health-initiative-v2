import $ from 'jquery';
import DataTable from 'datatables.net-dt';
import { createIcons, icons} from "lucide";

window.$ = window.jQuery = $;

const table = $("#patient-records-table").DataTable({
    serverSide: true,
    processing: false,
    autoWidth: false,
    searching: false,
    pageLength: 10,
    scrollY: 'calc(100vh - 320px)',
    scrollCollapse: true,
    paging: true,
    info: false,
    lengthChange: false,
    order: [[8, "desc"]], // Default to Date
    dom: `<"datatable-wrapper"<"datatable-body"t><"datatable-footer"p>>`,
    ajax: {
        url: `/table/` + window.page.token,
        type: "GET",
        data: d => { d.id = window.page.secret; }
    },
    columnDefs: [
        { className: "text-center", targets: [0, 6, 7, 9] },
        { orderable: false, targets: [6, 9] }
    ],
    columns: [
        { data: 0, width: "4%" },
        { data: 1, width: "10%" },
        { data: 2, width: "5%" },
        { data: 3, width: "10%" },
        { data: 4, width: "5%" },
        { data: 5, width: "5%" },
        { data: 6, width: "25%" },
        { data: 7, width: "12%" },
        { data: 8, width: "14%" },
        { data: 9, width: "10%" }
    ]
});


// Re-render icons after table draw if you use Lucide
table.on('draw', function() {
    if (typeof createIcons === 'function') {
        createIcons({ icons });
    }
});

window.table = table;
