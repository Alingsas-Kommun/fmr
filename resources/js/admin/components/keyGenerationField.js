/**
 * Key Generation Field Component Handler
 */
export function initKeyGenerationField() {
    // Generate random key
    function generateKey(length = 32) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }

    // Copy to clipboard
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success message
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            setTimeout(() => {
                button.textContent = originalText;
            }, 2000);
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.matches('[id$="_generate"]') || e.target.matches('[id$="_generate_first"]')) {
            e.preventDefault();
            const fieldId = e.target.id.replace(/_generate(_first)?$/, '');
            const hiddenInput = document.getElementById(fieldId);
            const display = document.getElementById(fieldId + '_display');
            const keyDisplay = document.getElementById(fieldId + '_display').parentElement.parentElement;
            
            const newKey = generateKey();
            hiddenInput.value = newKey;
            display.textContent = '*'.repeat(newKey.length);
            
            // Show the key display area
            keyDisplay.style.display = 'block';
            
            // Hide generate first button if it exists
            const generateFirstBtn = document.getElementById(fieldId + '_generate_first');
            if (generateFirstBtn) {
                generateFirstBtn.style.display = 'none';
            }
        }
        
        if (e.target.matches('[id$="_copy"]')) {
            e.preventDefault();
            const fieldId = e.target.id.replace('_copy', '');
            const hiddenInput = document.getElementById(fieldId);
            copyToClipboard(hiddenInput.value);
        }
        
        if (e.target.matches('[id$="_toggle"]')) {
            e.preventDefault();
            const fieldId = e.target.id.replace('_toggle', '');
            const hiddenInput = document.getElementById(fieldId);
            const display = document.getElementById(fieldId + '_display');
            const toggleBtn = e.target;
            
            if (display.textContent.includes('*')) {
                display.textContent = hiddenInput.value;
                toggleBtn.textContent = 'Hide';
            } else {
                display.textContent = '*'.repeat(hiddenInput.value.length);
                toggleBtn.textContent = 'Show';
            }
        }
    });
}
