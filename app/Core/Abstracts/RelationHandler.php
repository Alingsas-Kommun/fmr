<?php

namespace App\Core\Abstracts;

use function Roots\view;

abstract class RelationHandler
{
    protected static $post_type;
    protected static $meta_box_id;
    protected static $meta_box_title;
    protected static $context = 'normal';
    protected static $priority = 'high';
    
    // Configuration for the relation
    protected static $config;
    
    public function __construct()
    {
        if (empty(static::$config)) {
            throw new \Exception('Relation configuration must be defined in static::$config');
        }
        
        add_action('add_meta_boxes', [$this, 'register']);
        add_action('save_post_' . static::$post_type, [$this, 'save'], 10, 2);
    }

    public function register()
    {
        add_meta_box(
            static::$meta_box_id,
            __(static::$meta_box_title, 'fmr'),
            [$this, 'render'],
            static::$post_type,
            static::$context,
            static::$priority
        );
    }

    public function render($post)
    {
        wp_nonce_field(static::$meta_box_id . '_nonce_action', static::$meta_box_id . '_nonce');
        
        // Load existing data from database
        $existing_data = $this->loadExistingData($post->ID);
        
        // Translate entity names in config
        $translated_config = static::$config;
        if (isset($translated_config['entity'])) {
            $translated_config['entity'] = __($translated_config['entity'], 'fmr');
        }
        if (isset($translated_config['entity_plural'])) {
            $translated_config['entity_plural'] = __($translated_config['entity_plural'], 'fmr');
        }
        
        // Translate field labels
        if (isset($translated_config['fields'])) {
            foreach ($translated_config['fields'] as &$field) {
                if (isset($field['label'])) {
                    $field['label'] = __($field['label'], 'fmr');
                }
            }
        }
        
        $data = [
            'meta_box_id' => static::$meta_box_id,
            'config' => $translated_config,
            'existing_data' => $existing_data,
            'post_id' => $post->ID,
        ];

        echo view('admin.relation-handler.meta-box', $data)->render();
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

        $relation_data = isset($_POST[static::$config['storage_key']]) 
            ? $_POST[static::$config['storage_key']] 
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
