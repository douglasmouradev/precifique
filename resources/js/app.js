import './bootstrap';
import './toast';
import { initTenantSidebar } from './tenant-sidebar';
import { initConfirmDelete } from './confirm-delete';
import { initTenantCookies } from './tenant-cookies';
import { initNotificationBell } from './notification-bell';
import { initLocaleSwitcher } from './locale-switcher';
import { initTenantAiAssistant } from './tenant-ai-assistant';
import { initDropdowns } from './dropdown';
import { initAdminNavigation } from './admin-navigation';
import { initSalesForms } from './sales-form';
import { initBillingPix } from './billing-pix';
import { initPricingWizard } from './pricing-wizard';
import { initModals } from './modal';
import { initProfileFlash } from './profile-flash';

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse';

Alpine.plugin(intersect);
Alpine.plugin(collapse);

window.Alpine = Alpine;

Alpine.start();

function bootApp() {
    initTenantSidebar();
    initConfirmDelete();
    initTenantCookies();
    initNotificationBell();
    initLocaleSwitcher();
    initTenantAiAssistant();
    initDropdowns();
    initAdminNavigation();
    initSalesForms();
    initBillingPix();
    initPricingWizard();
    initModals();
    initProfileFlash();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootApp);
} else {
    bootApp();
}

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}
