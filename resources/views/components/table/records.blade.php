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

    table.dataTable tbody tr > td:nth-child(2) {
        max-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    table.dataTable tbody tr > td:nth-child(3) {
        max-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

</style>

<table id="records-table" class="table datatable w-full font-inter overflow-hidden">
    <thead>
    <tr>
        <th class="relative">Patient
            <x-lucide-user-round      class="w-[var(--s-icon)] h-[var(--s-icon)] absolute right-3 top-2 lg:block md:hidden" />
        </th>
        <th class="relative">Office/Unit
            <x-lucide-building-2      class="w-[var(--s-icon)] h-[var(--s-icon)] absolute right-3 top-2 lg:block md:hidden" />
        </th>
        <th class="relative">Created By
            <x-lucide-user-round      class="w-[var(--s-icon)] h-[var(--s-icon)] absolute right-3 top-2 lg:block md:hidden" />
        </th>
        <th class="relative">Recorded At
            <x-lucide-clock-plus      class="w-[var(--s-icon)] h-[var(--s-icon)] absolute right-3 top-2 lg:block md:hidden" />
        </th>
        <th class="relative">Status
            <x-lucide-info            class="w-[var(--s-icon)] h-[var(--s-icon)] absolute right-3 top-2 lg:block md:hidden" />
        </th>
        <th class="relative">Actions
            <x-lucide-grip-horizontal class="w-[var(--s-icon)] h-[var(--s-icon)] absolute right-3 top-2 lg:block md:hidden" />
        </th>
    </tr>
    </thead>
    <tbody></tbody>
</table>
