export function themeSwitcher() {
    return {
        currentTheme: 'auto',
        
        init() {
            // Initialize from existing ThemeManager
            this.currentTheme = window.themeManager?.getCurrentTheme() || 'auto';
            
            // Listen for theme changes from ThemeManager
            document.addEventListener('themeChanged', (e) => {
                this.currentTheme = e.detail.theme;
            });
        },
        
        cycleTheme() {
            // Cycle through themes: light → dark → auto → light
            let nextTheme;
            switch (this.currentTheme) {
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
        },
        
        setTheme(theme) {
            this.currentTheme = theme;
            
            // Use the existing ThemeManager
            if (window.themeManager) {
                window.themeManager.setTheme(theme);
            }
            
            // Dispatch event for other components
            document.dispatchEvent(new CustomEvent('themeChanged', {
                detail: { theme }
            }));
        },
        
        getButtonTitle() {
            const { __ } = wp.i18n;
            
            const titles = {
                'light': __('Light theme', 'fmr'),
                'dark': __('Dark theme', 'fmr'),
                'auto': __('Auto theme', 'fmr')
            };

            return titles[this.currentTheme];
        },
        
        getButtonAriaLabel() {
            const { __ } = wp.i18n;
            
            const labels = {
                'light': __('Switch to dark theme', 'fmr'),
                'dark': __('Switch to auto theme', 'fmr'),
                'auto': __('Switch to light theme', 'fmr')
            };

            return labels[this.currentTheme];
        }
    }
}
