/**
 * Eventos de conversão do funil (cadastro, billing, PIX).
 */
export function initAnalyticsEvents() {
    const track = window.precifiqueTrack;
    if (typeof track !== 'function') {
        return;
    }

    const page = document.body?.dataset?.analyticsPage;
    if (page) {
        track('page_view', { page });
    }

    document.querySelectorAll('[data-analytics-event]').forEach((el) => {
        el.addEventListener('click', () => {
            const name = el.dataset.analyticsEvent;
            if (name) {
                track(name, { label: el.dataset.analyticsLabel || undefined });
            }
        });
    });

    if (page === 'billing_pix' && document.getElementById('billing-pix-page')?.dataset?.premium === '1') {
        track('pix_confirmed', { plan: 'premium' });
    }
}
