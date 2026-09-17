

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

const initializeGallery = () => {
    const galleryPage = document.querySelector('[data-gallery-page]');

    if (!galleryPage) {
        return;
    }

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const hero = galleryPage.querySelector('[data-gallery-hero]');
    const filter = galleryPage.querySelector('[data-gallery-filter]');
    const itemContainer = galleryPage.querySelector('[data-gallery-items]');
    const pagination = galleryPage.querySelector('[data-gallery-pagination]');
    const sentinel = galleryPage.querySelector('[data-gallery-sentinel]');

    document.documentElement.classList.add('gallery-motion');

    const revealItems = (items) => {
        items.forEach((item, index) => {
            item.style.setProperty('--gallery-delay', `${Math.min(index * 42, 420)}ms`);
            item.classList.add('is-visible');
        });
    };

    if (reducedMotion) {
        hero?.classList.add('is-visible');
        filter?.classList.add('is-visible');
        revealItems(Array.from(galleryPage.querySelectorAll('[data-gallery-item]')));
    } else {
        requestAnimationFrame(() => {
            hero?.classList.add('is-visible');
            filter?.classList.add('is-visible');
        });

        if (itemContainer) {
            const revealObserver = new IntersectionObserver((entries) => {
                if (!entries.some((entry) => entry.isIntersecting)) {
                    return;
                }

                revealItems(Array.from(itemContainer.querySelectorAll('[data-gallery-item]:not(.is-visible)')));
                revealObserver.disconnect();
            }, { threshold: 0.03, rootMargin: '0px 0px 10% 0px' });

            revealObserver.observe(itemContainer);
        }
    }

    filter?.addEventListener('submit', () => {
        ['event', 'location', 'category', 'photographer', 'date', 'daypart', 'sort'].forEach((name) => {
            const controls = Array.from(filter.querySelectorAll(`[name="${name}"]`));
            const visibleControl = controls.find((control) => control.offsetParent !== null) ?? controls[0];

            controls.forEach((control) => {
                control.disabled = control !== visibleControl;
            });
        });
    });

    if (!itemContainer || !pagination || !sentinel || !pagination.dataset.nextPage) {
        return;
    }

    document.documentElement.classList.add('gallery-enhanced');
    const loading = pagination.querySelector('[data-gallery-loading]');
    const error = pagination.querySelector('[data-gallery-error]');
    const retry = pagination.querySelector('[data-gallery-retry]');
    const loadedPhotoIds = new Set(Array.from(itemContainer.querySelectorAll('[data-photo-id]')).map((item) => item.dataset.photoId));
    let nextPage = pagination.dataset.nextPage;
    let isLoading = false;

    const loadNextPage = async () => {
        if (isLoading || !nextPage) {
            return;
        }

        isLoading = true;
        loading?.classList.remove('hidden');
        error?.classList.add('hidden');

        try {
            const response = await fetch(nextPage, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });

            if (!response.ok) {
                throw new Error(`Gallery request failed with ${response.status}`);
            }

            const documentFragment = new DOMParser().parseFromString(await response.text(), 'text/html');
            const newItems = Array.from(documentFragment.querySelectorAll('[data-gallery-item]')).filter((item) => !loadedPhotoIds.has(item.dataset.photoId));

            newItems.forEach((item) => {
                loadedPhotoIds.add(item.dataset.photoId);
                itemContainer.append(item);
            });

            revealItems(newItems);
            nextPage = documentFragment.querySelector('[data-gallery-pagination]')?.dataset.nextPage ?? '';
            pagination.dataset.nextPage = nextPage;

            if (!nextPage) {
                infiniteObserver.disconnect();
            }
        } catch (loadError) {
            error?.classList.remove('hidden');
        } finally {
            isLoading = false;
            loading?.classList.add('hidden');
        }
    };

    const infiniteObserver = new IntersectionObserver((entries) => {
        if (entries.some((entry) => entry.isIntersecting)) {
            loadNextPage();
        }
    }, { rootMargin: '800px 0px' });

    infiniteObserver.observe(sentinel);
    retry?.addEventListener('click', loadNextPage);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeGallery, { once: true });
} else {
    initializeGallery();
}
