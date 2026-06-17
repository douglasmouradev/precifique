/**
 * Calculadora demo da landing — JS puro.
 */
export function initLandingPricingDemo() {
    const root = document.getElementById('landing-pricing-demo');
    if (!root) {
        return;
    }

    const cost = Number(root.dataset.cost || 25.5);
    let margin = 50;

    const priceEl = root.querySelector('[data-demo-price]');
    const costEl = root.querySelector('[data-demo-cost]');
    const profitEl = root.querySelector('[data-demo-profit]');
    const marginLabel = root.querySelector('[data-demo-margin-label]');
    const buttons = root.querySelectorAll('[data-demo-margin]');

    const fmt = (value) => {
        const locale = document.documentElement.lang?.replace('_', '-') || 'pt-BR';

        return Number(value).toLocaleString(locale, { style: 'currency', currency: 'BRL' });
    };

    const render = () => {
        const profit = cost * (margin / 100);
        const price = cost + profit;

        if (priceEl) {
            priceEl.textContent = fmt(price);
        }
        if (costEl) {
            costEl.textContent = fmt(cost);
        }
        if (profitEl) {
            profitEl.textContent = `+ ${fmt(profit)}`;
        }
        if (marginLabel) {
            marginLabel.textContent = String(margin);
        }

        buttons.forEach((button) => {
            const value = Number(button.dataset.demoMargin);
            const active = value === margin;
            button.classList.toggle('bg-brand', active);
            button.classList.toggle('text-ink', active);
            button.classList.toggle('bg-white/10', !active);
            button.classList.toggle('text-gray-300', !active);
        });
    };

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            margin = Number(button.dataset.demoMargin || 50);
            render();
        });
    });

    render();
}
