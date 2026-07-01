/**
 * Menu lateral do painel admin — mobile (JS puro, compatível com CSP).
 */
export function initAdminSidebar() {
    document.documentElement.dataset.adminSidebarInit = '1';

    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('admin-sidebar-overlay');
    const toggle = document.getElementById('admin-sidebar-toggle');
    const closeBtn = document.getElementById('admin-sidebar-close');

    if (!sidebar || !toggle) {
        return;
    }

    const desktopQuery = window.matchMedia('(min-width: 1024px)');
    const isDesktop = () => desktopQuery.matches;

    let open = false;

    const apply = () => {
        const visible = open || isDesktop();
        const offScreen = !isDesktop() && !open;
        sidebar.classList.toggle('-translate-x-full', offScreen);
        sidebar.classList.toggle('pointer-events-none', offScreen);
        sidebar.classList.toggle('is-open', open);
        if (!isDesktop()) {
            if (open) {
                sidebar.style.transform = 'translateX(0)';
                sidebar.style.pointerEvents = 'auto';
            } else {
                sidebar.style.transform = '';
                sidebar.style.pointerEvents = '';
            }
        } else {
            sidebar.style.transform = '';
            sidebar.style.pointerEvents = '';
        }
        sidebar.setAttribute('aria-hidden', String(!visible));
        overlay?.classList.toggle('hidden', !open || isDesktop());
        toggle.setAttribute('aria-expanded', String(open));
        const iconOpen = toggle.querySelector('[data-icon="open"]');
        const iconClose = toggle.querySelector('[data-icon="close"]');
        iconOpen?.classList.toggle('hidden', open);
        iconClose?.classList.toggle('hidden', !open);

        if (!isDesktop()) {
            document.body.style.overflow = open ? 'hidden' : '';
        } else {
            document.body.style.overflow = '';
        }
    };

    const setOpen = (next) => {
        if (isDesktop()) {
            open = false;
        } else {
            open = next;
        }
        apply();
    };

    toggle.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        if (!isDesktop()) {
            setOpen(!open);
        }
    });

    closeBtn?.addEventListener('click', (event) => {
        event.preventDefault();
        setOpen(false);
    });

    overlay?.addEventListener('click', () => setOpen(false));

    document.querySelectorAll('[data-admin-sidebar-close]').forEach((el) => {
        el.addEventListener('click', () => {
            if (!isDesktop()) {
                setOpen(false);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });

    desktopQuery.addEventListener('change', () => {
        if (isDesktop()) {
            open = false;
        }
        apply();
    });

    apply();
}
