document.addEventListener('DOMContentLoaded', function () {
    const manager = document.querySelector('[data-media-manager]');

    if (! manager) {
        return;
    }

    const mode = manager.dataset.mode || 'create';
    const grid = document.getElementById('mediaGrid');
    const dropzone = document.getElementById('mediaDropzone');
    const galleryInput = document.getElementById('galleryImagesFile');
    const mainInput = document.getElementById('mainImageFile');
    const mainSourceInput = document.getElementById('mainImageSource');
    const galleryOrderInput = document.getElementById('galleryOrderInput');
    const emptyHint = document.getElementById('mediaEmptyHint');

    let newFiles = [];
    let dragSrc = null;

    function updateEmptyHint() {
        if (! emptyHint || ! grid) {
            return;
        }

        emptyHint.hidden = grid.querySelectorAll('.adm-media-item').length > 0;
    }

    function syncGalleryInput() {
        if (! galleryInput) {
            return;
        }

        const mainIndex = mainSourceInput.value.startsWith('new:')
            ? parseInt(mainSourceInput.value.split(':')[1], 10)
            : -1;

        const dt = new DataTransfer();
        newFiles.forEach(function (file, index) {
            if (index !== mainIndex) {
                dt.items.add(file);
            }
        });
        galleryInput.files = dt.files;
    }

    function syncMainInput() {
        if (! mainInput || ! mainSourceInput) {
            return;
        }

        const source = mainSourceInput.value;

        if (source.startsWith('new:')) {
            const index = parseInt(source.split(':')[1], 10);
            const file = newFiles[index];

            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                mainInput.files = dt.files;
                return;
            }
        }

        const dt = new DataTransfer();
        mainInput.files = dt.files;
    }

    function syncGalleryOrder() {
        if (! galleryOrderInput || ! grid) {
            return;
        }

        const ids = Array.from(grid.querySelectorAll('.adm-media-item[data-type="gallery"]'))
            .map(function (item) {
                return item.dataset.id;
            })
            .filter(Boolean);

        galleryOrderInput.value = ids.join(',');
    }

    function clearMainState() {
        grid.querySelectorAll('.adm-media-item').forEach(function (item) {
            item.classList.remove('is-main');
            const star = item.querySelector('[data-action="set-main"]');
            if (star) {
                star.classList.remove('is-active');
            }
        });
    }

    function setMainItem(item) {
        if (! item || ! mainSourceInput) {
            return;
        }

        clearMainState();
        item.classList.add('is-main');

        const star = item.querySelector('[data-action="set-main"]');
        if (star) {
            star.classList.add('is-active');
        }

        const type = item.dataset.type;

        if (type === 'gallery') {
            mainSourceInput.value = 'gallery:' + item.dataset.id;
        } else if (type === 'current-main') {
            mainSourceInput.value = 'current';
        } else if (type === 'new') {
            mainSourceInput.value = 'new:' + item.dataset.index;
        }

        syncMainInput();
    }

    function createNewPreview(file, index) {
        const article = document.createElement('article');
        article.className = 'adm-media-item';
        article.draggable = true;
        article.dataset.type = 'new';
        article.dataset.index = String(index);

        const img = document.createElement('img');
        img.alt = file.name;
        article.appendChild(img);

        const actions = document.createElement('div');
        actions.className = 'adm-media-actions';
        actions.innerHTML = `
            <button type="button" class="adm-media-btn" data-action="set-main" title="Зробити головним">★</button>
            <button type="button" class="adm-media-btn" data-action="remove" title="Прибрати">×</button>
        `;
        article.appendChild(actions);

        const reader = new FileReader();
        reader.onload = function (event) {
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);

        return article;
    }

    function appendFiles(fileList) {
        const files = Array.from(fileList || []).filter(function (file) {
            return file.type.startsWith('image/');
        });

        if (! files.length) {
            return;
        }

        files.forEach(function (file) {
            const index = newFiles.length;
            newFiles.push(file);
            grid.appendChild(createNewPreview(file, index));
        });

        if (mainSourceInput.value === 'none' || mainSourceInput.value === 'new' || mode === 'create') {
            const firstNew = grid.querySelector('.adm-media-item[data-type="new"]');
            if (firstNew) {
                setMainItem(firstNew);
            }
        }

        syncGalleryInput();
        syncGalleryOrder();
        updateEmptyHint();
    }

    function removeItem(item) {
        const type = item.dataset.type;

        if (type === 'gallery') {
            const keepInput = item.querySelector('.keep-gallery-input');
            if (keepInput) {
                keepInput.remove();
            }
        }

        if (type === 'new') {
            const removeIndex = parseInt(item.dataset.index, 10);
            newFiles = newFiles.filter(function (_, index) {
                return index !== removeIndex;
            });

            grid.querySelectorAll('.adm-media-item[data-type="new"]').forEach(function (node) {
                node.remove();
            });

            newFiles.forEach(function (file, index) {
                grid.appendChild(createNewPreview(file, index));
            });

            if (mainSourceInput.value.startsWith('new:')) {
                const currentIndex = parseInt(mainSourceInput.value.split(':')[1], 10);
                const replacement = grid.querySelector('.adm-media-item[data-type="new"]');

                if (currentIndex === removeIndex) {
                    if (replacement) {
                        setMainItem(replacement);
                    } else if (grid.querySelector('[data-type="current-main"]')) {
                        setMainItem(grid.querySelector('[data-type="current-main"]'));
                    } else if (grid.querySelector('[data-type="gallery"]')) {
                        setMainItem(grid.querySelector('[data-type="gallery"]'));
                    } else {
                        mainSourceInput.value = 'remove_main';
                    }
                } else if (replacement) {
                    setMainItem(grid.querySelector('.adm-media-item[data-type="new"][data-index="0"]') || replacement);
                }
            }

            syncGalleryInput();
        }

        if (type === 'current-main') {
            mainSourceInput.value = 'remove_main';
        }

        item.remove();
        syncGalleryOrder();
        syncMainInput();
        updateEmptyHint();
    }

    if (galleryInput) {
        galleryInput.addEventListener('change', function () {
            appendFiles(this.files);
            this.value = '';
        });
    }

    if (dropzone) {
        ['dragenter', 'dragover'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (event) {
                event.preventDefault();
                dropzone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (event) {
                event.preventDefault();
                dropzone.classList.remove('is-dragover');
            });
        });

        dropzone.addEventListener('drop', function (event) {
            appendFiles(event.dataTransfer.files);
        });

        dropzone.addEventListener('click', function () {
            galleryInput?.click();
        });
    }

    if (grid) {
        grid.addEventListener('click', function (event) {
            const button = event.target.closest('[data-action]');
            if (! button) {
                return;
            }

            const item = button.closest('.adm-media-item');
            if (! item) {
                return;
            }

            const action = button.dataset.action;

            if (action === 'set-main') {
                setMainItem(item);
            }

            if (action === 'remove') {
                if (confirm('Прибрати це фото?')) {
                    removeItem(item);
                }
            }
        });

        grid.addEventListener('dragstart', function (event) {
            const item = event.target.closest('.adm-media-item');
            if (! item) {
                return;
            }

            dragSrc = item;
            item.classList.add('is-dragging');
            event.dataTransfer.effectAllowed = 'move';
        });

        grid.addEventListener('dragend', function (event) {
            const item = event.target.closest('.adm-media-item');
            if (item) {
                item.classList.remove('is-dragging');
            }

            syncGalleryOrder();
        });

        grid.addEventListener('dragover', function (event) {
            event.preventDefault();
            const target = event.target.closest('.adm-media-item');

            if (! dragSrc || ! target || dragSrc === target) {
                return;
            }

            const items = Array.from(grid.querySelectorAll('.adm-media-item'));
            const dragIndex = items.indexOf(dragSrc);
            const targetIndex = items.indexOf(target);

            if (dragIndex < targetIndex) {
                target.after(dragSrc);
            } else {
                target.before(dragSrc);
            }
        });
    }

    const initialMain = grid.querySelector('.adm-media-item.is-main') || grid.querySelector('.adm-media-item');
    if (initialMain) {
        setMainItem(initialMain);
    }

    syncGalleryOrder();
    updateEmptyHint();

    manager.closest('form')?.addEventListener('submit', function () {
        syncGalleryInput();
        syncMainInput();
        syncGalleryOrder();
    });
});
