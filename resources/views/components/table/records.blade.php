<style>

    table.dataTable tbody tr > td:last-child {
        text-align: center;
    }

    table.dataTable thead tr > th {
        text-align: left !important;
    }

    table.dataTable thead tr > th:nth-child(2) {
        text-align: left !important;
    }


</style>

<table id="records-table" class="table datatable w-full font-inter ">
    <thead>
        <tr>
            <th class="relative">Patient        <x-lucide-user-round class="w-4 h-4 font-bold text-slate-400 absolute right-3 top-3" /> </th>
            <th class="relative">Office/Unit    <x-lucide-building-2 class="w-4 h-4 font-bold text-slate-400 absolute right-3 top-3" /></th>
            <th class="relative">Created By     <x-lucide-user-round class="w-4 h-4 font-bold text-slate-400 absolute right-3 top-3" /></th>
            <th class="relative">Created        <x-lucide-clock-plus class="w-4 h-4 font-bold text-slate-400 absolute right-3 top-3" /></th>
            <th class="relative">Status         <x-lucide-info class="w-4 h-4 font-bold text-slate-400 absolute right-3 top-3" /></th>
            <th class="relative">Actions        <x-lucide-grip-horizontal class="w-4 h-4 font-bold text-slate-400 absolute right-3 top-3" /></th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
