import './bootstrap';
import './toast';
import { initTenantSidebar } from './tenant-sidebar';
import { initConfirmDelete } from './confirm-delete';
import { initConfirmSubmit } from './confirm-submit';
import { initTenantCookies } from './tenant-cookies';
import { initNotificationBell } from './notification-bell';
import { initLocaleSwitcher } from './locale-switcher';
import { initDropdowns } from './dropdown';
import { initModals } from './modal';
import { initProfileFlash } from './profile-flash';
import { initAnalyticsEvents } from './analytics-events';

const lazyModules = [
    { selector: '#tenant-ai-assistant', load: () => import('./tenant-ai-assistant').then((m) => m.initTenantAiAssistant) },
    { selector: '#admin-navigation', load: () => import('./admin-navigation').then((m) => m.initAdminNavigation) },
    { selector: '[data-sales-form]', load: () => import('./sales-form').then((m) => m.initSalesForms) },
    { selector: '#billing-pix-page', load: () => import('./billing-pix').then((m) => m.initBillingPix) },
    { selector: '#pricing-wizard', load: () => import('./pricing-wizard').then((m) => m.initPricingWizard) },
];

async function bootApp() {
    initTenantSidebar();
    initConfirmDelete();
    initConfirmSubmit();
    initTenantCookies();
    initNotificationBell();
    initLocaleSwitcher();
    initDropdowns();
    initModals();
    initProfileFlash();
    initAnalyticsEvents();

    await Promise.all(
        lazyModules
            .filter((mod) => document.querySelector(mod.selector))
            .map(async (mod) => {
                const init = await mod.load();
                init();
            }),
    );
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootApp);
} else {
    bootApp();
}

if ('serviceWorker' in navigator && document.body?.dataset?.sw !== 'off') {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}
