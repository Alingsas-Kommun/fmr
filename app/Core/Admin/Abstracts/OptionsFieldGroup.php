<?php

namespace App\Core\Admin\Abstracts;

use App\Core\Admin\Traits\FieldGroupTrait;

use function App\Core\setting;
use function App\Core\setSetting;
use function App\Core\deleteSetting;

/**
 * Abstract OptionsFieldGroup class for creating custom option panels
 * Stores field data in the WordPress options table
 */
abstract class OptionsFieldGroup
{
    use FieldGroupTrait;


    public function __construct()
    {
        $this->title = $this->getTitle();
        
        add_action('add_meta_boxes', [$this, 'register']);
        add_action('admin_init', [$this, 'handleSave']);
    }

    public function register()
    {
        // This will be called by the EditPage when setting up meta boxes
        // The actual registration happens in the EditPage's setupMetaBoxes method
    }

    /**
     * Register this field group with an EditPage
     */
    public function registerWithOptionsPage($editPage)
    {
        $editPage->addMetaBox(
            static::$id,
            $this->title,
            [$this, 'render'],
            static::$context,
            static::$priority
        );
    }


    public function handleSave()
    {
        // Only process if this is a POST request and our nonce is present
        if (!isset($_POST[static::$id . '_nonce']) || 
            !wp_verify_nonce($_POST[static::$id . '_nonce'], static::$id . '_nonce_action')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        // Use flattened fields for efficient processing
        $fields = $this->getFlattenedFields();
        $this->handleFieldSave($fields);
    }

    /**
     * Get field value from options using settings functions
     */
    protected function getFieldValue($field_id, $post = null, $default = '')
    {
        return setting($field_id, $default);
    }

    /**
     * Save field value to options using settings functions
     */
    protected function saveFieldValue($field_id, $value, $post_id = null)
    {
        return setSetting($field_id, $value);
    }

    /**
     * Delete field value from options using settings functions
     */
    protected function deleteFieldValue($field_id, $post_id = null)
    {
        return deleteSetting($field_id);
    }
}
