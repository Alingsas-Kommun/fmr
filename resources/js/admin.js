import { relationHandler } from './admin/relationHandler.js';
import { loadComponents } from './admin/includes/componentLoader.js';

// Initialize relation handler
window.relationHandler = relationHandler;

// Load all components with better error handling
async function initializeAdmin() {
    try {
        await loadComponents();
        console.log('🎉 All admin components loaded successfully');
    } catch (error) {
        console.error('❌ Error loading admin components:', error);
        // Don't let component loading errors break the entire admin
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAdmin);
} else {
    initializeAdmin();
}