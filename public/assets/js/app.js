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

    const thumbButtons = Array.from(document.querySelectorAll('.product-gallery-thumb'));
    const mainImage = document.getElementById('mainProductImage');
    const mainFallback = document.getElementById('mainProductFallback');
    const prevBtn = document.getElementById('productPrevImage');
    const nextBtn = document.getElementById('productNextImage');

    const setGalleryImage = (index) => {
        if (!thumbButtons.length || !mainImage) return;

        const safeIndex = (index + thumbButtons.length) % thumbButtons.length;
        const thumb = thumbButtons[safeIndex];
        const src = thumb.getAttribute('data-image-src');

        if (!src) return;

        mainImage.style.display = 'block';
        mainImage.src = src;
        mainImage.setAttribute('data-current-index', String(safeIndex));

        if (mainFallback) {
            mainFallback.style.display = 'none';
        }

        thumbButtons.forEach((item) => item.classList.remove('active'));
        thumb.classList.add('active');
    };

    thumbButtons.forEach((btn, index) => {
        btn.addEventListener('click', function () {
            setGalleryImage(index);
        });
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            const current = parseInt(mainImage?.getAttribute('data-current-index') || '0', 10);
            setGalleryImage(current - 1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            const current = parseInt(mainImage?.getAttribute('data-current-index') || '0', 10);
            setGalleryImage(current + 1);
        });
    }

    const selectedSizeInput = document.getElementById('selectedSizeInput');
    const sizeButtons = document.querySelectorAll('.product-size-btn');
    sizeButtons.forEach((btn) => {
        btn.addEventListener('click', function () {
            sizeButtons.forEach((item) => item.classList.remove('active'));
            this.classList.add('active');

            if (selectedSizeInput) {
                selectedSizeInput.value = this.getAttribute('data-size') || '';
            }
        });
    });

    const selectedColorNameInput = document.getElementById('selectedColorNameInput');
    const selectedColorHexInput = document.getElementById('selectedColorHexInput');
    const colorButtons = document.querySelectorAll('.product-color-btn');
    colorButtons.forEach((btn) => {
        btn.addEventListener('click', function () {
            colorButtons.forEach((item) => item.classList.remove('active'));
            this.classList.add('active');

            if (selectedColorNameInput) {
                selectedColorNameInput.value = this.getAttribute('data-color-name') || '';
            }

            if (selectedColorHexInput) {
                selectedColorHexInput.value = this.getAttribute('data-color-hex') || '';
            }
        });
    });

    const tabButtons = document.querySelectorAll('.product-tab-btn');
    const tabPanels = document.querySelectorAll('.product-tab-panel');

    tabButtons.forEach((btn) => {
        btn.addEventListener('click', function () {
            const tab = this.getAttribute('data-tab');

            tabButtons.forEach((item) => item.classList.remove('active'));
            tabPanels.forEach((panel) => panel.classList.remove('active'));

            this.classList.add('active');

            const panel = document.querySelector(`.product-tab-panel[data-panel="${tab}"]`);
            if (panel) {
                panel.classList.add('active');
            }
        });
    });

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
});