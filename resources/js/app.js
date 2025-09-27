import { ThemeManager } from './frontend/themeManager.js'
import { themeSwitcher } from './frontend/themeSwitcher.js'
import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

// Initialize theme manager
const themeManager = new ThemeManager();
window.themeManager = themeManager;

// Make themeSwitcher globally available for Alpine.js
window.themeSwitcher = themeSwitcher;

// Alpine.js is added via Livewire
