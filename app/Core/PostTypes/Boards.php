<?php

namespace App\Core\PostTypes;

use function App\Core\{arraySpliceAssoc};
use Illuminate\Support\Facades\Blade;

class Boards
{
    /**
     * The base of the post type
     *
     * @var string
     */
    public static $base = 'board';

    /**
     * The singular of the post type
     *
     * @var string
     */
    public static $singular;

    /**
     * The plural of the post type
     *
     * @var string
     */
    public static $plural;

    /**
     * The icon of the post type
     *
     * @var string
     */
    public static $icon = 'dashicons-building';

    /**
     * The supports of the post type
     *
     * @var array
     */
    public static $supports = [
        'title',
        'author',
    ];

    /**
     * The archive page of the post type
     *
     * @var bool
     */
    public static $archive_page = false;

    /**
     * The single page of the post type
     *
     * @var bool
     */
    public static $single_page = true;

    /**
     * The labels of the post type
     *
     * @var array
     */
    public static $labels = [];

    /**
     * Constructor. Set up the post type properties.
     *
     * @return void
     */
    public function __construct()
    {
        self::$singular = __('Board', 'fmr');
        self::$plural = __('Boards', 'fmr');
        self::$labels = self::getLabels();

        self::register();

        add_filter('manage_' . self::$base . '_posts_columns', [__CLASS__, 'addColumns']);
        add_action('manage_' . self::$base . '_posts_custom_column', [__CLASS__, 'addColumnData'], 10, 2);
        add_filter('disable_months_dropdown', [__CLASS__, 'disableMonthsDropdown'], 10, 2);
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

    /**
     * Add columns to the post type
     *
     * @param array $columns
     * @return array
     */
    public static function addColumns($columns)
    {
        unset($columns['date']);
        unset($columns['author']);

        if (isset($columns['title'])) {
            $columns['title'] = __('Name', 'fmr');
        }

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

    /**
     * Add column data to the post type
     *
     * @param string $column
     * @param int $post_id
     * @return void
     */
    public static function addColumnData($column, $post_id)
    {
        switch ($column) {
            case 'board-category':
                $term_id = get_meta_field($post_id, 'board_category');
                $term = $term_id ? get_term($term_id, 'type') : null;
                
                if ($term && !is_wp_error($term) && $term->name) {
                    $termLink = get_edit_term_link($term_id, 'type');
                    
                    if ($termLink) {
                        echo Blade::render(
                            '<a href="{!! $link !!}">{!! $title !!}</a>',
                            [
                                'link' => $termLink,
                                'title' => $term->name
                            ]
                        );
                    } else {
                        echo esc_html($term->name);
                    }
                } else {
                    echo '-';
                }

                break;
            case 'board-shortening':
                echo get_meta_field($post_id, 'board_shortening');

                break;
        }
    }

    /**
     * Disable months dropdown filter for this post type
     *
     * @param bool $disable
     * @param string $post_type
     * @return bool
     */
    public static function disableMonthsDropdown($disable, $post_type)
    {
        if ($post_type === self::$base) {
            return true;
        }
        
        return $disable;
    }
}