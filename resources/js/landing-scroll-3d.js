/**
 * Landing scroll 3D — transições suaves com follow interpolado (estilo Framer).
 */
const INTENSITY = {
    subtle: { rotateX: 8, rotateY: 3, lift: 28, scale: 0.97, z: -40 },
    medium: { rotateX: 12, rotateY: 5, lift: 44, scale: 0.94, z: -60 },
    strong: { rotateX: 16, rotateY: 6, lift: 52, scale: 0.91, z: -80 },
};

const MOBILE = { lift: 20, scale: 0.98 };
const SMOOTH = 0.11;
const SETTLE = 0.08;

const clamp = (n, min, max) => Math.max(min, Math.min(max, n));
const lerp = (a, b, t) => a + (b - a) * t;

/** easeInOutCubic */
const ease = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);

/** progress 0→1 enquanto a seção atravessa a viewport */
const sectionProgress = (rect, vh) =>
    clamp((vh - rect.top) / (vh + rect.height), 0, 1);

/** curva em 3 pontos: entrada → centro → saída */
const keyframe3 = (p, a, b, c) => {
    const t = ease(p);
    return t <= 0.5 ? lerp(a, b, t / 0.5) : lerp(b, c, (t - 0.5) / 0.5);
};

function createState() {
    return {
        rx: 0, ry: 0, ty: 0, tz: 0, s: 1, op: 1,
        trx: 0, try: 0,
    };
}

function applyState(el, s) {
    if (el.dataset.scroll3dPricing !== undefined) {
        el.style.transform = `translate3d(${s.trx}px, 0, 0) rotateY(${s.try}deg) scale(${s.s.toFixed(4)})`;
        el.style.opacity = s.op.toFixed(3);
        return;
    }

    el.style.transform =
        `rotateX(${s.rx.toFixed(2)}deg) rotateY(${s.ry.toFixed(2)}deg) ` +
        `translate3d(0, ${s.ty.toFixed(1)}px, ${s.tz.toFixed(0)}px) scale(${s.s.toFixed(4)})`;

    if (el.dataset.scroll3dHero !== undefined) {
        el.style.opacity = s.op.toFixed(3);
    }
}

function lerpState(current, target, factor = SMOOTH) {
    let delta = 0;
    for (const key of Object.keys(current)) {
        const diff = target[key] - current[key];
        current[key] += diff * factor;
        delta += Math.abs(diff);
    }
    return delta > SETTLE;
}

