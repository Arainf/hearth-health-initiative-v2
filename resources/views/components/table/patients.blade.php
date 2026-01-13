
<script>
    const patientUrl = "{{ asset('icons/user-solid-full.svg') }}";
    const phoneUrl = "{{ asset('icons/phone-flip-solid-full.svg') }}";
    const genderUrl = "{{ asset('icons/venus-mars-solid-full.svg') }}";
    const sideBarUrl = "{{ asset('icons/sidebar-right.svg') }}";
    const compareUrl = "{{ asset('icons/file-compare.png') }}"
</script>

<table id="patients" class="table datatable w-full bg-[#f9fbfc]">
    <thead>
       <tr>
            <th style="width:4%;">#</th>
            <th style="width:24%;">Patient Name</th>
            <th style="width:10%;">Unit</th>
            <th style="width:10%;">Records</th>
            <th style="width:14%;">Phone</th>
            <th style="width:10%;">Gender</th>
            <th style="width:18%;">Birth Day</th>
            <th style="width:10%;"></th>
        </tr>

    </thead>
    <tbody></tbody>
</table>


