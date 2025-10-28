import { relationHandler } from './admin/relationHandler.js';
import { loadComponents } from './admin/includes/componentLoader.js';
import Alpine from 'alpinejs';

// IMPORTANT: Set window globals BEFORE Alpine.start()
window.relationHandler = relationHandler;

// Set Alpine on window and start it immediately
window.Alpine = Alpine;
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