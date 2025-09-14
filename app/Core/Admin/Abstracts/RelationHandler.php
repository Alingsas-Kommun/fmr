<?php

namespace App\Core\Admin\Abstracts;

use function Roots\view;

abstract class RelationHandler
{
    protected static $post_type;
    protected static $meta_box_id;
    protected static $context = 'normal';
    protected static $priority = 'high';
    
    protected $title;

    abstract protected function getTitle();
    abstract protected function getConfig();

    public function __construct()
    {
        $this->title = $this->getTitle();
        
        add_action('add_meta_boxes', [$this, 'register']);
        add_action('save_post_' . static::$post_type, [$this, 'save'], 10, 2);
    }

    public function register()
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

    public function render($post)
    {
        wp_nonce_field(static::$meta_box_id . '_nonce_action', static::$meta_box_id . '_nonce');
        
        $existing_data = $this->loadExistingData($post->ID);
        
        $data = [
            'meta_box_id' => static::$meta_box_id,
            'config' => $this->getConfig(),
            'existing_data' => $existing_data,
            'post_id' => $post->ID,
        ];

        echo view('admin.relation-handler.list', $data)->render();
    }

    protected function loadExistingData($post_id)
    {
        return [];
    }

    public function save($post_id, $post)
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

        $config = $this->getConfig();
        $relation_data = isset($_POST[$config['storage_key']]) 
            ? $_POST[$config['storage_key']] 
            : '';

        if (empty($relation_data)) {
            return;
        }

        // Process the data through the controller
        $this->processRelationData($post_id, $relation_data);
    }

    protected function processRelationData($post_id, $relation_data)
    {
        
    }
}
