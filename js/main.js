document.addEventListener("DOMContentLoaded", function () {
    const navbar = document.querySelector('.navbar');

    if (navbar) {
        const currentPath = window.location.pathname;
        const navLinks = navbar.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            const linkPath = link.getAttribute('href');

            if (linkPath === currentPath) {
                link.classList.add('active');
                link.setAttribute('aria-current', 'page');
            } else {
                link.classList.remove('active');
                link.removeAttribute('aria-current');
            }
        });
    }

    const adminSidebarWrapper = document.querySelector('.admin-sidebar-wrapper');
    const adminSidebar = adminSidebarWrapper ? adminSidebarWrapper.querySelector('.admin-sidebar') : null;

    if (adminSidebar && adminSidebarWrapper) {
        const collapseToggle = adminSidebar.querySelector('.sidebar-toggle');
        const mobileToggle = document.querySelector('.admin-mobile-toggle');
        const backdrop = adminSidebarWrapper.querySelector('.admin-sidebar-backdrop');
        const closeButtons = adminSidebar.querySelectorAll('.sidebar-close-btn');
        const body = document.body;
        const mobileQuery = window.matchMedia('(max-width: 992px)');

        const updateCollapseToggle = (isCollapsed) => {
            if (!collapseToggle) return;
            collapseToggle.setAttribute('aria-expanded', String(!isCollapsed));
            const icon = collapseToggle.querySelector('ion-icon');
            if (icon) {
                icon.setAttribute('name', isCollapsed ? 'chevron-forward-outline' : 'chevron-back-outline');
            }
        };

        if (collapseToggle) {
            collapseToggle.addEventListener('click', () => {
                const isCollapsed = adminSidebar.classList.toggle('collapsed');
                updateCollapseToggle(isCollapsed);
            });

            // Ensure initial state is reflected in the toggle icon / aria attributes
            updateCollapseToggle(adminSidebar.classList.contains('collapsed'));
        }

        const updateMobileToggle = (isOpen) => {
            if (!mobileToggle) return;
            mobileToggle.setAttribute('aria-expanded', String(isOpen));
            const icon = mobileToggle.querySelector('ion-icon');
            if (icon) {
                icon.setAttribute('name', isOpen ? 'close-outline' : 'menu-outline');
            }
        };

        const openSidebar = () => {
            adminSidebarWrapper.classList.add('open');
            body.classList.add('admin-sidebar-open');
            adminSidebar.setAttribute('aria-hidden', 'false');
            updateMobileToggle(true);
            if (adminSidebar.classList.contains('collapsed')) {
                adminSidebar.classList.remove('collapsed');
                updateCollapseToggle(false);
            }
        };

        const closeSidebar = () => {
            adminSidebarWrapper.classList.remove('open');
            body.classList.remove('admin-sidebar-open');
            updateMobileToggle(false);
            if (mobileQuery.matches) {
                adminSidebar.setAttribute('aria-hidden', 'true');
            } else {
                adminSidebar.setAttribute('aria-hidden', 'false');
            }
        };

        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                if (adminSidebarWrapper.classList.contains('open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', closeSidebar);
        }

        if (closeButtons.length > 0) {
            closeButtons.forEach((button) => {
                button.addEventListener('click', closeSidebar);
            });
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && adminSidebarWrapper.classList.contains('open')) {
                closeSidebar();
            }
        });

        const syncSidebarForViewport = () => {
            if (mobileQuery.matches) {
                if (adminSidebar.classList.contains('collapsed')) {
                    adminSidebar.classList.remove('collapsed');
                    updateCollapseToggle(false);
                }

                if (adminSidebarWrapper.classList.contains('open')) {
                    adminSidebar.setAttribute('aria-hidden', 'false');
                    body.classList.add('admin-sidebar-open');
                    updateMobileToggle(true);
                } else {
                    adminSidebar.setAttribute('aria-hidden', 'true');
                    body.classList.remove('admin-sidebar-open');
                    updateMobileToggle(false);
                }
            } else {
                adminSidebarWrapper.classList.remove('open');
                adminSidebar.setAttribute('aria-hidden', 'false');
                body.classList.remove('admin-sidebar-open');
                updateMobileToggle(false);
            }
        };

        syncSidebarForViewport();

        if (typeof mobileQuery.addEventListener === 'function') {
            mobileQuery.addEventListener('change', syncSidebarForViewport);
        } else if (typeof mobileQuery.addListener === 'function') {
            mobileQuery.addListener(syncSidebarForViewport);
        }
    }
});