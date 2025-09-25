/**
 * Color Field Component Handler
 */
export function initColorField() {
    document.addEventListener('input', function(e) {
        if (e.target.matches('.color-picker')) {
            const textInput = document.getElementById(e.target.id + '_text');
            if (textInput) {
                textInput.value = e.target.value;
            }
            
            updateCssVariable(e.target);
        }
        
        if (e.target.matches('.color-text-input')) {
            const colorInput = document.getElementById(e.target.id.replace('_text', ''));
            if (colorInput) {
                // Validate hex color format
                if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
                    colorInput.value = e.target.value;
                    e.target.style.borderColor = '#ddd'; // Reset error state
                    
                    updateCssVariable(colorInput);
                } else if (e.target.value.length === 7) { // Only show error when user has typed 7 characters
                    e.target.style.borderColor = '#dc3232'; // Red border for invalid color
                }
            }
        }
    });

    // Handle paste events for better UX
    document.addEventListener('paste', function(e) {
        if (e.target.matches('.color-text-input')) {
            setTimeout(() => {
                const colorInput = document.getElementById(e.target.id.replace('_text', ''));
                if (colorInput && /^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
                    colorInput.value = e.target.value;
                    e.target.style.borderColor = '#ddd';
                    
                    updateCssVariable(colorInput);
                }
            }, 10);
        }
    });

    // Handle reset button clicks
    document.addEventListener('click', function(e) {
        if (e.target.matches('.color-reset') || e.target.closest('.color-reset')) {
            e.preventDefault();
            const button = e.target.matches('.color-reset') ? e.target : e.target.closest('.color-reset');
            const fieldId = button.id.replace('_reset', '');
            const colorInput = document.getElementById(fieldId);
            const textInput = document.getElementById(fieldId + '_text');
            
            if (colorInput && textInput) {
                const defaultValue = colorInput.dataset.default || '#000000';
                colorInput.value = defaultValue;
                textInput.value = defaultValue;
                textInput.style.borderColor = '#ddd'; // Reset error state
                
                updateCssVariable(colorInput);
            }
        }
    });
}

/**
 * Update CSS variable in the document root
 */
function updateCssVariable(colorInput) {
    const cssVar = colorInput.dataset.cssVar || colorInput.dataset['css-var'];
    
    if (cssVar && colorInput.value) {
        document.documentElement.style.setProperty(cssVar, colorInput.value);
    }
}
