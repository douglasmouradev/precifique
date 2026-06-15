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
        window.setTimeout(() => overlay.remove(), 350);
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

document.addEventListener('DOMContentLoaded', () => {
    initLandingIntro();
    initScrollProgressBar();
});

window.Alpine = Alpine;
Alpine.start();

document.documentElement.classList.add('alpine-ready');

window.setTimeout(() => {
    const overlay = document.getElementById('landing-intro-overlay');
    if (overlay) {
        window.precifiqueCloseIntroOverlay();
    }
}, 5500);
