/**
 * Página PIX — polling de confirmação de pagamento.
 */
export function initBillingPix() {
    const root = document.getElementById('billing-pix-page');
    if (!root) {
        return;
    }

    const statusUrl = root.dataset.statusUrl || '';
    const dashboardUrl = root.dataset.dashboardUrl || '';
    const toastMessage = root.dataset.toastMessage || '';
    const waiting = root.querySelector('[data-pix-waiting]');
    const confirmed = root.querySelector('[data-pix-confirmed]');
    const checking = root.querySelector('[data-pix-checking]');

    let premium = root.dataset.premium === '1';
    let timer = null;

    const showPremium = () => {
        premium = true;
        waiting?.classList.add('hidden');
        checking?.classList.add('hidden');
        confirmed?.classList.remove('hidden');
    };

    const checkStatus = async () => {
        if (!statusUrl || premium) {
            return;
        }

        try {
            const response = await fetch(statusUrl, { headers: { Accept: 'application/json' } });
            if (!response.ok) {
                return;
            }

            const data = await response.json();
            if (data.premium) {
                showPremium();
                window.clearInterval(timer);
                window.toast?.success(toastMessage);
                window.setTimeout(() => {
                    window.location.href = dashboardUrl;
                }, 1500);
            }
        } catch (_) {
            /* ignora */
        }
    };

    if (!premium && statusUrl) {
        timer = window.setInterval(checkStatus, 5000);
    }
}
