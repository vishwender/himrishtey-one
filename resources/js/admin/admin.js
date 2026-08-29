document.addEventListener('DOMContentLoaded', function () {

    const deleteRequestModal = document.getElementById('deleteRequestModal');

    if (deleteRequestModal) {
        deleteRequestModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            if (!button) {
                return;
            }

            const form = document.getElementById('deleteRequestForm');
            const memberName = document.getElementById('deleteRequestMemberName');
            const profileId = document.getElementById('deleteRequestProfileId');

            if (form) {
                form.action = button.dataset.action || '';
            }

            if (memberName) {
                memberName.textContent = button.dataset.memberName || '';
            }

            if (profileId) {
                const value = button.dataset.profileId || '';
                profileId.textContent = value ? `(${value})` : '';
            }
        });

        deleteRequestModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('deleteRequestForm')?.reset();
        });
    }

    const themeToggle = document.getElementById('themeToggle');

    if (themeToggle) {
        const savedTheme = localStorage.getItem('admin-theme');

        applyTheme(savedTheme === 'dark' ? 'dark' : 'light');
        updateThemeIcon();

        themeToggle.addEventListener('click', function () {
            const currentTheme =
                document.documentElement.getAttribute('data-theme');

            if (currentTheme === 'dark') {
                applyTheme('light');
                localStorage.setItem('admin-theme', 'light');
            } else {
                applyTheme('dark');
                localStorage.setItem('admin-theme', 'dark');
            }

            updateThemeIcon();
        });
    }

    const adminWrapper = document.querySelector('.admin-wrapper');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (adminWrapper && sidebarToggle) {
        const sidebarIsCollapsed =
            localStorage.getItem('admin-sidebar-collapsed') === 'true';

        setSidebarState(sidebarIsCollapsed);

        sidebarToggle.addEventListener('click', function () {
            const isCollapsed =
                adminWrapper.classList.toggle('sidebar-collapsed');

            localStorage.setItem(
                'admin-sidebar-collapsed',
                String(isCollapsed)
            );

            updateSidebarToggle(isCollapsed);
        });
    }

    function setSidebarState(isCollapsed) {
        adminWrapper.classList.toggle('sidebar-collapsed', isCollapsed);
        updateSidebarToggle(isCollapsed);
    }

    function updateSidebarToggle(isCollapsed) {
        sidebarToggle.setAttribute('aria-expanded', String(!isCollapsed));
        sidebarToggle.setAttribute(
            'aria-label',
            isCollapsed ? 'Show sidebar' : 'Hide sidebar'
        );

        const icon = sidebarToggle.querySelector('i');

        if (icon) {
            icon.className = isCollapsed
                ? 'bi bi-layout-sidebar'
                : 'bi bi-layout-sidebar-inset';
        }
    }

    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            document.documentElement.setAttribute('data-bs-theme', 'dark');
            return;
        }

        document.documentElement.removeAttribute('data-theme');
        document.documentElement.setAttribute('data-bs-theme', 'light');
    }

    function updateThemeIcon() {

        const isDark =
            document.documentElement.getAttribute('data-theme') === 'dark';

        const icon = themeToggle.querySelector('i');

        if (!icon) {
            return;
        }

        icon.className = isDark
            ? 'bi bi-sun'
            : 'bi bi-moon';
    }
});
