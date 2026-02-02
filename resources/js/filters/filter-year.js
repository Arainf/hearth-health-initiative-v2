export function setupYearFilterRecords( selectedYear = null) {
    const name = 'year-filter';

    const btn   = document.getElementById(`${name}-btn`);
    const menu  = document.getElementById(`${name}-menu`);
    const label = document.getElementById(`${name}-label`);
    const items = menu?.querySelectorAll(`.${name}-dropdown-item`);

    if (!btn || !menu || !label || !items) return;

    if (selectedYear) {
        label.textContent = selectedYear;
    } else {
        label.textContent = 'All Years';
    }

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.classList.toggle('hidden');
    });

    window.addEventListener('click', () => {
        menu.classList.add('hidden');
    });

    items.forEach(item => {
        item.addEventListener('click', () => {
            const value = item.dataset.value;
            const text  = item.textContent.trim();

            label.textContent = text;
            menu.classList.add('hidden');

            // Stage filter
            window.stageFilter?.('year', value);

            // Refresh status counts
            window.refreshStatusFilter?.(value);
        });
    });
}
