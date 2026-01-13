export function setupSearchFilterRecords(table, applyFilters) {
    const searchInput = document.getElementById('record-search');
    if (!searchInput) {
        console.warn("⚠️ Search input with id='record-search' not found.");
        return;
    }

    let searchTimeout;

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        console.log("⌨️ User typed:", query);

        searchTimeout = setTimeout(() => {
            if (query === "") {
                table.clearFilter(true);
                applyFilters();
            } else {
                const filters = [
                    [
                        { field: "record_ref_filter", type: "like", value: query },
                        { field: "patient_name_filter", type: "like", value: query },
                        { field: "patient.phone_number", type: "like", value: query },
                    ]
                ];

                table.setFilter(filters);
            }
        }, 300);
    });
}

export function setupSearchFilterPatients(table, applyFilters) {
    const searchInput = document.getElementById('record-search');
    if (!searchInput) {
        console.warn("⚠️ Search input not found.");
        return;
    }

    let searchTimeout;

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        searchTimeout = setTimeout(() => {
            if (query === "") {
                table.clearFilter(true);
                applyFilters();
            } else {
                const filters = [
                    [
                        { field: "patient_id_filter", type: "like", value: query },
                        { field: "patient_name_filter", type: "like", value: query },
                        { field: "phone_number", type: "like", value: query },
                    ]
                ];

                table.setFilter(filters);
            }
        }, 300);
    });
}

export function setupSearchFilterAccounts(table, applyFilters) {
    const searchInput = document.getElementById('record-search');
    if (!searchInput) {
        console.warn("⚠️ Search input not found.");
        return;
    }

    let searchTimeout;

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        searchTimeout = setTimeout(() => {
            if (query === "") {
                table.clearFilter(true);
                applyFilters();
            } else {
                const filters = [
                    [
                        { field: "name", type: "like", value: query }
                    ]
                ];

                table.setFilter(filters);
            }
        }, 300);
    });
}




export function setupSearchBar(onSearchCallback) {

    // 1. Try OLD behavior first: #record-search IS the input
    let searchInput = document.getElementById('record-search');

    // 2. If #record-search is NOT an input, find the input inside it
    if (searchInput && searchInput.tagName !== "INPUT") {
        searchInput = searchInput.querySelector("input");
    }

    // 3. Final safety check
    if (!searchInput) {
        console.warn("⚠️ setupSearchBar: No usable search input found.");
        return;
    }

    let searchTimeout;

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);

        const raw = this.value || "";
        const query = raw.trim();

        console.log("⌨️ User typed:", query);

        searchTimeout = setTimeout(() => {
            onSearchCallback(query);
        }, 300);
    });
}



