// Theme Manager Class
export class ThemeManager {
    constructor() {
        this.html = document.documentElement;
        this.mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        this.init();
    }

    init() {
        // Listen for system theme changes
        this.mediaQuery.addEventListener('change', (e) => {
            this.handleSystemThemeChange(e);
        });

        // Set initial theme based on system preference
        this.setThemeFromSystem();
    }

    setThemeFromSystem() {
        if (this.mediaQuery.matches) {
            this.enableDarkMode();
        } else {
            this.disableDarkMode();
        }
    }

    handleSystemThemeChange(e) {
        if (e.matches) {
            this.enableDarkMode();
        } else {
            this.disableDarkMode();
        }
    }

    enableDarkMode() {
        this.html.classList.add('dark');
    }

    disableDarkMode() {
        this.html.classList.remove('dark');
    }

    toggleTheme() {
        if (this.html.classList.contains('dark')) {
            this.disableDarkMode();
        } else {
            this.enableDarkMode();
        }
    }

    isDarkMode() {
        return this.html.classList.contains('dark');
    }
}
