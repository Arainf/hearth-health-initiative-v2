export function setupYearFilterRecords(selectedYear = null) {

    const name = 'year-filter';

    const btn   = document.getElementById(`${name}-btn`);
    const menu  = document.getElementById(`${name}-menu`);
    const label = document.getElementById(`${name}-label`);
    const items = menu?.querySelectorAll(`.${name}-dropdown-item`);
    const chevron = btn?.querySelector('svg');

    if (!btn || !menu || !label || !items) return;

    if (selectedYear) {
        label.textContent = selectedYear;
    } else {
        label.textContent = 'All ';
    }

    btn.addEventListener('click', (e) => {
        e.stopPropagation();

        const isOpen = menu.classList.contains('show');

        if (isOpen) {
            menu.classList.remove('show');
            chevron?.classList.remove('chevron-rotate');
        } else {
            menu.classList.add('show');
            chevron?.classList.add('chevron-rotate');
        }
    });

    window.addEventListener('click', () => {
        menu.classList.remove('show');
        chevron?.classList.remove('chevron-rotate');
    });

    items.forEach(item => {
        item.addEventListener('click', () => {

            const value = item.dataset.value;
            const text  = item.textContent.trim();

            label.textContent = text;

            menu.classList.remove('show');
            chevron?.classList.remove('chevron-rotate');

            // Stage filter
            window.stageFilter?.('year', value);

            // Refresh status counts
            window.refreshStatusFilter?.(value);

        });
    });
}
