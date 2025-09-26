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

        // Apply initial theme
        this.applyStoredTheme();
    }

    applyStoredTheme() {
        const storedTheme = this.getStoredTheme();
        this.applyTheme(storedTheme);
    }

    getStoredTheme() {
        return localStorage.getItem('theme') || 'auto';
    }

    setTheme(theme) {
        localStorage.setItem('theme', theme);
        this.applyTheme(theme);
        
        // Dispatch event for other components to listen
        document.dispatchEvent(new CustomEvent('themeChanged', {
            detail: { theme }
        }));
    }

    applyTheme(theme) {
        if (theme === 'dark') {
            this.enableDarkMode();
        } else if (theme === 'light') {
            this.disableDarkMode();
        } else { // auto
            this.setThemeFromSystem();
        }
    }

    setThemeFromSystem() {
        if (this.mediaQuery.matches) {
            this.enableDarkMode();
        } else {
            this.disableDarkMode();
        }
    }

    handleSystemThemeChange(e) {
        // Only respond to system changes if theme is set to auto
        const currentTheme = this.getStoredTheme();
        
        if (currentTheme === 'auto') {
            if (e.matches) {
                this.enableDarkMode();
            } else {
                this.disableDarkMode();
            }
        }
    }

    enableDarkMode() {
        this.html.classList.add('dark');
    }

    disableDarkMode() {
        this.html.classList.remove('dark');
    }

    toggleTheme() {
        const currentTheme = this.getStoredTheme();
        let nextTheme;
        
        switch (currentTheme) {
            case 'light':
                nextTheme = 'dark';
                break;
            case 'dark':
                nextTheme = 'auto';
                break;
            default: // auto
                nextTheme = 'light';
                break;
        }
        
        this.setTheme(nextTheme);

        return nextTheme;
    }

    isDarkMode() {
        return this.html.classList.contains('dark');
    }

    getCurrentTheme() {
        return this.getStoredTheme();
    }
}
