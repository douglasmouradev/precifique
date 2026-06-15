import './bootstrap';
import './landing-scroll-3d';

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse';

Alpine.plugin(intersect);
Alpine.plugin(collapse);

window.precifiqueCloseIntroOverlay = function precifiqueCloseIntroOverlay() {
    try {
        sessionStorage.setItem('precifique_intro_seen', '1');
    } catch (_) {
        /* storage bloqueado */
    }

    document.documentElement.classList.add('landing-intro-seen');

    const overlay = document.getElementById('landing-intro-overlay');
    if (overlay) {
        overlay.classList.add('landing-intro--hidden');
        overlay.setAttribute('aria-hidden', 'true');
        window.setTimeout(() => overlay.remove(), 700);
    }

    document.body.style.overflow = '';
};

function initLandingIntro() {
    const overlay = document.getElementById('landing-intro-overlay');
    if (!overlay) {
        return;
    }

    try {
        if (sessionStorage.getItem('precifique_intro_seen')) {
            overlay.remove();

            return;
        }
    } catch (_) {
        overlay.remove();

        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        window.precifiqueCloseIntroOverlay();

        return;
    }

    const pctEl = document.getElementById('landing-intro-pct');
    const barEl = document.getElementById('landing-intro-bar');
    const statusEl = document.getElementById('landing-intro-status');
    const phraseEl = document.getElementById('landing-intro-phrase');
    const ready = overlay.dataset.introReady || 'Pronto!';
    const preparing = overlay.dataset.introPreparing || 'Carregando…';

    document.body.style.overflow = 'hidden';

    let failsafeTimer = window.setTimeout(() => {
        window.precifiqueCloseIntroOverlay();
    }, 4500);

    const duration = 1600;
    const start = performance.now();
    const easeOut = (t) => 1 - Math.pow(1 - t, 3);

    const step = (now) => {
        const t = Math.min(1, (now - start) / duration);
        const progress = Math.round(easeOut(t) * 100);

        if (pctEl) {
            pctEl.textContent = String(progress);
        }
        if (barEl) {
            barEl.style.width = `${progress}%`;
        }
        if (statusEl) {
            statusEl.textContent = t >= 1 ? ready : preparing;
        }
        if (phraseEl && progress >= 20) {
            phraseEl.classList.add('is-visible');
        }

        if (t < 1) {
            requestAnimationFrame(step);
        } else {
            window.clearTimeout(failsafeTimer);
            failsafeTimer = window.setTimeout(() => {
                window.precifiqueCloseIntroOverlay();
            }, 500);
        }
    };

    requestAnimationFrame(step);

    document.getElementById('landing-intro-skip')?.addEventListener('click', (event) => {
        event.preventDefault();
        window.clearTimeout(failsafeTimer);
        window.precifiqueCloseIntroOverlay();
    });
}

function initScrollProgressBar() {
    const fill = document.querySelector('.scroll-progress-3d-top__fill');
    if (!fill) {
        return;
    }

    const update = () => {
        const el = document.documentElement;
        const max = el.scrollHeight - el.clientHeight;
        const progress = max > 0 ? Math.min(100, (el.scrollTop / max) * 100) : 0;
        fill.style.width = `${progress}%`;
    };

    window.addEventListener('scroll', update, { passive: true });
    update();
}

function initScrollReveal() {
    const elements = document.querySelectorAll('.scroll-reveal');
    if (!elements.length) {
        return;
    }

    const reveal = (el) => el.classList.add('is-visible');

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        elements.forEach(reveal);

        return;
    }

    document.documentElement.classList.add('landing-animate');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                reveal(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '-40px 0px' });

    const vh = window.innerHeight;
    elements.forEach((el) => {
        const rect = el.getBoundingClientRect();
        if (rect.top < vh * 0.92 && rect.bottom > 0) {
            reveal(el);
        } else {
            observer.observe(el);
        }
    });
}

function initLandingMobileMenu() {
    const toggle = document.getElementById('landing-mobile-menu-toggle');
    const menu = document.getElementById('landing-mobile-menu');
    if (!toggle || !menu) {
        return;
    }

    const iconOpen = toggle.querySelector('[data-menu-icon="open"]');
    const iconClose = toggle.querySelector('[data-menu-icon="close"]');
    const openLabel = toggle.dataset.labelOpen || 'Abrir menu';
    const closeLabel = toggle.dataset.labelClose || 'Fechar menu';
    let open = false;

    const setOpen = (next) => {
        open = next;
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? closeLabel : openLabel);
        menu.classList.toggle('hidden', !open);
        menu.hidden = !open;
        iconOpen?.classList.toggle('hidden', open);
        iconClose?.classList.toggle('hidden', !open);
    };

    toggle.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        setOpen(!open);
    });

    menu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setOpen(false));
    });

    document.addEventListener('click', (event) => {
        if (!open) {
            return;
        }
        if (toggle.contains(event.target) || menu.contains(event.target)) {
            return;
        }
        setOpen(false);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });
}

function initLandingHeaderScroll() {
    const header = document.getElementById('landing-header');
    if (!header) {
        return;
    }

    const update = () => {
        header.classList.toggle('is-scrolled', window.scrollY > 16);
    };

    window.addEventListener('scroll', update, { passive: true });
    update();
}

function bootLanding() {
    initLandingIntro();
    initScrollProgressBar();
    initScrollReveal();
    initLandingMobileMenu();
    initLandingHeaderScroll();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootLanding);
} else {
    bootLanding();
}

window.Alpine = Alpine;
Alpine.start();

window.setTimeout(() => {
    document.querySelectorAll('.scroll-reveal:not(.is-visible)').forEach((el) => {
        el.classList.add('is-visible');
    });
}, 2500);

window.setTimeout(() => {
    const overlay = document.getElementById('landing-intro-overlay');
    if (overlay) {
        window.precifiqueCloseIntroOverlay();
    }
}, 5500);
