/**
 * Modais de confirmação de exclusão — JS puro com <dialog> nativo.
 */
const OPEN_GUARD_MS = 500;

let navigatedAt = Date.now();

window.addEventListener('pageshow', () => {
    navigatedAt = Date.now();
});

export function initConfirmDelete() {
    document.querySelectorAll('[data-confirm-delete]').forEach((root) => {
        const trigger = root.querySelector('[data-confirm-delete-trigger]');
        const dialog = root.querySelector('[data-confirm-delete-dialog]');

        if (!trigger || !dialog || !(dialog instanceof HTMLDialogElement)) {
            return;
        }

        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            if (Date.now() - navigatedAt < OPEN_GUARD_MS) {
                return;
            }

            if (!dialog.open) {
                dialog.showModal();
            }
        });

        root.querySelectorAll('[data-confirm-delete-cancel]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                dialog.close();
            });
        });

        dialog.addEventListener('cancel', (event) => {
            event.preventDefault();
            dialog.close();
        });

        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    });
}
