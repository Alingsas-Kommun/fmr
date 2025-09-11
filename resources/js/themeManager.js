export class ThemeManager {
    constructor() {
        this.html = document.documentElement;
        this.mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        this.init();
    }

    init() {
        this.mediaQuery.addEventListener('change', (e) => {
            this.handleSystemThemeChange(e);
        });

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
