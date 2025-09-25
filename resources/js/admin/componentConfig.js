/**
 * Component configuration file
 * Add new components here to enable auto-loading
 */

export const componentConfig = {
    imageField: {
        name: 'Image Field',
        description: 'Handles image upload and selection with WordPress media library',
        enabled: true,
        priority: 1
    },
    
    colorField: {
        name: 'Color Field', 
        description: 'Color picker with hex input synchronization',
        enabled: true,
        priority: 2
    },
    
    keyGenerationField: {
        name: 'Key Generation Field',
        description: 'API key generation with copy and toggle functionality',
        enabled: true,
        priority: 3
    },
};

/**
 * Get enabled components sorted by priority
 */
export function getEnabledComponents() {
    return Object.entries(componentConfig)
        .filter(([key, config]) => config.enabled)
        .sort(([, a], [, b]) => a.priority - b.priority)
        .map(([key]) => key);
}

/**
 * Get component configuration
 */
export function getComponentConfig(componentName) {
    return componentConfig[componentName] || null;
}