function initLandingScroll3d() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }
    if (window.matchMedia('(max-width: 767px)').matches) {
        return;
    }
    if (navigator.connection?.saveData) {
        return;
    }
    const root = document.querySelector('[data-landing-3d]');
    if (!root) return;

    const sections = [...root.querySelectorAll('[data-scroll-3d-section]')];
    const hero = root.querySelector('[data-scroll-3d-hero]');
    const heroInner = hero?.querySelector('.scroll-3d-section__inner');
    const heroOrb = hero?.querySelector('.hero-orb');
    const pricingCards = [...root.querySelectorAll('[data-scroll-3d-pricing]')];
    const card3d = [...root.querySelectorAll('.card-3d:not([data-scroll-3d-pricing])')];

    const states = new Map();
    const targets = new Map();

    const getState = (el) => {
        if (!states.has(el)) {
            states.set(el, createState());
            targets.set(el, createState());
        }
        return { current: states.get(el), target: targets.get(el) };
    };

    if (heroInner) {
        heroInner.dataset.scroll3dHero = '';
    }

    pricingCards.forEach((card) => {
        card.dataset.scroll3dPricing = '';
    });

    card3d.forEach((card) => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            card.style.setProperty('--hover-rx', `${(-y * 7).toFixed(2)}deg`);
            card.style.setProperty('--hover-ry', `${(x * 7).toFixed(2)}deg`);
        });
        card.addEventListener('mouseleave', () => {
            card.style.setProperty('--hover-rx', '0deg');
            card.style.setProperty('--hover-ry', '0deg');
        });
    });

    const computeTargets = () => {
        const vh = window.innerHeight;
        const mobile = window.innerWidth < 768;
        const scrollY = window.scrollY;

        if (heroInner) {
            const { target } = getState(heroInner);
            const p = clamp(scrollY / (vh * 0.85), 0, 1);
            const e = ease(p);

            if (mobile) {
                target.ty = lerp(0, -16, e);
                target.s = lerp(1, 0.98, e);
                target.op = lerp(1, 0.92, e);
                target.rx = target.ry = target.tz = 0;
            } else {
                target.rx = lerp(0, 8, e);
                target.ty = lerp(0, -36, e);
                target.tz = lerp(0, -30, e);
                target.s = lerp(1, 0.94, e);
                target.op = lerp(1, 0.88, e);
                target.ry = 0;
            }
        }

        if (heroOrb && !mobile) {
            const p = clamp(scrollY / (vh * 1.2), 0, 1);
            heroOrb.style.transform =
                `translate3d(0, ${lerp(0, 80, ease(p)).toFixed(1)}px, 0) scale(${lerp(1, 1.12, ease(p)).toFixed(3)})`;
            heroOrb.style.opacity = `${lerp(0.35, 0.15, ease(p)).toFixed(2)}`;
        }

        sections.forEach((section, index) => {
            const inner = section.querySelector('.scroll-3d-section__inner');
            if (!inner) return;

            const { target } = getState(inner);
            const p = sectionProgress(section.getBoundingClientRect(), vh);
            const sign = index % 2 === 0 ? 1 : -1;

            if (mobile) {
                const lift = keyframe3(p, MOBILE.lift, 0, -MOBILE.lift * 0.5);
                target.rx = target.ry = target.tz = 0;
                target.ty = lift;
                target.s = keyframe3(p, MOBILE.scale, 1, MOBILE.scale + 0.01);
                return;
            }

            const key = section.dataset.intensity || 'medium';
            const cfg = INTENSITY[key] || INTENSITY.medium;

            target.rx = keyframe3(p, cfg.rotateX, 0, -cfg.rotateX);
            target.ry = keyframe3(p, cfg.rotateY * sign, 0, -cfg.rotateY * sign);
            target.ty = keyframe3(p, cfg.lift, 0, -cfg.lift * 0.5);
            target.tz = keyframe3(p, cfg.z, 0, cfg.z * 0.4);
            target.s = keyframe3(p, cfg.scale, 1, cfg.scale + 0.025);
        });

        pricingCards.forEach((card) => {
            const section = card.closest('[data-scroll-3d-section]');
            if (!section) return;

            const { target } = getState(card);
            const p = sectionProgress(section.getBoundingClientRect(), vh);
            const t = ease(clamp((p - 0.08) / 0.55, 0, 1));
            const fromLeft = card.dataset.direction === 'left';

            if (window.innerWidth < 768) {
                target.trx = lerp(fromLeft ? -24 : 24, 0, t);
                target.try = 0;
                target.s = lerp(0.96, 1, t);
                target.op = lerp(0.75, 1, t);
                return;
            }

            target.trx = lerp(fromLeft ? -56 : 56, 0, t);
            target.try = lerp(fromLeft ? -14 : 14, 0, t);
            target.s = lerp(0.92, 1, t);
            target.op = lerp(0.65, 1, t);
        });
    };

    let animating = false;
    let lastScroll = Date.now();

    const frame = () => {
        let stillMoving = false;

        states.forEach((current, el) => {
            const target = targets.get(el);
            if (lerpState(current, target)) {
                stillMoving = true;
            }
            applyState(el, current);
        });

        const recentlyScrolled = Date.now() - lastScroll < 180;

        if (stillMoving || recentlyScrolled) {
            requestAnimationFrame(frame);
        } else {
            animating = false;
        }
    };

    const onScroll = () => {
        lastScroll = Date.now();
        computeTargets();
        if (!animating) {
            animating = true;
            requestAnimationFrame(frame);
        }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });

    computeTargets();
    animating = true;
    requestAnimationFrame(frame);

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            computeTargets();
            if (!animating) {
                animating = true;
                requestAnimationFrame(frame);
            }
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLandingScroll3d);
} else {
    initLandingScroll3d();
}
