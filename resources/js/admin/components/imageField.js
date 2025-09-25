/**
 * Image Field Component Handler
 */
export function initImageField() {
    // Handle image selection
    document.addEventListener('click', function(e) {
        if (e.target.matches('[id$="_select"]') || e.target.closest('[id$="_select"]')) {
            e.preventDefault();
            
            // Check if wp.media is available
            if (typeof wp === 'undefined' || !wp.media) {
                console.warn('WordPress media library not available, retrying...');
                // Retry after a short delay
                setTimeout(() => {
                    if (typeof wp !== 'undefined' && wp.media) {
                        // Re-trigger the click event
                        e.target.click();
                    } else {
                        console.error('WordPress media library still not available');
                    }
                }, 100);
                
                return;
            }
            
            const button = e.target.matches('[id$="_select"]') ? e.target : e.target.closest('[id$="_select"]');
            const fieldId = button.id.replace('_select', '');
            const hiddenInput = document.getElementById(fieldId);
            const preview = document.getElementById(fieldId + '_preview');

            // Create media uploader for this specific field
            const mediaUploader = wp.media({
                title: 'Select Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                hiddenInput.value = attachment.id;
                
                // Update preview with overlay buttons
                const imageUrl = attachment.sizes && attachment.sizes.medium ? 
                    attachment.sizes.medium.url : attachment.url;
                preview.innerHTML = `
                    <img src="${imageUrl}" class="max-w-full h-auto" alt="${attachment.alt || ''}">
                    <div class="image-overlay-actions">
                        <button type="button" class="button button-small" id="${fieldId}_select" title="Change Image">
                            <span class="dashicons dashicons-edit"></span>
                        </button>
                        
                        <button type="button" class="button button-small" id="${fieldId}_remove" title="Remove Image">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                `;
                preview.style.display = 'block';
                
                // Hide the standalone select button
                const standaloneActions = preview.parentElement.querySelector('.image-actions');
                if (standaloneActions) {
                    standaloneActions.style.display = 'none';
                }
            });

            mediaUploader.open();
        }
    });

    // Handle image removal
    document.addEventListener('click', function(e) {
        if (e.target.matches('[id$="_remove"]') || e.target.closest('[id$="_remove"]')) {
            e.preventDefault();
            const button = e.target.matches('[id$="_remove"]') ? e.target : e.target.closest('[id$="_remove"]');
            const fieldId = button.id.replace('_remove', '');
            const hiddenInput = document.getElementById(fieldId);
            const preview = document.getElementById(fieldId + '_preview');
            const wrapper = preview.parentElement;
            const standaloneActions = wrapper.querySelector('.image-actions');

            hiddenInput.value = '';
            preview.style.display = 'none';
            
            // Show the standalone select button
            if (standaloneActions) {
                standaloneActions.style.display = 'flex';
            } else {
                // If .image-actions doesn't exist, create it
                const actionsDiv = document.createElement('div');
                actionsDiv.className = 'image-actions';
                actionsDiv.innerHTML = `
                    <button type="button" class="button button-secondary" id="${fieldId}_select">
                        Select Image
                    </button>
                `;
                wrapper.appendChild(actionsDiv);
            }
        }
    });
}
