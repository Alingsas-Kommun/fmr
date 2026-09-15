import { relationHandler } from './admin/relationHandler.js';
import { multiSelect } from './admin/multiSelect.js';
import { loadComponents } from './admin/includes/componentLoader.js';
import Alpine from 'alpinejs';

// IMPORTANT: Set window globals BEFORE Alpine.start()
window.relationHandler = relationHandler;

window.Alpine = Alpine;
Alpine.data('multiSelect', (config = {}) => multiSelect(config));
Alpine.start();

// Load additional components asynchronously
async function initializeAdmin() {
    try {
        await loadComponents();
        console.log('🎉 All admin components loaded successfully');
    } catch (error) {
        console.error('❌ Error loading admin components:', error);
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAdmin);
} else {
    initializeAdmin();
}