
<script>
    const patientUrl = "{{ asset('icons/user-solid-full.svg') }}";
    const phoneUrl = "{{ asset('icons/phone-flip-solid-full.svg') }}";
    const genderUrl = "{{ asset('icons/venus-mars-solid-full.svg') }}";
    const sideBarUrl = "{{ asset('icons/sidebar-right.svg') }}";
    const compareUrl = "{{ asset('icons/file-compare.png') }}"
</script>


<style>
    table.dataTable tbody tr > td:first-child{
        text-align: center;
    }

    table.dataTable tbody tr > td:nth-child(6){
        text-align: center;
    }

    .dataTables_scrollHeadInner,
    .dataTables_scrollHeadInner table {
        width: 100% !important;
    }
</style>

<table id="patients" class="table datatable w-full bg-[#f9fbfc]">
    <thead>
       <tr>
            <th class="text-center">#</th>
            <th>Patient Name</th>
            <th>Unit</th>
            <th>Records</th>
            <th>Phone</th>
            <th>Gender</th>
            <th>Birth Day</th>
            <th>Actions</th>
        </tr>

    </thead>
    <tbody></tbody>
</table>


