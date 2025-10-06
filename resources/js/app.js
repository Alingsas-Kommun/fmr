import { ThemeManager } from './frontend/themeManager.js'
import { themeSwitcher } from './frontend/themeSwitcher.js'
import { tableSort } from './frontend/tableSort.js'
import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

// Initialize theme manager
const themeManager = new ThemeManager();
window.themeManager = themeManager;

// Make components globally available for Alpine.js
window.themeSwitcher = themeSwitcher;
window.tableSort = tableSort;

// Alpine.js is added via Livewire
