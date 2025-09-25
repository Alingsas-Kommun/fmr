<?php

namespace App\Core\Admin\Abstracts;

use App\Core\Admin\Traits\FieldGroupTrait;

/**
 * Abstract FieldGroup class for creating custom meta boxes
 * 
 * Supports both single and multiple post types by setting the $post_types property:
 */
abstract class FieldGroup
{
    use FieldGroupTrait;

    protected static $post_types = [];

    abstract protected function getTitle();

    public function __construct()
    {
        $this->title = $this->getTitle();
        
        add_action('add_meta_boxes', [$this, 'register']);
        
        // Register save action for each post type
        foreach (static::$post_types as $post_type) {
            add_action('save_post_' . $post_type, [$this, 'save'], 10, 2);
        }
    }

    public function register()
    {
        // Register meta box for each post type
        foreach (static::$post_types as $post_type) {
            add_meta_box(
                static::$id,
                $this->title,
                [$this, 'render'],
                $post_type,
                static::$context,
                static::$priority
            );
        }
    }


    public function save($post_id, $post)
    {
        if (!isset($_POST[static::$id . '_nonce']) || 
            !wp_verify_nonce($_POST[static::$id . '_nonce'], static::$id . '_nonce_action')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Use flattened fields for efficient processing
        $fields = $this->getFlattenedFields();
        $this->handleFieldSave($fields, $post_id);
    }

    /**
     * Get field value from post meta
     */
    protected function getFieldValue($field_id, $post = null, $default = '')
    {
        if (!$post || !isset($post->ID)) {
            return $default;
        }
        
        $value = get_post_meta($post->ID, $field_id, true);
        
        return $value === '' ? $default : $value;
    }

    /**
     * Save field value to post meta
     */
    protected function saveFieldValue($field_id, $value, $post_id = null)
    {
        if (!$post_id) {
            return;
        }
        
        update_post_meta($post_id, $field_id, $value);
    }

    /**
     * Delete field value from post meta
     */
    protected function deleteFieldValue($field_id, $post_id = null)
    {
        if (!$post_id) {
            return;
        }
        
        delete_post_meta($post_id, $field_id);
    }
}