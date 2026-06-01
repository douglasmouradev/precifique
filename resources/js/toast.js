/**
 * Toasts globais — window.toast.success|error|warning|info(message)
 */
const toasts = [];

function pushToast(type, message, duration = 4500) {
    const id = Date.now() + Math.random();
    toasts.push({ id, type, message });
    window.dispatchEvent(new CustomEvent('precifique-toast', { detail: { id, type, message } }));
    setTimeout(() => {
        const i = toasts.findIndex((t) => t.id === id);
        if (i >= 0) toasts.splice(i, 1);
        window.dispatchEvent(new CustomEvent('precifique-toast-remove', { detail: { id } }));
    }, duration);
}

window.toast = {
    success: (msg) => pushToast('success', msg),
    error: (msg) => pushToast('error', msg),
    warning: (msg) => pushToast('warning', msg),
    info: (msg) => pushToast('info', msg),
};

document.addEventListener('alpine:init', () => {
    Alpine.data('toastContainer', () => ({
        items: [],
        init() {
            window.addEventListener('precifique-toast', (e) => {
                this.items.push(e.detail);
            });
            window.addEventListener('precifique-toast-remove', (e) => {
                this.items = this.items.filter((t) => t.id !== e.detail.id);
            });
        },
        remove(id) {
            this.items = this.items.filter((t) => t.id !== id);
        },
    }));
});
