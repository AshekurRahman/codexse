import './bootstrap';

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import collapse from '@alpinejs/collapse';

Alpine.plugin(persist);
Alpine.plugin(collapse);

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
