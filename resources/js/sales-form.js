/**
 * Formulário de vendas — preço do produto e forma de pagamento.
 */
export function initSalesForms() {
    document.querySelectorAll('[data-sales-form]').forEach((form) => {
        const priceInput = form.querySelector('[data-sales-price]');
        const productSelect = form.querySelector('[data-sales-product]');
        const paymentInput = form.querySelector('[data-sales-payment]');
        const paymentButtons = form.querySelectorAll('[data-sales-payment-option]');

        if (productSelect && priceInput) {
            productSelect.addEventListener('change', () => {
                const option = productSelect.selectedOptions[0];
                priceInput.value = option?.dataset.price || '0';
            });
        }

        if (!paymentInput || paymentButtons.length === 0) {
            return;
        }

        const activeClasses = ['bg-brand', 'border-brand', 'text-ink', 'shadow-sm'];
        const inactiveClasses = ['bg-white', 'border-slate-200', 'text-slate-600', 'hover:border-slate-300'];

        const setPayment = (value) => {
            paymentInput.value = value;

            paymentButtons.forEach((button) => {
                const isActive = button.dataset.salesPaymentOption === value;
                button.setAttribute('aria-pressed', String(isActive));
                button.classList.remove(...activeClasses, ...inactiveClasses);
                button.classList.add(...(isActive ? activeClasses : inactiveClasses));
            });
        };

        paymentButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const value = button.dataset.salesPaymentOption || '';
                if (value) {
                    setPayment(value);
                }
            });
        });

        const initial = paymentInput.value || paymentButtons[0]?.dataset.salesPaymentOption || '';
        if (initial) {
            setPayment(initial);
        }
    });
}
