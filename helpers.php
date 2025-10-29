<?php

if (! function_exists('get_meta_field')) {
    function get_meta_field($post_id, $field_key, $visibility = false) {
        if ($visibility) {
            $visibility_key = $field_key . '_visibility';
            $is_visible = get_post_meta($post_id, $visibility_key, true);
            
            if ($is_visible === false || $is_visible === '0' || $is_visible === '') {
                return null;
            }
        }
        
        return get_post_meta($post_id, $field_key, true);
    }
}

if (! function_exists('get_site_locale')) {
    /**
     * Get the current WordPress site locale for Carbon date formatting.
     * Falls back to default locale from config if WordPress function is not available.
     *
     * @return string The locale string (e.g., 'sv_SE', 'en_US')
     */
    function get_site_locale() {
        if (function_exists('get_locale')) {
            return get_locale();
        }
        
        // Fallback to config default if WordPress is not loaded
        return config('routes.default_locale', 'sv') === 'sv' ? 'sv_SE' : 'en_US';
    }
}