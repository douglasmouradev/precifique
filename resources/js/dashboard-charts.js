import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const brand = '#00C896';

export function initDashboardCharts(data) {
    if (!data) return;

    Chart.defaults.font.family = '"Plus Jakarta Sans", sans-serif';
    Chart.defaults.color = '#64748b';

    const revenueEl = document.getElementById('revenueChart');
    if (revenueEl) {
        new Chart(revenueEl, {
            type: 'line',
            data: {
                labels: data.revenueLabels,
                datasets: [{
                    data: data.revenueTotals,
                    borderColor: brand,
                    backgroundColor: brand + '20',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: brand,
                }],
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } },
                },
            },
        });
    }

    const paymentEl = document.getElementById('paymentChart');
    if (paymentEl && data.paymentLabels?.length) {
        const total = data.paymentCounts.reduce((a, b) => a + b, 0);
        const counts = total > 0 ? data.paymentCounts : data.paymentCounts.map(() => 1);
        new Chart(paymentEl, {
            type: 'doughnut',
            data: {
                labels: data.paymentLabels,
                datasets: [{
                    data: counts,
                    backgroundColor: data.paymentColors || ['#00C896', '#0D0D0D', '#6366f1'],
                    borderWidth: 0,
                }],
            },
            options: {
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16 } },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const real = data.paymentCounts[ctx.dataIndex] ?? 0;
                                if (total === 0) return `${ctx.label}: 0 vendas`;
                                const pct = total > 0 ? Math.round((real / total) * 100) : 0;
                                return `${ctx.label}: ${real} (${pct}%)`;
                            },
                        },
                    },
                },
                cutout: '65%',
            },
        });
    }

    const topEl = document.getElementById('topProductsChart');
    if (topEl) {
        new Chart(topEl, {
            type: 'bar',
            data: {
                labels: data.topProductLabels,
                datasets: [{ data: data.topProductQty, backgroundColor: brand, borderRadius: 8 }],
            },
            options: {
                plugins: { legend: { display: false } },
                indexAxis: 'y',
                scales: {
                    x: { grid: { color: '#f1f5f9' } },
                    y: { grid: { display: false } },
                },
            },
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.dashboardChartData) {
        initDashboardCharts(window.dashboardChartData);
    }
});
