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