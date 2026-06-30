/**
 * Menu lateral do painel admin — mobile (JS puro, compatível com CSP).
 */
export function initAdminSidebar() {
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
        sidebar.classList.toggle('is-open', open);
        sidebar.setAttribute('aria-hidden', String(!visible));
        overlay?.classList.toggle('hidden', !open || isDesktop());
        toggle.setAttribute('aria-expanded', String(open));

        if (!isDesktop()) {
            document.body.style.overflow = open ? 'hidden' : '';
        } else {
            document.body.style.overflow = '';
        }
    };

    const setOpen = (next) => {
        open = isDesktop() ? false : next;
        apply();
    };

    toggle.addEventListener('click', (event) => {
        event.preventDefault();
        if (isDesktop()) {
            return;
        }
        setOpen(!open);
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
