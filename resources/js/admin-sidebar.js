/**
 * Menu lateral admin — complemento ao toggle CSS (Escape e resize).
 */
export function initAdminSidebar() {
    const checkbox = document.getElementById('admin-sidebar-check');
    if (!checkbox) {
        return;
    }

    const desktopQuery = window.matchMedia('(min-width: 1024px)');

    const close = () => {
        checkbox.checked = false;
    };

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && checkbox.checked) {
            close();
        }
    });

    desktopQuery.addEventListener('change', () => {
        if (desktopQuery.matches) {
            close();
        }
    });

    document.querySelectorAll('[data-admin-sidebar-close]').forEach((el) => {
        el.addEventListener('click', () => {
            if (!desktopQuery.matches) {
                close();
            }
        });
    });
}
