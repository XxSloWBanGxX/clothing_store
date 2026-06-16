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

    window.addEventListener('resize', function () {
        if (window.innerWidth > 1024) {
            closeSidebar();
        }
    });
});
