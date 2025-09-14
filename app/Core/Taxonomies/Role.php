<?php

namespace App\Core\Taxonomies;

class Role
{
    public static $base = 'role';

    public static $postTypes = [];

    public function __construct()
    {
        $this->register();

        add_action('admin_head', [$this, 'removeFields']);
        add_filter("manage_edit-" . self::$base . "_columns", [$this, 'removeColumns']);
    }

    public function register()
    {
        $args = [
            'hierarchical' => false,
            'label' => __('Roles', 'fmr'),
            'labels' => [
                'name'              => __('Roles', 'fmr'),
                'singular_name'     => __('Role', 'fmr'),
                'menu_name'         => _x('Roles', 'Admin menu name', 'fmr'),
                'search_items'      => __('Search roles', 'fmr'),
                'all_items'         => __('All roles', 'fmr'),
                'parent_item'       => __('Parent role', 'fmr'),
                'parent_item_colon' => __('Parent role:', 'fmr'),
                'edit_item'         => __('Edit role', 'fmr'),
                'update_item'       => __('Update role', 'fmr'),
                'add_new_item'      => __('Add new role', 'fmr'),
                'new_item_name'     => __('New role name', 'fmr'),
                'not_found'         => __('No roles were found', 'fmr'),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'rewrite' => false,
            'query_var' => true,
        ];

        register_taxonomy(
            self::$base,
            self::$postTypes,
            $args
        );
    }

    /**
     * Remove slug and description fields in taxonomy admin form
     */
    public function removeFields()
    {
        $screen = get_current_screen();

        if ($screen && $screen->taxonomy === self::$base) {
            echo '<style>
                .form-field.term-slug-wrap,
                .term-description-wrap { display:none !important; }
            </style>';
        }
    }

    /**
     * Remove Description, Slug, and Count columns from the terms list table
     */
    public function removeColumns($columns)
    {
        unset($columns['description']);
        unset($columns['slug']);
        unset($columns['posts']); // "Count" column
        return $columns;
    }
}