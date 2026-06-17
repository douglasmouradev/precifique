/**
 * Modais admin — <dialog> nativo + eventos open-modal / close-modal.
 */
export function initModals() {
    document.querySelectorAll('[data-modal]').forEach((dialog) => {
        if (!(dialog instanceof HTMLDialogElement)) {
            return;
        }

        const name = dialog.dataset.modal || '';

        const open = () => {
            if (!dialog.open) {
                dialog.showModal();
                document.body.classList.add('overflow-y-hidden');
            }
        };

        const close = () => {
            if (dialog.open) {
                dialog.close();
                document.body.classList.remove('overflow-y-hidden');
            }
        };

        if (dialog.hasAttribute('open')) {
            open();
        }

        dialog.querySelectorAll('[data-modal-close]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                close();
            });
        });

        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                close();
            }
        });

        dialog.addEventListener('close', () => {
            document.body.classList.remove('overflow-y-hidden');
        });

        window.addEventListener('open-modal', (event) => {
            if (event.detail === name) {
                open();
            }
        });

        window.addEventListener('close-modal', (event) => {
            if (!event.detail || event.detail === name) {
                close();
            }
        });
    });

    document.querySelectorAll('[data-modal-open]').forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            const name = trigger.dataset.modalOpen || '';
            if (name) {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: name }));
            }
        });
    });
}
