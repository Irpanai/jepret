

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const initializeHomepageMotion = () => {
    const animatedElements = document.querySelectorAll('[data-reveal], [data-hero-reveal]');

    if (animatedElements.length === 0) {
        return;
    }

    document.documentElement.classList.add('homepage-motion');

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        animatedElements.forEach((element) => element.classList.add('is-visible'));

        return;
    }

    requestAnimationFrame(() => {
        document.querySelectorAll('[data-hero-reveal]').forEach((element) => {
            element.classList.add('is-visible');
        });
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.14,
        rootMargin: '0px 0px -8% 0px',
    });

    document.querySelectorAll('[data-reveal]').forEach((element) => observer.observe(element));
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeHomepageMotion, { once: true });
} else {
    initializeHomepageMotion();
}
