/**
 * Toasts globais — window.toast.success|error|warning|info(message)
 */
const toastStyles = {
    success: { border: 'border-brand/30', dot: 'bg-brand' },
    error: { border: 'border-red-200', dot: 'bg-red-500' },
    warning: { border: 'border-amber-200', dot: 'bg-amber-500' },
    info: { border: 'border-slate-200', dot: 'bg-slate-400' },
};

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function initToastContainer() {
    const root = document.getElementById('toast-container');
    if (!root) {
        return;
    }

    const items = new Map();

    const remove = (id) => {
        const entry = items.get(id);
        if (!entry) {
            return;
        }

        entry.el.remove();
        items.delete(id);
    };

    const render = (detail) => {
        const { id, type, message } = detail;
        const style = toastStyles[type] || toastStyles.info;

        const el = document.createElement('div');
        el.className = `pointer-events-auto rounded-xl border px-4 py-3 text-sm shadow-lg backdrop-blur-sm flex items-start gap-3 bg-white text-slate-700 ${style.border}`;
        el.innerHTML = `
            <span class="shrink-0 w-2 h-2 rounded-full mt-1.5 ${style.dot}"></span>
            <p class="flex-1 leading-snug">${escapeHtml(message)}</p>
            <button type="button" data-toast-close class="text-slate-400 hover:text-slate-600 shrink-0" aria-label="Fechar">×</button>
        `;

        el.querySelector('[data-toast-close]')?.addEventListener('click', () => remove(id));
        root.appendChild(el);
        items.set(id, { el });
    };

    window.addEventListener('precifique-toast', (event) => {
        render(event.detail);
    });

    window.addEventListener('precifique-toast-remove', (event) => {
        remove(event.detail.id);
    });
}

function pushToast(type, message, duration = 4500) {
    const id = Date.now() + Math.random();
    window.dispatchEvent(new CustomEvent('precifique-toast', { detail: { id, type, message } }));
    window.setTimeout(() => {
        window.dispatchEvent(new CustomEvent('precifique-toast-remove', { detail: { id } }));
    }, duration);
}

window.toast = {
    success: (msg) => pushToast('success', msg),
    error: (msg) => pushToast('error', msg),
    warning: (msg) => pushToast('warning', msg),
    info: (msg) => pushToast('info', msg),
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initToastContainer);
} else {
    initToastContainer();
}
