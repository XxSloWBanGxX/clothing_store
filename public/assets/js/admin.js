document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('admSidebar');
    const overlay = document.getElementById('admOverlay');
    const toggle = document.getElementById('admMenuToggle');

    if (! sidebar || ! overlay || ! toggle) {
        return;
    }

    const closeSidebar = () => {
        sidebar.classList.remove('is-open');
        overlay.hidden = true;
        document.body.classList.remove('adm-sidebar-open');
    };

    const openSidebar = () => {
        sidebar.classList.add('is-open');
        overlay.hidden = false;
        document.body.classList.add('adm-sidebar-open');
    };

    toggle.addEventListener('click', function () {
        if (sidebar.classList.contains('is-open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    overlay.addEventListener('click', closeSidebar);

    sidebar.querySelectorAll('.adm-nav-link, .adm-foot-link, .adm-foot-btn').forEach(function (link) {
        link.addEventListener('click', closeSidebar);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar.classList.contains('is-open')) {
            closeSidebar();
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 1024) {
            closeSidebar();
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    function normalizeSearchText(value) {
        return (value || '')
            .toString()
            .toLowerCase()
            .replace(/[^\p{L}\p{N}\s]/gu, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function itemHaystack(item) {
        var fromDataset = item.getAttribute('data-search') || item.dataset.search || '';
        var fromName = item.getAttribute('data-name') || '';
        var fromDom = '';

        var titleEl = item.querySelector('.adm-picker-item-body strong');
        if (titleEl) {
            fromDom = titleEl.textContent || '';
        }

        return normalizeSearchText(fromDataset + ' ' + fromName + ' ' + fromDom);
    }

    function matchesSearch(item, query) {
        if (!query) {
            return true;
        }

        var haystack = itemHaystack(item);
        var tokens = query.split(' ').filter(Boolean);

        return tokens.every(function (token) {
            return haystack.indexOf(token) !== -1;
        });
    }

    function setItemVisible(item, visible) {
        item.hidden = !visible;
        item.classList.toggle('is-picker-hidden', !visible);
        item.style.display = visible ? '' : 'none';
    }

    document.querySelectorAll('[data-product-picker]').forEach(function (root) {
        var searchInput = root.querySelector('[data-picker-search]');
        var categorySelect = root.querySelector('[data-picker-category]');
        var items = Array.from(root.querySelectorAll('[data-picker-item]'));
        var countEl = root.querySelector('[data-picker-count]');
        var visibleCountEl = root.querySelector('[data-picker-visible-count]');
        var btnSelectVisible = root.querySelector('[data-picker-select-visible]');
        var btnClearVisible = root.querySelector('[data-picker-clear-visible]');
        var btnClearAll = root.querySelector('[data-picker-clear-all]');

        function updateCount() {
            var selected = root.querySelectorAll('[data-picker-checkbox]:checked').length;
            if (countEl) {
                countEl.textContent = 'Обрано: ' + selected;
            }
        }

        function visibleItems() {
            return items.filter(function (item) {
                return !item.hidden && item.style.display !== 'none';
            });
        }

        function applyFilters() {
            var query = normalizeSearchText(searchInput ? searchInput.value : '');
            var category = categorySelect ? String(categorySelect.value || '') : '';
            var visible = 0;

            items.forEach(function (item) {
                var itemCategory = String(item.getAttribute('data-category') || item.dataset.category || '');
                var matchesCategory = !category || itemCategory === category;
                var show = matchesSearch(item, query) && matchesCategory;
                setItemVisible(item, show);
                if (show) {
                    visible++;
                }
            });

            if (visibleCountEl) {
                visibleCountEl.textContent = visible
                    ? 'Показано ' + visible + ' з ' + items.length + ' товарів'
                    : 'Нічого не знайдено — зміни пошук або категорію';
            }
        }

        items.forEach(function (item) {
            var checkbox = item.querySelector('[data-picker-checkbox]');
            if (!checkbox) {
                return;
            }

            checkbox.addEventListener('change', function () {
                item.classList.toggle('is-checked', checkbox.checked);
                updateCount();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
            searchInput.addEventListener('keyup', applyFilters);
            searchInput.addEventListener('search', applyFilters);
            searchInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });
        }

        if (categorySelect) {
            categorySelect.addEventListener('change', applyFilters);
        }

        if (btnSelectVisible) {
            btnSelectVisible.addEventListener('click', function () {
                visibleItems().forEach(function (item) {
                    var checkbox = item.querySelector('[data-picker-checkbox]');
                    if (checkbox) {
                        checkbox.checked = true;
                        item.classList.add('is-checked');
                    }
                });
                updateCount();
            });
        }

        if (btnClearVisible) {
            btnClearVisible.addEventListener('click', function () {
                visibleItems().forEach(function (item) {
                    var checkbox = item.querySelector('[data-picker-checkbox]');
                    if (checkbox) {
                        checkbox.checked = false;
                        item.classList.remove('is-checked');
                    }
                });
                updateCount();
            });
        }

        if (btnClearAll) {
            btnClearAll.addEventListener('click', function () {
                items.forEach(function (item) {
                    var checkbox = item.querySelector('[data-picker-checkbox]');
                    if (checkbox) {
                        checkbox.checked = false;
                        item.classList.remove('is-checked');
                    }
                });
                updateCount();
            });
        }

        applyFilters();
        updateCount();
    });
});
