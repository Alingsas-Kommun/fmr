<?php

namespace App\Core\PostTypes;

use function App\Core\{arraySpliceAssoc};

class Parties
{
    public static $base = 'party';

    public static $singular;

    public static $plural;

    public static $icon = 'dashicons-groups';

    public static $supports = [
        'title',
        'thumbnail',
    ];

    public static $archive_page = false;

    public static $single_page = true;

    public static $labels = [];

    public function __construct()
    {
        self::$singular = __('Party', 'fmr');
        self::$plural = __('Parties', 'fmr');
        self::$labels = self::getLabels();

        self::register();

        add_filter('manage_' . self::$base . '_posts_columns', [__CLASS__, 'addColumns']);
        add_action('manage_' . self::$base . '_posts_custom_column', [__CLASS__, 'addColumnData'], 10, 2);
        add_action('admin_head', [__CLASS__, 'partyImageColumnWidth']);
    }

    /**
     * Set labels
     *
     * @return array
     */
    public function getLabels()
    {
        return [
            'name'               => \_x('Parties', 'Post type general name', 'fmr'),
            'singular_name'      => \_x('Party', 'Post type singular name', 'fmr'),
            'menu_name'          => \_x('Parties', 'Admin Menu text', 'fmr'),
            'name_admin_bar'     => \_x('Party', 'Add New on Toolbar', 'fmr'),
            'add_new'            => __('Add new', 'fmr'),
            'add_new_item'       => __('Add new party', 'fmr'),
            'new_item'           => __('New party', 'fmr'),
            'edit_item'          => __('Edit party', 'fmr'),
            'view_item'          => __('View party', 'fmr'),
            'all_items'          => __('All parties', 'fmr'),
            'search_items'       => __('Search parties', 'fmr'),
            'parent_item_colon'  => __('Parent parties:', 'fmr'),
            'not_found'          => __('No parties found.', 'fmr'),
            'not_found_in_trash' => __('No parties found in Trash.', 'fmr'),
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
            'rewrite'            => ['slug' => __('parties', 'fmr')],
            'capability_type'    => 'post',
            'has_archive'        => self::$archive_page,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'supports'           => self::$supports,
            'menu_icon'          => self::$icon,
        ];

        register_post_type(self::$base, $args);
    }

    public static function addColumns($columns)
    {
        unset($columns['date']);
        unset($columns['author']);

        $columns_to_add = [];
        $columns_to_add[] = [
            'slug' => 'party-image',
            'title' => '',
            'priority' => 1,
        ];
        $columns_to_add[] = [
            'slug' => 'party-shortening',
            'title' => __('Shortening', 'fmr'),
            'priority' => 3,
        ];
        $columns_to_add[] = [
            'slug' => 'party-group-leader',
            'title' => __('Group Leader, City Council', 'fmr'),
            'priority' => 4,
        ];

        foreach ($columns_to_add as $col) {
            arraySpliceAssoc($columns, $col['priority'], 0, [$col['slug'] => $col['title']]);
        }

        return $columns;
    }

    public static function addColumnData($column, $post_id)
    {
        switch ($column) {
            case 'party-image':
                $attachment_id = get_post_thumbnail_id($post_id);
                $link = get_edit_post_link($post_id);

                $image  = "<a href='{$link}'>";
                $image .= wp_get_attachment_image($attachment_id, 'thumbnail');
                $image .= "</a>";

                echo $image;

                break;
            case 'party-shortening':
                echo get_meta_field($post_id, 'party_shortening');

                break;
            case 'party-group-leader':
                echo get_meta_field($post_id, 'party_group_leader');

                break;
        }
    }

    public static function partyImageColumnWidth()
    {
        echo '<style type="text/css">';
        echo 'td.party-image, td.party-image img, th#party-image { max-width: 50px !important; width: 50px !important; height: auto !important; }';
        echo '</style>';
    }
}
