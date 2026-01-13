// 📅 YEAR FILTER MODULE
export function setupYearFilterRecords(table, defaultYear) {
    const yearBtn   = document.getElementById('year-filter-btn');
    const yearMenu  = document.getElementById('year-filter-menu');
    const yearLabel = document.getElementById('year-filter-label');

    if (!yearBtn || !yearMenu || !yearLabel) return;

    // 👉 Set default label ONLY (no reload)
    if (defaultYear) {
        yearLabel.textContent = defaultYear;
    }

    yearBtn.addEventListener('click', () => {
        yearMenu.classList.toggle('hidden');
    });

    window.addEventListener('click', (e) => {
        if (!yearBtn.contains(e.target) && !yearMenu.contains(e.target)) {
            yearMenu.classList.add('hidden');
        }
    });

    // Check if we're on archive page
    const isArchivePage = window.location.pathname.includes('/archive');
    const apiUrl = isArchivePage ? '/api/getArchiveYears' : '/api/getRecordYears';

    fetch(apiUrl)
        .then(res => res.json())
        .then(years => {
            const menuList = yearMenu.querySelector('ul');

            menuList.innerHTML = `
                <li class="year-filter-dropdown-item px-3 py-2 cursor-pointer hover:bg-gray-100"
                    data-value="all">
                    All Years
                </li>
            `;

            years.forEach(year => {
                menuList.insertAdjacentHTML('beforeend', `
                    <li class="year-filter-dropdown-item px-3 py-2 cursor-pointer hover:bg-gray-100"
                        data-value="${year}">
                        ${year}
                    </li>
                `);
            });

            // ✅ USER ACTION = stage filter and refresh status counts
            menuList.querySelectorAll('.year-filter-dropdown-item').forEach(item => {
                item.addEventListener('click', () => {
                    const value = item.dataset.value;
                    yearLabel.textContent = item.textContent.trim();
                    yearMenu.classList.add('hidden');

                    // Stage the year filter (don't apply yet)
                    window.stageFilter('year', value);

                    // Trigger status filter refresh with new year
                    if (window.refreshStatusFilter) {
                        window.refreshStatusFilter(value);
                    }
                });
            });
        });
}


export function setupYearFilterPatients(table, applyFilters) {
    const yearBtn = document.getElementById('year-filter-btn');
    const yearMenu = document.getElementById('year-filter-menu');
    const yearLabel = document.getElementById('year-filter-label');

    if (!yearBtn || !yearMenu) return;

    yearBtn.addEventListener('click', () => {
        yearMenu.classList.toggle('hidden');
    });

    window.addEventListener('click', (e) => {
        if (!yearBtn.contains(e.target) && !yearMenu.contains(e.target)) {
            yearMenu.classList.add('hidden');
        }
    });

    fetch('/api/getPatientYears')
        .then(res => res.json())
        .then(rawYears => {

            // 1️⃣ Clean & sort years
            const years = rawYears
                .filter(y => typeof y === "number")
                .sort((a, b) => b - a);

            const minYear = Math.min(...years);
            const maxYear = Math.max(...years);

            // 2️⃣ Build UI
            yearMenu.innerHTML = `
                <div class="p-3 space-y-3">
                    <button id="year-all-btn"
                        class="w-full px-3 py-2 text-left hover:bg-gray-100 rounded">
                        All Years
                    </button>

                    <div class="flex gap-2 p-2">
                        <select id="year-from" class="w-full border rounded px-2 py-1">
                            ${years.map(y => `<option value="${y}">${y}</option>`).join("")}
                        </select>

                        <select id="year-to" class="w-full border rounded px-2 py-1">
                            ${years.map(y => `<option value="${y}">${y}</option>`).join("")}
                        </select>
                    </div>

                    <button id="apply-year-range"
                        class="w-full bg-blue-600 text-white rounded px-3 py-2">
                        Apply
                    </button>
                </div>
            `;

            // Default values
            document.getElementById('year-from').value = minYear;
            document.getElementById('year-to').value = maxYear;

            // 3️⃣ All years
            document.getElementById('year-all-btn').onclick = () => {
                yearLabel.textContent = "All Years";
                yearMenu.classList.add('hidden');
                applyFilters({ year: "all" });
            };

            // 4️⃣ Apply range
            document.getElementById('apply-year-range').onclick = () => {
                const from = Number(document.getElementById('year-from').value);
                const to = Number(document.getElementById('year-to').value);

                if (from > to) {
                    alert("Invalid year range");
                    return;
                }

                yearLabel.textContent = `${from} – ${to}`;
                yearMenu.classList.add('hidden');

                applyFilters({
                    year: { from, to }
                });
            };
        });
}


