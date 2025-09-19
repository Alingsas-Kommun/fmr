<?php

namespace App\Core\Admin\Abstracts;

use App\Models\Post;
use function Roots\view;

/**
 * Abstract FieldGroup class for creating custom meta boxes
 * 
 * Supports both single and multiple post types by setting the $post_types property:
 */
abstract class FieldGroup
{
    protected static $post_types = [];
    protected static $id;
    protected static $context = 'normal';
    protected static $priority = 'low';
    
    protected $title;

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

    public function render($post)
    {
        wp_nonce_field(static::$id . '_nonce_action', static::$id . '_nonce');

        $data = [
            'id' => static::$id,
            'tabs' => $this->prepareTabs($post),
            'groups' => $this->prepareGroups($post),
        ];

        echo view('admin.field-groups.fields', $data)->render();
    }

    /**
     * Extract tabs from field configuration automatically
     */
    protected function extractTabs()
    {
        $tabs = [];
        $fields = $this->getFields();
        
        foreach ($fields as $group) {
            if (isset($group['tab']) && !empty($group['tab'])) {
                $tab_id = $group['tab'];
                if (!isset($tabs[$tab_id])) {
                    // Check if group has a custom tab label
                    $label = $group['tab_label'] ?? ucfirst(str_replace('_', ' ', $tab_id));
                    $tabs[$tab_id] = [
                        'label' => $label,
                    ];
                }
            }
        }
        
        return $tabs;
    }

    protected function getFields()
    {
        return [];
    }


    /**
     * Flatten the field configuration into a simple array for easy processing
     */
    protected function getFlattenedFields()
    {
        $flattened = [];
        $fields = $this->getFields();
        
        foreach ($fields as $group) {
            if (isset($group['fields']) && is_array($group['fields'])) {
                foreach ($group['fields'] as $row) {
                    if (isset($row['fields']) && is_array($row['fields'])) {
                        // Complex configuration with rows
                        foreach ($row['fields'] as $field) {
                            $flattened[$field['id']] = $field;
                        }
                    } elseif (isset($row['id'])) {
                        // Simple configuration with direct fields
                        $flattened[$row['id']] = $row;
                    }
                }
            }
        }
        
        return $flattened;
    }

    protected function prepareTabs($post)
    {
        $tabs = $this->extractTabs();
        if (empty($tabs)) {
            return [];
        }

        $prepared_tabs = [];
        foreach ($tabs as $tab_id => $tab) {
            $groups = $this->prepareGroups($post, $tab_id);
            
            // Only include tabs that have groups assigned to them
            if (!empty($groups)) {
                $prepared_tabs[$tab_id] = [
                    'label' => $tab['label'],
                    'groups' => $groups,
                ];
            }
        }

        return $prepared_tabs;
    }

    protected function prepareGroups($post, $tab_id = null)
    {
        $groups = [];
        foreach ($this->getFields() as $group) {
            if ($tab_id !== null && (!isset($group['tab']) || $group['tab'] !== $tab_id)) {
                continue;
            }

            if ($tab_id === null && isset($group['tab'])) {
                continue;
            }

            // Add type marker to group
            $group['_type'] = 'group';
            
            $rows = [];
            if (isset($group['fields']) && is_array($group['fields'])) {
                foreach ($group['fields'] as $row) {
                    if (isset($row['fields']) && is_array($row['fields'])) {
                        // Complex configuration with rows
                        $row['_type'] = 'row';
                        foreach ($row['fields'] as &$field) {
                            $field['_type'] = 'field';
                            $field['value'] = get_post_meta($post->ID, $field['id'], true);
                            
                            if ($field['type'] === 'post_relation' && isset($field['post_type'])) {
                                $field['options'] = $this->getPostRelationOptions($field['post_type'], $field['display_field'] ?? 'post_title');
                            }
                            
                            if (isset($field['visibility'])) {
                                $visibility_id = $field['id'] . '_visibility';
                                $field['visibility']['id'] = $visibility_id;
                                $field['visibility']['value'] = get_post_meta($post->ID, $visibility_id, true);
                                
                                if ($field['visibility']['value'] === '') {
                                    $field['visibility']['value'] = $field['visibility']['default'] ?? true;
                                }
                            }
                        }
                    } elseif (isset($row['id'])) {
                        // Simple configuration - this row is actually a field
                        $row['_type'] = 'field';
                        $row['value'] = get_post_meta($post->ID, $row['id'], true);
                        
                        if ($row['type'] === 'post_relation' && isset($row['post_type'])) {
                            $row['options'] = $this->getPostRelationOptions($row['post_type'], $row['display_field'] ?? 'post_title');
                        }
                        
                        if (isset($row['visibility'])) {
                            $visibility_id = $row['id'] . '_visibility';
                            $row['visibility']['id'] = $visibility_id;
                            $row['visibility']['value'] = get_post_meta($post->ID, $visibility_id, true);
                            
                            if ($row['visibility']['value'] === '') {
                                $row['visibility']['value'] = $row['visibility']['default'] ?? true;
                            }
                        }
                    }

                    $rows[] = $row;
                }
            }

            $group['rows'] = $rows;
            $groups[] = $group;
        }

        return $groups;
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
        
        foreach ($fields as $field) {
            $has_value = false;

            if (isset($_POST[$field['id']])) {
                $value = $_POST[$field['id']];
                
                switch ($field['type']) {
                    case 'textarea':
                        $value = sanitize_textarea_field($value);
                        break;
                    case 'checkbox':
                        $value = (bool) $value;
                        break;
                    default:
                        $value = sanitize_text_field($value);
                }

                if ($value === '' || $value === null) {
                    delete_post_meta($post_id, $field['id']);
                } else {
                    update_post_meta($post_id, $field['id'], $value);
                    $has_value = true;
                }
            } else if ($field['type'] === 'checkbox') {
                delete_post_meta($post_id, $field['id']);
            }

            $visibility_id = $field['id'] . '_visibility';
            
            if (isset($field['visibility'])) {
                if ($has_value) {
                    $visibility = isset($_POST[$visibility_id]) && $_POST[$visibility_id] == '1' ? 1 : 0;
                    update_post_meta($post_id, $visibility_id, $visibility);
                } else {
                    delete_post_meta($post_id, $visibility_id);
                }
            } else {
                delete_post_meta($post_id, $visibility_id);
            }
        }
    }

    /**
     * Get post relation options for a specific post type
     *
     * @param string $post_type
     * @param string $display_field
     * @return array
     */
    protected function getPostRelationOptions($post_type, $display_field = 'post_title')
    {
        $posts = Post::type($post_type)
            ->published()
            ->orderBy('post_title')
            ->get();

        $options = [];
        foreach ($posts as $post) {
            $display_value = $post->$display_field;
            
            // If display_field is a meta field, get it
            if ($display_field !== 'post_title' && $display_field !== 'post_content') {
                $display_value = $post->getMeta($display_field);
            }
            
            // Fallback to post title if display value is empty
            if (empty($display_value)) {
                $display_value = $post->post_title;
            }
            
            $options[$post->ID] = $display_value;
        }

        return $options;
    }
}