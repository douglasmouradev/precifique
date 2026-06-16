/**
 * Menu lateral do painel tenant — JS puro (sem depender do Alpine).
 */
export function initTenantSidebar() {
    const sidebar = document.getElementById('tenant-sidebar');
    const overlay = document.getElementById('tenant-sidebar-overlay');
    const toggle = document.getElementById('tenant-sidebar-toggle');
    const main = document.getElementById('tenant-main');
    const cookieBanner = document.getElementById('tenant-cookie-banner');

    if (!sidebar || !toggle) {
        return;
    }

    const desktopQuery = window.matchMedia('(min-width: 1024px)');
    const isDesktop = () => desktopQuery.matches;
    const openLabel = toggle.dataset.labelOpen || 'Abrir menu';
    const closeLabel = toggle.dataset.labelClose || 'Fechar menu';
    const iconOpen = toggle.querySelector('[data-icon="open"]');
    const iconClose = toggle.querySelector('[data-icon="close"]');

    let open = false;

    const apply = () => {
        sidebar.classList.toggle('translate-x-0', open);
        sidebar.classList.toggle('-translate-x-full', !open);
        sidebar.classList.toggle('pointer-events-auto', open);
        sidebar.classList.toggle('pointer-events-none', !open);
        sidebar.setAttribute('aria-hidden', String(!open));

        if (overlay) {
            overlay.classList.toggle('hidden', !open);
            overlay.setAttribute('aria-hidden', String(!open));
        }

        if (main) {
            main.classList.toggle('lg:pl-[16.5rem]', open);
        }

        if (cookieBanner) {
            cookieBanner.classList.toggle('lg:left-[16.5rem]', open);
            cookieBanner.classList.toggle('lg:left-0', !open);
        }

        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? closeLabel : openLabel);
        iconOpen?.classList.toggle('hidden', open);
        iconClose?.classList.toggle('hidden', !open);

        if (!isDesktop()) {
            document.body.style.overflow = open ? 'hidden' : '';
        } else {
            document.body.style.overflow = '';
        }
    };

    const setOpen = (next, persist = true) => {
        open = next;
        if (persist) {
            try {
                localStorage.setItem('precifique_sidebar', open ? '1' : '0');
            } catch (_) {
                /* storage bloqueado */
            }
        }
        apply();
    };

    const readSaved = () => {
        try {
            return localStorage.getItem('precifique_sidebar');
        } catch (_) {
            return null;
        }
    };

    const saved = readSaved();
    if (isDesktop() && saved === '1') {
        setOpen(true, false);
    } else {
        setOpen(false, false);
    }

    toggle.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        setOpen(!open);
    });

    document.getElementById('tenant-sidebar-close')?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        setOpen(false);
    });

    overlay?.addEventListener('click', () => setOpen(false));

    sidebar.querySelectorAll('[data-sidebar-close]').forEach((el) => {
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
        if (!isDesktop() && open) {
            document.body.style.overflow = 'hidden';
        } else if (isDesktop()) {
            document.body.style.overflow = '';
        }
    });
}
