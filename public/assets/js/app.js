document.addEventListener('DOMContentLoaded', function () {
    const burgerBtn = document.getElementById('burgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');

    if (burgerBtn && mobileMenu) {
        burgerBtn.addEventListener('click', function () {
            mobileMenu.classList.toggle('active');
        });
    }

    const maxPriceRange = document.getElementById('max_price_range');
    const maxPriceInput = document.getElementById('max_price');
    const maxPriceValue = document.getElementById('maxPriceValue');

    if (maxPriceRange && maxPriceInput && maxPriceValue) {
        const syncFromRange = () => {
            maxPriceInput.value = maxPriceRange.value;
            maxPriceValue.textContent = maxPriceRange.value;
        };

        const syncFromInput = () => {
            let value = parseInt(maxPriceInput.value || 0, 10);

            if (isNaN(value)) value = 0;
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

    function createColorRow(name = '', hex = '#111111') {
        const row = document.createElement('div');
        row.className = 'admin-color-row';
        row.innerHTML = `
            <input type="text" name="color_names[]" placeholder="Назва кольору, напр. Чорний" value="${name}">
            <input type="color" name="color_hexes[]" value="${hex}" class="admin-color-input">
            <span class="admin-color-live-preview" style="background:${hex};"></span>
            <button type="button" class="admin-remove-color-btn">×</button>
        `;
        return row;
    }

    const addColorBtn = document.getElementById('addColorRow');
    const colorBuilder = document.getElementById('colorBuilder');

    if (addColorBtn && colorBuilder) {
        addColorBtn.addEventListener('click', function () {
            colorBuilder.appendChild(createColorRow());
        });

        colorBuilder.addEventListener('click', function (e) {
            if (e.target.classList.contains('admin-remove-color-btn')) {
                const rows = colorBuilder.querySelectorAll('.admin-color-row');
                const row = e.target.closest('.admin-color-row');

                if (!row) return;

                if (rows.length > 1) {
                    row.remove();
                } else {
                    const textInput = row.querySelector('input[type="text"]');
                    const colorInput = row.querySelector('input[type="color"]');
                    const preview = row.querySelector('.admin-color-live-preview');

                    if (textInput) textInput.value = '';
                    if (colorInput) colorInput.value = '#111111';
                    if (preview) preview.style.background = '#111111';
                }
            }
        });

        colorBuilder.addEventListener('input', function (e) {
            if (e.target.classList.contains('admin-color-input')) {
                const row = e.target.closest('.admin-color-row');
                const preview = row?.querySelector('.admin-color-live-preview');
                if (preview) {
                    preview.style.background = e.target.value;
                }
            }
        });
    }

    const mainImageInput = document.getElementById('main_image');
    const mainImagePreview = document.getElementById('mainImagePreview');

    if (mainImageInput && mainImagePreview) {
        mainImageInput.addEventListener('change', function () {
            mainImagePreview.innerHTML = '';

            const file = this.files?.[0];
            if (!file) {
                mainImagePreview.innerHTML = '<div class="admin-empty-preview">Тут з’явиться головне фото</div>';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                mainImagePreview.innerHTML = `
                    <div class="admin-preview-item large">
                        <img src="${e.target?.result}" alt="preview">
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        });
    }

    const galleryInput = document.getElementById('gallery_images');
    const galleryPreviewNew = document.getElementById('galleryImagesPreviewNew');

    if (galleryInput && galleryPreviewNew) {
        galleryInput.addEventListener('change', function () {
            galleryPreviewNew.innerHTML = '';

            const files = Array.from(this.files || []);
            if (!files.length) {
                galleryPreviewNew.innerHTML = '<div class="admin-empty-preview">Тут з’являться нові фото, які ти додаєш</div>';
                return;
            }

            files.forEach((file) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const item = document.createElement('div');
                    item.className = 'admin-preview-item';
                    item.innerHTML = `<img src="${e.target?.result}" alt="preview">`;
                    galleryPreviewNew.appendChild(item);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    const currentGalleryBox = document.getElementById('currentGalleryBox');

    function ensureGalleryEmptyPlaceholder() {
        if (!currentGalleryBox) return;

        const items = currentGalleryBox.querySelectorAll('.admin-current-gallery-item');
        const existingPlaceholder = currentGalleryBox.querySelector('.gallery-empty-placeholder');

        if (items.length === 0 && !existingPlaceholder) {
            const empty = document.createElement('div');
            empty.className = 'admin-empty-preview gallery-empty-placeholder';
            empty.textContent = 'Фото галереї ще не додані';
            currentGalleryBox.appendChild(empty);
        }

        if (items.length > 0 && existingPlaceholder) {
            existingPlaceholder.remove();
        }
    }

    if (currentGalleryBox) {
        currentGalleryBox.addEventListener('click', function (e) {
            if (e.target.classList.contains('admin-gallery-remove-btn')) {
                const item = e.target.closest('.admin-current-gallery-item');
                if (!item) return;

                item.remove();
                ensureGalleryEmptyPlaceholder();
            }
        });

        ensureGalleryEmptyPlaceholder();
    }

    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.fav-picker-trigger');

        if (trigger) {
            e.preventDefault();
            e.stopPropagation();

            const picker = trigger.closest('[data-fav-picker]');
            const menu = picker ? picker.querySelector('.fav-picker-menu') : null;

            document.querySelectorAll('[data-fav-picker] .fav-picker-menu').forEach(function (item) {
                if (item !== menu) {
                    item.hidden = true;
                }
            });

            document.querySelectorAll('.fav-picker-trigger').forEach(function (btn) {
                if (btn !== trigger) {
                    btn.setAttribute('aria-expanded', 'false');
                }
            });

            if (menu) {
                menu.hidden = !menu.hidden;
                trigger.setAttribute('aria-expanded', menu.hidden ? 'false' : 'true');
            }

            return;
        }

        if (!e.target.closest('[data-fav-picker]')) {
            document.querySelectorAll('[data-fav-picker] .fav-picker-menu').forEach(function (menu) {
                menu.hidden = true;
            });
            document.querySelectorAll('.fav-picker-trigger').forEach(function (btn) {
                btn.setAttribute('aria-expanded', 'false');
            });
        }
    });
});