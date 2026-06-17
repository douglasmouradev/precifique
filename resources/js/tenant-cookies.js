/**
 * Banner de cookies do painel tenant — JS puro.
 */
export function initTenantCookies() {
    const banner = document.getElementById('tenant-cookie-banner');
    const accept = document.getElementById('tenant-cookie-accept');

    if (!banner || !accept) {
        return;
    }

    const hide = () => {
        try {
            localStorage.setItem('precifique_cookies', '1');
        } catch (_) {
            /* storage bloqueado */
        }
        document.documentElement.classList.add('cookies-accepted');
        banner.remove();
    };

    try {
        if (localStorage.getItem('precifique_cookies') === '1') {
            hide();

            return;
        }
    } catch (_) {
        /* ignora */
    }

    accept.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        hide();
    });
}
