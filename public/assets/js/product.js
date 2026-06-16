document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.product-color-dot[data-color]').forEach((element) => {
        element.style.background = element.dataset.color;
    });

    const galleryRoot = document.querySelector('.product-page-v2 .pd-gallery');
    if (!galleryRoot) {
        return;
    }

    const galleryCounter = document.getElementById('galleryCounter');
    const thumbButtons = Array.from(galleryRoot.querySelectorAll('.pd-gallery-thumb'));
    const mainImage = document.getElementById('mainProductImage');
    const mainFallback = document.getElementById('mainProductFallback');
    const prevBtn = document.getElementById('productPrevImage');
    const nextBtn = document.getElementById('productNextImage');

    let currentIndex = parseInt(mainImage?.getAttribute('data-current-index') || '0', 10);

    const updateGalleryCounter = (index) => {
        if (!galleryCounter || !thumbButtons.length) {
            return;
        }
        galleryCounter.textContent = `${index + 1} / ${thumbButtons.length}`;
    };

    const showFallback = () => {
        if (mainImage) {
            mainImage.style.display = 'none';
            mainImage.classList.remove('is-visible');
        }
        if (mainFallback) {
            mainFallback.style.display = 'flex';
        }
    };

    const showImage = () => {
        if (mainImage) {
            mainImage.style.display = 'block';
            mainImage.classList.add('is-visible');
        }
        if (mainFallback) {
            mainFallback.style.display = 'none';
        }
    };

    const setGalleryImage = (index) => {
        if (!thumbButtons.length || !mainImage) {
            return;
        }

        const safeIndex = ((index % thumbButtons.length) + thumbButtons.length) % thumbButtons.length;
        const thumb = thumbButtons[safeIndex];
        const src = thumb.getAttribute('data-image-src');

        if (!src) {
            return;
        }

        if (safeIndex === currentIndex && mainImage.src === src && mainImage.classList.contains('is-visible')) {
            return;
        }

        currentIndex = safeIndex;
        mainImage.setAttribute('data-current-index', String(safeIndex));

        thumbButtons.forEach((item) => item.classList.remove('active'));
        thumb.classList.add('active');
        thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        updateGalleryCounter(safeIndex);

        mainImage.classList.remove('is-visible');
        mainImage.onload = showImage;
        mainImage.onerror = showFallback;
        mainImage.src = src;
    };

    if (mainImage) {
        mainImage.onload = showImage;
        mainImage.onerror = showFallback;
        if (mainImage.complete && mainImage.naturalWidth > 0) {
            showImage();
        }
    }

    thumbButtons.forEach((btn, index) => {
        btn.addEventListener('click', () => setGalleryImage(index));
    });

    prevBtn?.addEventListener('click', () => setGalleryImage(currentIndex - 1));
    nextBtn?.addEventListener('click', () => setGalleryImage(currentIndex + 1));

    document.addEventListener('keydown', (event) => {
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName || '')) {
            return;
        }
        if (event.key === 'ArrowLeft') {
            setGalleryImage(currentIndex - 1);
        } else if (event.key === 'ArrowRight') {
            setGalleryImage(currentIndex + 1);
        }
    });

    const selectedSizeInput = document.getElementById('selectedSizeInput');
    const selectedSizeLabel = document.getElementById('selectedSizeLabel');
    document.querySelectorAll('.product-size-btn').forEach((btn) => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.product-size-btn').forEach((item) => item.classList.remove('active'));
            this.classList.add('active');
            const size = this.getAttribute('data-size') || '';
            if (selectedSizeInput) {
                selectedSizeInput.value = size;
            }
            if (selectedSizeLabel) {
                selectedSizeLabel.textContent = size;
            }
        });
    });

    const selectedColorNameInput = document.getElementById('selectedColorNameInput');
    const selectedColorHexInput = document.getElementById('selectedColorHexInput');
    const selectedColorLabel = document.getElementById('selectedColorLabel');
    document.querySelectorAll('.product-color-btn').forEach((btn) => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.product-color-btn').forEach((item) => item.classList.remove('active'));
            this.classList.add('active');
            const name = this.getAttribute('data-color-name') || '';
            const hex = this.getAttribute('data-color-hex') || '';
            if (selectedColorNameInput) {
                selectedColorNameInput.value = name;
            }
            if (selectedColorHexInput) {
                selectedColorHexInput.value = hex;
            }
            if (selectedColorLabel) {
                selectedColorLabel.textContent = name;
            }
        });
    });

    document.querySelectorAll('.product-tab-btn').forEach((btn) => {
        btn.addEventListener('click', function () {
            const tab = this.getAttribute('data-tab');
            document.querySelectorAll('.product-tab-btn').forEach((item) => item.classList.remove('active'));
            document.querySelectorAll('.product-tab-panel').forEach((panel) => panel.classList.remove('active'));
            this.classList.add('active');
            document.querySelector(`.product-tab-panel[data-panel="${tab}"]`)?.classList.add('active');
        });
    });

    const qtyInput = document.getElementById('qtyInput');
    const qtyMinus = document.getElementById('qtyMinus');
    const qtyPlus = document.getElementById('qtyPlus');

    if (qtyInput) {
        const max = parseInt(qtyInput.getAttribute('max'), 10) || 99;

        qtyMinus?.addEventListener('click', () => {
            const value = parseInt(qtyInput.value, 10) || 1;
            if (value > 1) {
                qtyInput.value = value - 1;
            }
        });

        qtyPlus?.addEventListener('click', () => {
            const value = parseInt(qtyInput.value, 10) || 1;
            if (value < max) {
                qtyInput.value = value + 1;
            }
        });
    }
});
