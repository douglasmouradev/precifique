/**
 * Dropdown — JS puro.
 */
export function initDropdowns() {
    document.querySelectorAll('[data-dropdown]').forEach((root) => {
        const trigger = root.querySelector('[data-dropdown-trigger]');
        const panel = root.querySelector('[data-dropdown-panel]');

        if (!trigger || !panel) {
            return;
        }

        let open = false;

        const setOpen = (next) => {
            open = next;
            panel.classList.toggle('hidden', !open);
            trigger.setAttribute('aria-expanded', String(open));
        };

        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            setOpen(!open);
        });

        trigger.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                setOpen(!open);
            }
        });

        panel.addEventListener('click', () => setOpen(false));

        document.addEventListener('click', (event) => {
            if (!open || root.contains(event.target)) {
                return;
            }
            setOpen(false);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && open) {
                setOpen(false);
            }
        });
    });
}
