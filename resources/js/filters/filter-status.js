export function setupStatusFilter(table, initialStatus = null, initialYear = 'all') {
    const dropdownBtn = document.getElementById('status-filter-btn');
    const dropdownMenu = document.getElementById('status-filter-menu');
    const dropdownLabel = document.getElementById('status-filter-label');
    if (!dropdownBtn || !dropdownMenu) return;

    let currentYear = initialYear;

    // Function to load status counts for a given year
    function loadStatusCounts(year = 'all') {
        // Disable dropdown and show loading state
        dropdownBtn.disabled = true;
        dropdownBtn.classList.add('opacity-50', 'cursor-not-allowed');

        // Show loading in menu
        const menuList = dropdownMenu.querySelector('ul');
        menuList.innerHTML = `
            <li class="px-3 py-4 text-center text-gray-500">
                <div class="flex items-center justify-center gap-2">
                    <div class="w-4 h-4 border-2 border-gray-300 border-t-blue-600 rounded-full animate-spin"></div>
                    <span>Loading status counts...</span>
                </div>
            </li>
        `;

        // Build API URL with year parameter
        // Check if we're on archive page
        const isArchivePage = window.location.pathname.includes('/archive');
        const archivedParam = isArchivePage ? '&archived=true' : '&archived=false';
        const apiUrl = year && year !== 'all'
            ? `/api/getStatusCount?year=${year}${archivedParam}`
            : `/api/getStatusCount?${archivedParam}`;

        fetch(apiUrl)
            .then(res => res.json())
            .then(data => {
                menuList.innerHTML = `
                    <li class="status-filter-dropdown-item flex items-center gap-2 px-3 py-2 cursor-pointer hover:bg-gray-100" data-value="all">
                        <span class="inline-block rounded-full h-3 w-3 bg-gray-400"></span> All
                    </li>
                `;

                data.forEach(status => {
                    const colorMap = {
                        'approved': '#16a34a',
                        'pending': '#f59e0b',
                        'not evaluated': '#9ca3af'
                    };
                    const color = colorMap[status.status_name.toLowerCase()] || '#6b7280';
                    const formattedValue = status.status_name.toLowerCase();

                    menuList.insertAdjacentHTML('beforeend', `
                        <li class="status-filter-dropdown-item flex items-center gap-2 px-3 py-2 cursor-pointer hover:bg-gray-100"
                            data-value="${formattedValue}">
                            <span class="inline-block rounded-full h-3 w-3" style="background-color:${color}"></span>
                            ${status.status_name}
                            <span class="ml-auto text-gray-500">(${status.count})</span>
                        </li>
                    `);
                });

                // Set initial status label if provided (only on first load when label is still default)
                if (initialStatus && initialStatus !== 'all' && (dropdownLabel.textContent === 'All' || !dropdownLabel.textContent.trim())) {
                    const initialItem = menuList.querySelector(`[data-value="${initialStatus}"]`);
                    if (initialItem) {
                        const fullText = initialItem.textContent.trim();
                        const displayLabel = fullText.replace(/\s*\([^)]*\)\s*$/, '').trim();
                        dropdownLabel.textContent = displayLabel;
                    }
                }

                // Re-enable dropdown
                dropdownBtn.disabled = false;
                dropdownBtn.classList.remove('opacity-50', 'cursor-not-allowed');

                // Event listeners
                menuList.querySelectorAll('.status-filter-dropdown-item').forEach(item => {
                    item.addEventListener('click', () => {
                        const value = item.dataset.value;
                        // Extract just the status name without the count
                        let displayLabel = 'All';
                        if (value === 'all') {
                            displayLabel = 'All';
                        } else {
                            // Get text content and remove count in parentheses
                            const fullText = item.textContent.trim();
                            displayLabel = fullText.replace(/\s*\([^)]*\)\s*$/, '').trim();
                        }
                        dropdownLabel.textContent = displayLabel;
                        dropdownMenu.classList.add('hidden');

                        // Stage the filter (don't apply yet)
                        window.stageFilter('status', value);
                    });
                });
            })
            .catch(error => {
                console.error('Error loading status counts:', error);
                menuList.innerHTML = `
                    <li class="px-3 py-4 text-center text-red-500">
                        Error loading status counts
                    </li>
                `;
                dropdownBtn.disabled = false;
                dropdownBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
    }

    dropdownBtn.addEventListener('click', () => {
        if (!dropdownBtn.disabled) {
            dropdownMenu.classList.toggle('hidden');
        }
    });

    window.addEventListener('click', (e) => {
        if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.classList.add('hidden');
        }
    });

    // Export refresh function
    window.refreshStatusFilter = (year = 'all') => {
        currentYear = year;
        loadStatusCounts(year);
    };

    // Initial load
    loadStatusCounts(initialYear);
}
