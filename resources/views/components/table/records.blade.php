<style>
    /* Left border */
    table.dataTable tbody tr > td:first-child {
    border-left: 3px solid #e5e7eb;
    text-align: left;
    }

    table.dataTable tbody tr > td:last-child {
        text-align: center;
    }

    table.dataTable thead tr > th:first-child {
        text-align: left !important;
    }

    table.dataTable thead tr > th:nth-child(2) {
        text-align: center !important;
    }

    table.dataTable thead tr > th:last-child {
        text-align: left !important;
    }
</style>

<table id="records-table" class="table datatable w-full bg-[#f9fbfc]">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
