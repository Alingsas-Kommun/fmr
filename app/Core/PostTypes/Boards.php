<?php

namespace App\Core\PostTypes;

use function App\Core\{arraySpliceAssoc};

class Boards
{
    public static $base = 'board';

    public static $singular;

    public static $plural;

    public static $icon = 'dashicons-building';

    public static $supports = [
        'title',
        'author',
    ];

    public static $archive_page = false;

    public static $single_page = true;

    public static $labels = [];

    public function __construct()
    {
        self::$singular = __('Board', 'fmr');
        self::$plural = __('Boards', 'fmr');
        self::$labels = self::getLabels();

        self::register();

        add_filter('manage_' . self::$base . '_posts_columns', [__CLASS__, 'addColumns']);
        add_action('manage_' . self::$base . '_posts_custom_column', [__CLASS__, 'addColumnData'], 10, 2);
    }

    /**
     * Set labels
     *
     * @return array
     */
    public function getLabels()
    {
        return [
            'name'               => _x('Boards', 'Post type general name', 'fmr'),
            'singular_name'      => _x('Board', 'Post type singular name', 'fmr'),
            'menu_name'          => _x('Boards', 'Admin Menu text', 'fmr'),
            'name_admin_bar'     => _x('Board', 'Add New on Toolbar', 'fmr'),
            'add_new'            => __('Add new', 'fmr'),
            'add_new_item'       => __('Add new board', 'fmr'),
            'new_item'           => __('New board', 'fmr'),
            'edit_item'          => __('Edit board', 'fmr'),
            'view_item'          => __('View board', 'fmr'),
            'all_items'          => __('All board', 'fmr'),
            'search_items'       => __('Search boards', 'fmr'),
            'parent_item_colon'  => __('Parent boards:', 'fmr'),
            'not_found'          => __('No boards found.', 'fmr'),
            'not_found_in_trash' => __('No boards found in Trash.', 'fmr'),
        ];
    }

    /**
     * Register the custom post type
     */
    public function register()
    {
        $args = [
            'labels'             => self::$labels,
            'public'             => true,
            'publicly_queryable' => self::$single_page,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => __('boards', 'fmr')],
            'capability_type'    => 'post',
            'has_archive'        => self::$archive_page,
            'hierarchical'       => false,
            'menu_position'      => 25,
            'supports'           => self::$supports,
            'menu_icon'          => self::$icon,
        ];

        register_post_type(self::$base, $args);
    }

    public static function addColumns($columns)
    {
        unset($columns['date']);

        $columns_to_add = [];
        $columns_to_add[] = [
            'slug' => 'board-category',
            'title' => __('Category', 'fmr'),
            'priority' => 2,
        ];
        $columns_to_add[] = [
            'slug' => 'board-shortening',
            'title' => __('Shortening', 'fmr'),
            'priority' => 2,
        ];

        foreach ($columns_to_add as $col) {
            arraySpliceAssoc($columns, $col['priority'], 0, [$col['slug'] => $col['title']]);
        }

        return $columns;
    }

    public static function addColumnData($column, $post_id)
    {
        switch ($column) {
            case 'board-category':
                echo get_meta_field($post_id, 'board_category');

                break;
            case 'board-shortening':
                echo get_meta_field($post_id, 'board_shortening');

                break;
        }
    }
}