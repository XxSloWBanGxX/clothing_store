document.addEventListener('DOMContentLoaded', function () {
    const editBtn = document.getElementById('editProfileBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const viewMode = document.getElementById('profileViewMode');
    const editMode = document.getElementById('profileEditMode');
    const passwordModal = document.getElementById('passwordModal');
    const openPassword = document.getElementById('openPasswordModal');
    const closePassword = document.getElementById('closePasswordModal');
    const passwordBackdrop = document.getElementById('passwordModalBackdrop');
    const avatarInput = document.getElementById('staffAvatarInput');
    const avatarSaveBtn = document.getElementById('avatarSaveBtn');
    const avatarFileHint = document.getElementById('avatarFileHint');
    const avatarPreview = document.getElementById('avatarPreview');

    function setEditMode(editing) {
        if (!viewMode || !editMode) return;
        viewMode.style.display = editing ? 'none' : 'grid';
        editMode.style.display = editing ? 'block' : 'none';
        editBtn?.classList.toggle('is-active', editing);
        if (editBtn) {
            editBtn.innerHTML = editing
                ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Скасувати'
                : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Редагувати дані';
        }
    }

    editBtn?.addEventListener('click', function () {
        const editing = editMode.style.display === 'none';
        setEditMode(editing);
    });

    cancelEditBtn?.addEventListener('click', function () {
        setEditMode(false);
    });

    function togglePasswordModal(show) {
        passwordModal?.classList.toggle('open', show);
        passwordModal?.setAttribute('aria-hidden', show ? 'false' : 'true');
    }

    openPassword?.addEventListener('click', function () { togglePasswordModal(true); });
    closePassword?.addEventListener('click', function () { togglePasswordModal(false); });
    passwordBackdrop?.addEventListener('click', function () { togglePasswordModal(false); });

    avatarInput?.addEventListener('change', function () {
        const file = this.files?.[0];
        if (!file) return;

        if (avatarFileHint) {
            avatarFileHint.textContent = 'Обрано: ' + file.name;
            avatarFileHint.classList.add('has-file');
        }

        if (avatarSaveBtn) avatarSaveBtn.disabled = false;

        if (avatarPreview && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                avatarPreview.innerHTML = '<img src="' + e.target.result + '" alt="">';
            };
            reader.readAsDataURL(file);
        }
    });
});
