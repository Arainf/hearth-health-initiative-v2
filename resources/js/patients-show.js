import $ from 'jquery';
import DataTable from 'datatables.net-dt';

window.$ = window.jQuery = $;

const patientId = window.location.pathname.split("/").filter(Boolean)[1];

const table = $("#patient-records-table").DataTable({
    serverSide: true,
    processing: false,
    searching: false,
    pageLength: 10,
    scrollY: 'calc(100vh - 320px)',
    scrollCollapse: true,
    autoWidth: false,
    fixedHeader: false,
    paging: true,
    info: false,
    lengthChange: false,
    order: [[1, "asc"]],
    dom: `
        <"datatable-wrapper"
            <"datatable-body"t>
            <"datatable-footer"p>
        >
    `,
    ajax: {
        url: `/table/patients/${patientId}/records`,
        type: "GET",
        data: d => {
            d.date_from = $('#date-from').val();
            d.date_to   = $('#date-to').val();
        }
    },

    columns: [
    { data: 0, className: "text-center", width: "4%" },   // #

    { data: 1, width: "11%" }, // Total Chol
    { data: 2, width: "5%" }, // HDL
    { data: 3, width: "8%" }, // BP
    { data: 4, width: "5%" }, // FBS
    { data: 5, width: "5%" }, // HbA1c

   {
    data: null,
    className: "text-center",
    width: "28%",
    orderable: false,
    render: row => `
        <div class="risk-group flex justify-center gap-4">

            <div class="flex items-center gap-2"
                 title="Hypertension">
                <span class="risk-box ${row[6] ? 'yes' : 'no'}">
                    ${row[6] ? '✓' : '✕'}
                </span>
                <span class="text-xs font-medium text-gray-700 whitespace-nowrap">
                    Hypertension
                </span>
            </div>

            <div class="flex items-center gap-2"
                 title="Diabetes Mellitus">
                <span class="risk-box ${row[7] ? 'yes' : 'no'}">
                    ${row[7] ? '✓' : '✕'}
                </span>
                <span class="text-xs font-medium text-gray-700 whitespace-nowrap">
                    Diabetes Mellitus
                </span>
            </div>

            <div class="flex items-center gap-2"
                 title="Smoking">
                <span class="risk-box ${row[8] ? 'yes' : 'no'}">
                    ${row[8] ? '✓' : '✕'}
                </span>
                <span class="text-xs font-medium text-gray-700 whitespace-nowrap">
                    Smoking
                </span>
            </div>

        </div>
    `
},


    {
        data: 9,
        className: "text-center",
        width: "10%",           // Status
        render: s => {
            const map = {
                Approved: 'green',
                Pending: 'orange',
                Rejected: 'red'
            };
            const c = map[s] ?? 'gray';
            return `<span class="px-2 py-1 text-xs rounded-full bg-${c}-100 text-${c}-800">${s}</span>`;
        }
    },

    { data: 10, width: "12%" }, // Created

  {
    data: 11,
    orderable: false,
    width: "10%",
    className: "text-center",
    render: id => {
        // NOT AVAILABLE
        if (!id) {
            return `
                <button
                    class="hhi-btn hhi-btn-secondary text-sm"
                    disabled
                    title="No generated evaluation available"
                >
                    Not Available
                </button>
            `;
        }

        // VIEW AVAILABLE
        return `
            <button
                class="hhi-btn hhi-btn-view text-sm view-record"
                data-id="${id}"
                title="View generated evaluation"
            >
                View
            </button>
        `;
    }
}

]       

});

window.table = table;

/* DATE FILTER */
$('#apply-date').on('click', () => {
    table.ajax.reload();
});

// patients-show.js (ADD / UPDATE THESE PARTS ONLY)

$(document).on('click', '.view-record', function () {
    const id = $(this).data('id');

    $('#reportModal').removeClass('hidden');
    if(!id){
        $('#modalContent').text('No generated content.');
        return;
    }
    $('#modalContent').text('Loading…');

    $.get(`/api/getGeneratedContent/${id}`, res => {
        $('#modalContent').text(res.generated_text|| 'No generated content.');
    });
});

$('#closeModal').on('click', () => {
    $('#reportModal').addClass('hidden');
});


$('#closeDrawer').on('click', () => {
    $('#reportDrawer').addClass('translate-x-full');
});
