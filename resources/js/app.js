import './bootstrap';

const sidebarLayout = document.querySelector('.app-layout');
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('appSidebar');

if (sidebarLayout && sidebarToggle && sidebar) {
    const storageKey = 'cargotrace-sidebar-collapsed';

    // Restore the previous desktop sidebar preference.
    try {
        if (
            window.innerWidth > 560 &&
            window.localStorage.getItem(storageKey) === 'true'
        ) {
            sidebarLayout.classList.add('is-sidebar-collapsed');
        }
    } catch (error) {
        // The sidebar still works when browser storage is unavailable.
    }

    const updateSidebarButton = () => {
        const isCollapsed = sidebarLayout.classList.contains('is-sidebar-collapsed');

        sidebarToggle.setAttribute('aria-expanded', String(!isCollapsed));
        sidebarToggle.setAttribute(
            'aria-label',
            isCollapsed ? 'Show sidebar' : 'Hide sidebar'
        );
    };

    sidebarToggle.addEventListener('click', () => {
        sidebarLayout.classList.toggle('is-sidebar-collapsed');

        const isCollapsed = sidebarLayout.classList.contains('is-sidebar-collapsed');

        if (window.innerWidth > 560) {
            try {
                window.localStorage.setItem(storageKey, String(isCollapsed));
            } catch (error) {
                // Ignore storage errors; the current toggle remains functional.
            }
        }

        updateSidebarButton();
    });

    sidebar.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 560) {
                sidebarLayout.classList.add('is-sidebar-collapsed');
                updateSidebarButton();
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && window.innerWidth <= 560) {
            sidebarLayout.classList.add('is-sidebar-collapsed');
            updateSidebarButton();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 560) {
            sidebarLayout.classList.remove('is-sidebar-collapsed');
            updateSidebarButton();
        }
    });

    updateSidebarButton();
}