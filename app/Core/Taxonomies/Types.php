<?php

namespace App\Core\Taxonomies;

class Types
{
    /**
     * The base of the taxonomy
     *
     * @var string
     */
    public static $base = 'type';

    /**
     * The post types of the taxonomy
     *
     * @var array
     */
    public static $postTypes = ['board'];

    /**
     * Constructor. Set up the taxonomy properties.
     *
     * @return void
     */
    public function __construct()
    {
        $this->register();

        add_action('admin_head', [$this, 'removeFields']);
        add_filter("manage_edit-" . self::$base . "_columns", [$this, 'removeColumns']);
    }

    /**
     * Register the taxonomy
     *
     * @return void
     */
    public function register()
    {
        $args = [
            'hierarchical' => true,
            'label' => __('Types', 'fmr'),
            'labels' => [
                'name'              => __('Types', 'fmr'),
                'singular_name'     => __('Type', 'fmr'),
                'menu_name'         => _x('Types', 'Admin menu name', 'fmr'),
                'search_items'      => __('Search types', 'fmr'),
                'all_items'         => __('All types', 'fmr'),
                'parent_item'       => __('Parent type', 'fmr'),
                'parent_item_colon' => __('Parent type:', 'fmr'),
                'edit_item'         => __('Edit type', 'fmr'),
                'update_item'       => __('Update type', 'fmr'),
                'add_new_item'      => __('Add new type', 'fmr'),
                'new_item_name'     => __('New type name', 'fmr'),
                'not_found'         => __('No types were found', 'fmr'),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
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
     * 
     * @param array $columns
     * @return array
     */
    public function removeColumns($columns)
    {
        unset($columns['description']);
        unset($columns['slug']);
        unset($columns['posts']); // "Count" column
        
        return $columns;
    }
}