/**
 * Navegação admin — menu mobile em JS puro.
 */
export function initAdminNavigation() {
    const nav = document.getElementById('admin-navigation');
    const toggle = nav?.querySelector('[data-admin-nav-toggle]');
    const panel = nav?.querySelector('[data-admin-nav-panel]');
    const iconOpen = toggle?.querySelector('[data-admin-nav-icon-open]');
    const iconClose = toggle?.querySelector('[data-admin-nav-icon-close]');

    if (!nav || !toggle || !panel) {
        return;
    }

    let open = false;

    const setOpen = (next) => {
        open = next;
        panel.classList.toggle('hidden', !open);
        toggle.setAttribute('aria-expanded', String(open));
        iconOpen?.classList.toggle('hidden', open);
        iconClose?.classList.toggle('hidden', !open);
    };

    toggle.addEventListener('click', (event) => {
        event.preventDefault();
        setOpen(!open);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && open) {
            setOpen(false);
        }
    });
}
