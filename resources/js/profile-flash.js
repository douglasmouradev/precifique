/**
 * Mensagens "Saved." nos formulários de perfil admin.
 */
export function initProfileFlash() {
    document.querySelectorAll('[data-saved-flash]').forEach((el) => {
        window.setTimeout(() => {
            el.classList.add('hidden');
        }, 2000);
    });
}
