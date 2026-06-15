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
    document.getElementById('landing-intro-overlay')?.remove();
    document.body.style.overflow = '';
};

Alpine.data('landingIntro', () => ({
    showIntro: (() => {
        try {
            return !sessionStorage.getItem('precifique_intro_seen');
        } catch (_) {
            return false;
        }
    })(),
    progress: 0,
    phraseVisible: false,
    loadingDone: false,
    scrollProgress: 0,
    focusTrapHandler: null,
    failsafeTimer: null,
    introReady: 'Pronto!',
    introPreparing: 'Carregando…',

    init() {
        try {
            const copy = JSON.parse(this.$el.dataset.introCopy || '{}');
            if (copy.ready) {
                this.introReady = copy.ready;
            }
            if (copy.preparing) {
                this.introPreparing = copy.preparing;
            }
        } catch (_) {
            /* fallback nos defaults */
        }

        this.initScrollProgress();

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            this.closeIntro();

            return;
        }

        if (!this.showIntro) {
            return;
        }

        document.body.style.overflow = 'hidden';
        this.failsafeTimer = setTimeout(() => {
            if (this.showIntro) {
                this.closeIntro();
            }
        }, 4500);

        this.$nextTick(() => {
            this.$refs.introDialog?.focus();
            this.focusTrapHandler = (e) => this.handleFocusTrap(e);
            document.addEventListener('keydown', this.focusTrapHandler);
        });

        const duration = 1600;
        const start = performance.now();
        const easeOut = (t) => 1 - Math.pow(1 - t, 3);
        const step = (now) => {
            const t = Math.min(1, (now - start) / duration);
            this.progress = Math.round(easeOut(t) * 100);
            if (this.progress >= 20) {
                this.phraseVisible = true;
            }
            if (t < 1) {
                requestAnimationFrame(step);
            } else {
                this.loadingDone = true;
                setTimeout(() => this.closeIntro(), 500);
            }
        };
        requestAnimationFrame(step);
    },

    initScrollProgress() {
        const update = () => {
            const el = document.documentElement;
            const max = el.scrollHeight - el.clientHeight;
            this.scrollProgress = max > 0 ? Math.min(100, (el.scrollTop / max) * 100) : 0;
        };
        window.addEventListener('scroll', update, { passive: true });
        update();
    },

    closeIntro() {
        if (this.failsafeTimer) {
            clearTimeout(this.failsafeTimer);
            this.failsafeTimer = null;
        }
        this.showIntro = false;
        if (typeof window.precifiqueCloseIntroOverlay === 'function') {
            window.precifiqueCloseIntroOverlay();
        } else {
            try {
                sessionStorage.setItem('precifique_intro_seen', '1');
            } catch (_) {
                /* storage bloqueado */
            }
            document.body.style.overflow = '';
        }
        if (this.focusTrapHandler) {
            document.removeEventListener('keydown', this.focusTrapHandler);
            this.focusTrapHandler = null;
        }
    },

    handleFocusTrap(e) {
        if (!this.showIntro) {
            return;
        }
        if (e.key === 'Escape') {
            this.closeIntro();

            return;
        }
        if (e.key !== 'Tab') {
            return;
        }
        const container = this.$refs.introDialog;
        if (!container) {
            return;
        }
        const focusable = container.querySelectorAll(
            "button, [href], [tabindex]:not([tabindex='-1'])",
        );
        if (!focusable.length) {
            return;
        }
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    },
}));

window.Alpine = Alpine;
Alpine.start();

// Failsafe: fecha intro se Alpine ou a animação travar
setTimeout(() => {
    const overlay = document.getElementById('landing-intro-overlay');
    if (overlay) {
        window.precifiqueCloseIntroOverlay();
    }
}, 5500);
