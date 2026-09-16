document.addEventListener('DOMContentLoaded', function () {

    const deleteRequestModal = document.getElementById('deleteRequestModal');

    if (deleteRequestModal) {
        const form = document.getElementById('deleteRequestForm');
        const submit = form.querySelector('[type="submit"]');
        const reason = form.querySelector('[name="reason"]');
        const feedback = document.getElementById('deleteRequestFeedback');
        let submitting = false;
        let completed = false;
        let memberRow = null;

        const showFeedback = (message, success = false) => {
            feedback.textContent = message;
            feedback.className = `alert alert-${success ? 'success' : 'danger'}`;
        };

        deleteRequestModal.addEventListener('show.bs.modal', event => {
            form.reset();
            completed = false;
            feedback.className = 'alert d-none';
            feedback.textContent = '';
            const button = event.relatedTarget;
            memberRow = button?.closest('tr');
            const action = button?.dataset.action;
            form.removeAttribute('action');
            submit.disabled = !action;
            reason.disabled = false;
            if (action) form.setAttribute('action', action);
            document.getElementById('deleteRequestMemberName').textContent = button?.dataset.memberName || '';
            const profileId = button?.dataset.profileId;
            document.getElementById('deleteRequestProfileId').textContent = profileId ? `(${profileId})` : '';
            if (!action) showFeedback('Unable to select the member. Close this dialog and try again.');
        });

        deleteRequestModal.addEventListener('hide.bs.modal', event => {
            if (submitting) event.preventDefault();
        });

        deleteRequestModal.addEventListener('hidden.bs.modal', () => {
            if (completed) window.location.reload();
        });

        form.addEventListener('submit', async event => {
            event.preventDefault();
            if (submitting || completed || !form.getAttribute('action') || !form.reportValidity()) return;
            submitting = true;
            submit.disabled = true;
            feedback.className = 'alert d-none';
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { Accept: 'application/json' },
                    body: new FormData(form),
                    credentials: 'same-origin',
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok || response.redirected) {
                    const errors = Object.values(data.errors || {}).flat();
                    const message = response.status === 419
                        ? 'Your session has expired. Refresh the page and try again.'
                        : (errors.join(' ') || data.message || 'Unable to submit the request. Refresh the page and try again.');
                    showFeedback(message);
                    return;
                }
                completed = true;
                memberRow?.remove();
                reason.disabled = true;
                showFeedback(data.message || 'Profile delete request raised successfully.', true);
            } catch {
                showFeedback('Unable to confirm submission. Check your connection and try again; duplicate requests are prevented.');
            } finally {
                submitting = false;
                submit.disabled = completed;
            }
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
