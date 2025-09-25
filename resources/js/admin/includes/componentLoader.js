/**
 * Component loader for admin components
 * Automatically discovers and initializes all component JavaScript files
 */

import { getEnabledComponents, getComponentConfig } from '../componentConfig.js';

// Component registry to store initialized components
const componentRegistry = new Map();

/**
 * Wait for WordPress dependencies to be available
 */
function waitForWordPressDependencies() {
    return new Promise((resolve) => {
        const checkDependencies = () => {
            // Check for essential WordPress dependencies
            if (typeof wp !== 'undefined' && 
                typeof jQuery !== 'undefined' && 
                typeof _ !== 'undefined' && 
                typeof wp.media !== 'undefined') {
                resolve();
                return;
            }
            
            // If dependencies aren't ready, wait a bit more
            setTimeout(checkDependencies, 50);
        };
        
        // Start checking immediately
        checkDependencies();
        
        // Fallback timeout to prevent infinite waiting
        setTimeout(() => {
            console.warn('⚠️ WordPress dependencies not fully loaded, proceeding anyway');
            resolve();
        }, 2000);
    });
}

/**
 * Auto-discover and load all component files
 */
async function loadComponents() {
    // Wait for WordPress dependencies first
    await waitForWordPressDependencies();
    
    // Get enabled components from configuration
    const enabledComponents = getEnabledComponents();
    
    console.log('🚀 Starting component loading...');
    console.log(`📋 Found ${enabledComponents.length} enabled components`);
    
    // Load each component
    const loadPromises = enabledComponents.map(async (componentName) => {
        const config = getComponentConfig(componentName);
        try {
            await loadComponent(componentName);
            console.log(`✅ Loaded: ${config.name}`);
            return { name: componentName, status: 'success' };
        } catch (error) {
            console.warn(`❌ Failed to load: ${config.name}`, error);
            return { name: componentName, status: 'failed', error };
        }
    });
    
    // Wait for all components to load
    const results = await Promise.allSettled(loadPromises);
    
    // Log results
    const successful = results.filter(r => r.status === 'fulfilled' && r.value.status === 'success').length;
    const failed = results.filter(r => r.status === 'rejected' || (r.status === 'fulfilled' && r.value.status === 'failed')).length;
    
    console.log(`🎉 Component loading complete: ${successful}/${enabledComponents.length} successful`);
    if (failed > 0) {
        console.warn(`⚠️ ${failed} components failed to load`);
    }
}

/**
 * Load a specific component
 */
async function loadComponent(componentName) {
    // Skip if already loaded
    if (componentRegistry.has(componentName)) {
        return;
    }
    
    try {
        // Dynamic import of the component
        const componentModule = await import(`./../components/${componentName}.js`);
        
        // Look for init function (e.g., initImageField, initColorField, etc.)
        const initFunctionName = `init${componentName.charAt(0).toUpperCase() + componentName.slice(1)}`;
        
        if (componentModule[initFunctionName] && typeof componentModule[initFunctionName] === 'function') {
            // Initialize the component
            componentModule[initFunctionName]();
            
            // Register as loaded
            componentRegistry.set(componentName, {
                module: componentModule,
                initFunction: componentModule[initFunctionName],
                loaded: true
            });
        } else {
            console.warn(`⚠️ No init function found for component: ${componentName} (expected: ${initFunctionName})`);
        }
    } catch (error) {
        console.error(`❌ Failed to load component: ${componentName}`, error);
        throw error;
    }
}

/**
 * Get component info
 */
function getComponentInfo(componentName) {
    return componentRegistry.get(componentName);
}

/**
 * Check if component is loaded
 */
function isComponentLoaded(componentName) {
    return componentRegistry.has(componentName) && componentRegistry.get(componentName).loaded;
}

/**
 * Reload a specific component
 */
async function reloadComponent(componentName) {
    componentRegistry.delete(componentName);
    await loadComponent(componentName);
}

/**
 * Get all loaded components
 */
function getLoadedComponents() {
    return Array.from(componentRegistry.keys());
}

// Export the component loader functions
export {
    loadComponents,
    loadComponent,
    getComponentInfo,
    isComponentLoaded,
    reloadComponent,
    getLoadedComponents
};
