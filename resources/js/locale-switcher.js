/**
 * Seletor PT/EN — POST com CSRF e recarrega a página.
 */
export function initLocaleSwitcher() {
    document.querySelectorAll('[data-locale-switcher]').forEach((root) => {
        const url = root.dataset.url || '';
        const csrf = root.dataset.csrf || '';
        const current = root.dataset.current || '';

        if (!url || !csrf) {
            return;
        }

        root.querySelectorAll('[data-locale]').forEach((button) => {
            button.addEventListener('click', async (event) => {
                event.preventDefault();

                const locale = button.dataset.locale || '';
                if (!locale || locale === current) {
                    return;
                }

                button.disabled = true;

                const body = new FormData();
                body.append('_token', csrf);
                body.append('locale', locale);

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        body,
                        credentials: 'same-origin',
                        headers: {
                            Accept: 'text/html,application/xhtml+xml',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (response.ok || response.status === 302 || response.status === 419) {
                        window.location.reload();
                    }
                } catch (_) {
                    button.disabled = false;
                }
            });
        });
    });
}
