/**
 * Confirmação de envio de formulário com <dialog> nativo.
 */
const OPEN_GUARD_MS = 500;

let navigatedAt = Date.now();

window.addEventListener('pageshow', () => {
    navigatedAt = Date.now();
});

export function initConfirmSubmit() {
    document.querySelectorAll('[data-confirm-submit]').forEach((root) => {
        const dialog = root.querySelector('[data-confirm-submit-dialog]');
        const confirmBtn = root.querySelector('[data-confirm-submit-confirm]');

        if (!dialog || !(dialog instanceof HTMLDialogElement) || !confirmBtn) {
            return;
        }

        const form = root.querySelector('form');
        const trigger = root.querySelector('[data-confirm-submit-trigger]');

        if (!form || !trigger) {
            return;
        }

        trigger.addEventListener('click', (event) => {
            event.preventDefault();

            if (Date.now() - navigatedAt < OPEN_GUARD_MS) {
                return;
            }

            if (!dialog.open) {
                dialog.showModal();
            }
        });

        root.querySelectorAll('[data-confirm-submit-cancel]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                dialog.close();
            });
        });

        confirmBtn.addEventListener('click', (event) => {
            event.preventDefault();
            dialog.close();
            form.requestSubmit();
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
