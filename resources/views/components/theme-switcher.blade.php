<div x-data="themeSwitcher()" x-init="init()">
    <button 
        @click="cycleTheme()"
        class="flex items-center justify-center h-12 transition-colors duration-200"
        :aria-label="getButtonAriaLabel()"
        :title="getButtonTitle()"
    >
        <x-heroicon-o-sun x-cloak x-show="currentTheme === 'light'" class="w-7 h-7 text-yellow-500" />
        <x-heroicon-o-moon x-cloak x-show="currentTheme === 'dark'" class="w-6 h-6 text-blue-700" />
        <x-heroicon-o-sun x-cloak x-show="currentTheme === 'auto'" class="w-7 h-7 text-gray-700" />
    </button>

    <script>
        function themeSwitcher() {
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
                    const titles = {
                        'light': '{{ __("Light theme", "fmr") }}',
                        'dark': '{{ __("Dark theme", "fmr") }}',
                        'auto': '{{ __("Auto theme", "fmr") }}'
                    };

                    return titles[this.currentTheme];
                },
                
                getButtonAriaLabel() {
                    const labels = {
                        'light': '{{ __("Switch to dark theme", "fmr") }}',
                        'dark': '{{ __("Switch to auto theme", "fmr") }}',
                        'auto': '{{ __("Switch to light theme", "fmr") }}'
                    };

                    return labels[this.currentTheme];
                }
            }
        }
    </script>
</div>