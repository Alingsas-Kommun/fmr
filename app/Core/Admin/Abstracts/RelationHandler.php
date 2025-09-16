<?php

namespace App\Core\Admin\Abstracts;

use function Roots\view;

abstract class RelationHandler
{
    // For post type compatibility
    protected static $post_type;
    protected static $meta_box_id;
    protected static $context = 'normal';
    protected static $priority = 'high';
    
    // For custom edit page compatibility
    protected $editPage;
    protected $title;
    protected $mode = 'post_type'; // 'post_type' or 'custom_edit'

    abstract protected function getTitle();
    abstract protected function getConfig();

    public function __construct($editPage = null)
    {
        $this->title = $this->getTitle();
        
        // Determine mode based on constructor parameter
        if ($editPage instanceof EditPage) {
            $this->mode = 'custom_edit';
            $this->editPage = $editPage;
            $this->registerForCustomEdit();
        } else {
            $this->mode = 'post_type';
            $this->registerForPostType();
        }
    }

    /**
     * Register for WordPress post types (original behavior)
     */
    protected function registerForPostType()
    {
        add_action('add_meta_boxes', [$this, 'registerPostTypeMetaBox']);
        add_action('save_post_' . static::$post_type, [$this, 'savePostType'], 10, 2);
    }

    /**
     * Register for custom edit pages (new behavior)
     */
    protected function registerForCustomEdit()
    {
        // Register meta box with the edit page
        $this->editPage->addMetaBox(
            static::$meta_box_id,
            $this->title,
            [$this, 'render'],
            static::$context,
            static::$priority
        );
        
        // Register save hook for custom edit page
        $pageSlug = $this->editPage->getPageSlug();
        add_action("save_{$pageSlug}", [$this, 'handleCustomEditSave'], 10, 3);
    }

    /**
     * Register meta box for post types
     */
    public function registerPostTypeMetaBox()
    {
        add_meta_box(
            static::$meta_box_id,
            $this->title,
            [$this, 'render'],
            static::$post_type,
            static::$context,
            static::$priority
        );
    }

    /**
     * Render the meta box content
     */
    public function render($object, $box)
    {
        wp_nonce_field(static::$meta_box_id . '_nonce_action', static::$meta_box_id . '_nonce');
        
        $objectId = $this->getObjectId($object);
        $existing_data = $this->loadExistingData($objectId);
        
        $data = [
            'meta_box_id' => static::$meta_box_id,
            'config' => $this->getConfig(),
            'existing_data' => $existing_data,
            'object_id' => $objectId,
        ];

        echo view('admin.relation-handler.list', $data)->render();
    }

    /**
     * Get object ID based on mode
     */
    protected function getObjectId($object)
    {
        if ($this->mode === 'custom_edit') {
            // For custom edit pages, object is the current object being edited
            if (is_object($object) && isset($object->id)) {
                return $object->id;
            }
            // Fallback to GET parameter
            return $_GET['id'] ?? null;
        } else {
            // For post types, object is the post
            return $object->ID ?? $object->id ?? null;
        }
    }

    /**
     * Save for post types (original behavior)
     */
    public function savePostType($post_id, $post)
    {
        if (!isset($_POST[static::$meta_box_id . '_nonce']) || 
            !wp_verify_nonce($_POST[static::$meta_box_id . '_nonce'], static::$meta_box_id . '_nonce_action')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $this->processSave($post_id, $_POST);
    }

    /**
     * Handle save for custom edit pages (new behavior)
     */
    public function handleCustomEditSave($object, $data, $id)
    {
        if (!isset($data[static::$meta_box_id . '_nonce']) || 
            !wp_verify_nonce($data[static::$meta_box_id . '_nonce'], static::$meta_box_id . '_nonce_action')) {
            return;
        }

        $this->processSave($id, $data);
    }

    /**
     * Common save processing logic
     */
    protected function processSave($object_id, $data)
    {
        $config = $this->getConfig();
        $relation_data = isset($data[$config['storage_key']]) 
            ? $data[$config['storage_key']] 
            : '';

        if (empty($relation_data)) {
            return;
        }

        // Process the data through the controller
        $this->processRelationData($object_id, $relation_data);
    }

    /**
     * Load existing data - override in child classes
     */
    protected function loadExistingData($object_id)
    {
        return [];
    }

    /**
     * Process relation data - override in child classes
     */
    protected function processRelationData($object_id, $relation_data)
    {
        // Override in child classes
    }

    /**
     * Get the edit page instance (for custom edit mode)
     */
    public function getEditPage()
    {
        return $this->editPage;
    }

    /**
     * Get the current mode
     */
    public function getMode()
    {
        return $this->mode;
    }
}