import './bootstrap';
import './form-validation';

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import collapse from '@alpinejs/collapse';

// Lazy load html2canvas only when needed (saves ~147KB on initial load)
window.html2canvas = async function(...args) {
    const { default: html2canvas } = await import('html2canvas');
    return html2canvas(...args);
};

Alpine.plugin(persist);
Alpine.plugin(collapse);

// Scroll Animation Directive
Alpine.directive('scroll-animate', (el, { expression, modifiers }) => {
    const delay = modifiers.includes('delay') ? parseInt(expression) || 0 : 0;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    el.classList.add('is-visible');
                    el.classList.add('animate-fade-up');
                }, delay);
                observer.unobserve(el);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    el.classList.add('scroll-animate-init');
    observer.observe(el);
});

// Testimonial Carousel Component
Alpine.data('testimonialCarousel', (testimonials = []) => ({
    testimonials: testimonials,
    currentSlide: 0,
    autoplayInterval: null,
    slidesPerView: 3,

    init() {
        this.updateSlidesPerView();
        this.startAutoplay();
        window.addEventListener('resize', () => this.updateSlidesPerView());
    },

    updateSlidesPerView() {
        if (window.innerWidth < 640) {
            this.slidesPerView = 1;
        } else if (window.innerWidth < 1024) {
            this.slidesPerView = 2;
        } else {
            this.slidesPerView = 3;
        }
    },

    get totalSlides() {
        return Math.ceil(this.testimonials.length / this.slidesPerView);
    },

    startAutoplay() {
        this.autoplayInterval = setInterval(() => this.nextSlide(), 5000);
    },

    stopAutoplay() {
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
        }
    },

    nextSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
    },

    prevSlide() {
        this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
    },

    goToSlide(index) {
        this.currentSlide = index;
        this.stopAutoplay();
        this.startAutoplay();
    }
}));

// Counter Animation Component
Alpine.data('counterAnimation', (target = 0, duration = 2000) => ({
    current: 0,
    target: target,
    duration: duration,

    init() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateCounter();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        observer.observe(this.$el);
    },

    animateCounter() {
        const startTime = performance.now();
        const startValue = this.current;

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / this.duration, 1);

            // Easing function (ease-out)
            const easeOut = 1 - Math.pow(1 - progress, 3);

            this.current = Math.round(startValue + (this.target - startValue) * easeOut);

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }
}));

// Theme store for dark mode persistence
Alpine.store('theme', {
    dark: Alpine.$persist(false).as('codexse_dark_mode'),

    init() {
        // Check system preference if no stored preference
        if (localStorage.getItem('codexse_dark_mode') === null) {
            this.dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        this.applyTheme();

        // Listen for system preference changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (localStorage.getItem('codexse_dark_mode') === null) {
                this.dark = e.matches;
                this.applyTheme();
            }
        });
    },

    toggle() {
        this.dark = !this.dark;
        document.documentElement.classList.add('dark-transition');
        this.applyTheme();
        setTimeout(() => {
            document.documentElement.classList.remove('dark-transition');
        }, 300);
    },

    applyTheme() {
        if (this.dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
});

window.Alpine = Alpine;

Alpine.start();
