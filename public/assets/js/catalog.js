document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('catalogFilterForm');
    const sortInput = document.getElementById('catalogSortInput');
    const sortPills = Array.from(document.querySelectorAll('.catalog-sort-pill'));
    const filterToggle = document.getElementById('catalogFilterToggle');
    const filterPanel = document.getElementById('catalogPanel');
    const filterClose = document.getElementById('catalogPanelClose');
    const overlay = document.getElementById('catalogOverlay');

    const maxPriceRange = document.getElementById('max_price_range');
    const maxPriceInput = document.getElementById('max_price');
    const maxPriceValue = document.getElementById('maxPriceValue');

    if (maxPriceRange && maxPriceInput && maxPriceValue) {
        const syncFromRange = () => {
            maxPriceInput.value = maxPriceRange.value;
            maxPriceValue.textContent = maxPriceRange.value;
        };

        const syncFromInput = () => {
            let value = parseInt(maxPriceInput.value || maxPriceRange.max, 10);
            if (isNaN(value)) value = parseInt(maxPriceRange.max, 10);
            if (value < 0) value = 0;
            if (value > 5000) value = 5000;
            maxPriceInput.value = value;
            maxPriceRange.value = value;
            maxPriceValue.textContent = value;
        };

        if (maxPriceInput.value === '') {
            maxPriceInput.value = maxPriceRange.value;
        }

        syncFromInput();
        maxPriceRange.addEventListener('input', syncFromRange);
        maxPriceInput.addEventListener('input', syncFromInput);
        maxPriceInput.addEventListener('change', syncFromInput);
    }

    if (sortPills.length && sortInput && filterForm) {
        sortPills.forEach((pill) => {
            pill.addEventListener('click', function () {
                const value = pill.getAttribute('data-sort');
                if (!value || sortInput.value === value) return;

                sortInput.value = value;
                sortPills.forEach((item) => item.classList.toggle('is-active', item === pill));
                filterForm.submit();
            });
        });
    }

    const setFiltersOpen = (open) => {
        if (!filterPanel || !overlay) return;
        filterPanel.classList.toggle('is-open', open);
        overlay.hidden = !open;
        document.body.classList.toggle('catalog-filters-open', open);
    };

    setFiltersOpen(false);

    if (filterToggle) {
        filterToggle.addEventListener('click', () => setFiltersOpen(true));
    }

    if (filterClose) {
        filterClose.addEventListener('click', () => setFiltersOpen(false));
    }

    if (overlay) {
        overlay.addEventListener('click', () => setFiltersOpen(false));
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && filterPanel?.classList.contains('is-open')) {
            setFiltersOpen(false);
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024 && filterPanel?.classList.contains('is-open')) {
            setFiltersOpen(false);
        }
    });
});
