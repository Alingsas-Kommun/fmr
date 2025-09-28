<?php

namespace App\Core\Admin\Traits;

use App\Models\Post;
use App\Models\Term;
use function Roots\view;

/**
 * Trait containing shared field group logic
 */
trait FieldGroupTrait
{
    protected static $id;
    protected static $context = 'normal';
    protected static $priority = 'low';
    
    protected $title;

    abstract protected function getTitle();
    
    /**
     * Render the field group
     */
    public function render($object = null)
    {
        wp_nonce_field(static::$id . '_nonce_action', static::$id . '_nonce');

        $data = [
            'id' => static::$id,
            'tabs' => $this->prepareTabs($object),
            'groups' => $this->prepareGroups($object),
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

    protected function prepareTabs($post = null)
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

    protected function prepareGroups($post = null, $tab_id = null)
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
                            $field['value'] = $this->getFieldValue($field['id'], $post);
                            
                            if ($field['type'] === 'post_relation' && isset($field['post_type'])) {
                                $field['options'] = $this->getPostRelationOptions($field['post_type'], $field['display_field'] ?? 'post_title', $field['label'] ?? '');
                            }

                            if ($field['type'] === 'taxonomy_relation' && isset($field['taxonomy'])) {
                                $field['options'] = $this->getTaxonomyRelationOptions($field['taxonomy'], $field['label'] ?? '');
                            }
                            
                            if (isset($field['visibility'])) {
                                $visibility_id = $field['id'] . '_visibility';
                                $field['visibility']['id'] = $visibility_id;
                                $field['visibility']['value'] = $this->getFieldValue($visibility_id, $post, $field['visibility']['default'] ?? true);
                            }
                        }
                    } elseif (isset($row['id'])) {
                        // Simple configuration - this row is actually a field
                        $row['_type'] = 'field';
                        $row['value'] = $this->getFieldValue($row['id'], $post);
                        
                        if ($row['type'] === 'post_relation' && isset($row['post_type'])) {
                            $row['options'] = $this->getPostRelationOptions($row['post_type'], $row['display_field'] ?? 'post_title', $row['label'] ?? '');
                        }

                        if ($row['type'] === 'taxonomy_relation' && isset($row['taxonomy'])) {
                            $row['options'] = $this->getTaxonomyRelationOptions($row['taxonomy'], $row['label'] ?? '');
                        }
                        
                        if (isset($row['visibility'])) {
                            $visibility_id = $row['id'] . '_visibility';
                            $row['visibility']['id'] = $visibility_id;
                            $row['visibility']['value'] = $this->getFieldValue($visibility_id, $post, $row['visibility']['default'] ?? true);
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

    /**
     * Get field value - to be implemented by the class using the trait
     */
    abstract protected function getFieldValue($field_id, $post = null, $default = '');

    /**
     * Save field value - to be implemented by the class using the trait
     */
    abstract protected function saveFieldValue($field_id, $value, $post_id = null);

    /**
     * Delete field value - to be implemented by the class using the trait
     */
    abstract protected function deleteFieldValue($field_id, $post_id = null);

    /**
     * Get post relation options for a specific post type
     *
     * @param string $post_type
     * @param string $display_field
     * @return array
     */
    protected function getPostRelationOptions($post_type, $display_field = 'post_title', $label = '')
    {
        $posts = Post::type($post_type)
            ->published()
            ->orderBy('post_title')
            ->get();

        $options = ['' => sprintf(__('Select %s', 'fmr'), strtolower($label))];

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

    /**
     * Get taxonomy relation options for a specific taxonomy
     *
     * @param string $taxonomy
     * @return array
     */
    protected function getTaxonomyRelationOptions($taxonomy, $label = '')
    {
        $terms = Term::whereHas('termTaxonomy', function($q) use ($taxonomy) {
            $q->where('taxonomy', $taxonomy);
        })->orderBy('name')->get();

        $options = ['' => sprintf(__('Select %s', 'fmr'), strtolower($label))];
        
        foreach ($terms as $term) {
            $options[$term->term_id] = $term->name;
        }

        return $options;
    }

    /**
     * Process field values for saving
     */
    protected function processFieldValue($field, $value)
    {
        switch ($field['type']) {
            case 'textarea':
                return sanitize_textarea_field($value);
            case 'checkbox':
                return (bool) $value;
            case 'image':
                return absint($value); // Ensure it's a positive integer (attachment ID)
            case 'color':
                return sanitize_hex_color($value);
            case 'key_generation':
                return sanitize_text_field($value);
            case 'taxonomy_relation':
                return absint($value); // Ensure it's a positive integer (term ID)
            case 'post_relation':
                return absint($value); // Ensure it's a positive integer (post ID)
            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * Handle field saving logic
     */
    protected function handleFieldSave($fields, $post_id = null)
    {
        foreach ($fields as $field) {
            $has_value = false;

            if (isset($_POST[$field['id']])) {
                $value = $_POST[$field['id']];
                $value = $this->processFieldValue($field, $value);

                if ($value === '' || $value === null) {
                    $this->deleteFieldValue($field['id'], $post_id);
                } else {
                    $this->saveFieldValue($field['id'], $value, $post_id);
                    $has_value = true;
                }
            } else if ($field['type'] === 'checkbox') {
                $this->saveFieldValue($field['id'], 0, $post_id);
            }

            $visibility_id = $field['id'] . '_visibility';
            
            if (isset($field['visibility'])) {
                if ($has_value) {
                    $visibility = isset($_POST[$visibility_id]) && $_POST[$visibility_id] == '1' ? 1 : 0;
                    $this->saveFieldValue($visibility_id, $visibility, $post_id);
                } else {
                    $this->deleteFieldValue($visibility_id, $post_id);
                }
            } else {
                $this->deleteFieldValue($visibility_id, $post_id);
            }
        }
    }
}
