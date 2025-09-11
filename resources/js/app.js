import Alpine from 'alpinejs'
import { ThemeManager } from './themeManager.js'
import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

// Initialize theme manager
const themeManager = new ThemeManager();
window.themeManager = themeManager;

// Initialize Alpine.js
window.Alpine = Alpine
Alpine.start()
