<?php

namespace App\Core\PostTypes;

use App\Models\Post;

use function App\Core\{arraySpliceAssoc};
use App\Http\Controllers\Admin\PersonController;

class Persons
{
    public static $base = 'person';

    public static $singular;

    public static $plural;

    public static $icon = 'dashicons-businessperson';

    public static $supports = [
        'title',
        'thumbnail',
    ];

    public static $archive_page = false;

    public static $single_page = true;

    public static $labels = [];

    public function __construct()
    {
        self::$singular = __('Person', 'fmr');
        self::$plural = __('Persons', 'fmr');
        self::$labels = self::getLabels();

        self::register();

        add_filter('manage_' . self::$base . '_posts_columns', [__CLASS__, 'addColumns']);
        add_action('manage_' . self::$base . '_posts_custom_column', [__CLASS__, 'addColumnData'], 10, 2);
        add_action('admin_head', [__CLASS__, 'personImageColumnWidth']);
        
        // Party filter
        add_action('restrict_manage_posts', [__CLASS__, 'addPartyFilter']);
        add_action('pre_get_posts', [__CLASS__, 'filterByParty']);
    }

    /**
     * Set labels
     *
     * @return array
     */
    public function getLabels()
    {
        return [
            'name'               => _x('Persons', 'Post type general name', 'fmr'),
            'singular_name'      => _x('Person', 'Post type singular name', 'fmr'),
            'menu_name'          => _x('Persons', 'Admin Menu text', 'fmr'),
            'name_admin_bar'     => _x('Person', 'Add New on Toolbar', 'fmr'),
            'add_new'            => __('Add new', 'fmr'),
            'add_new_item'       => __('Add new person', 'fmr'),
            'new_item'           => __('New person', 'fmr'),
            'edit_item'          => __('Edit person', 'fmr'),
            'view_item'          => __('View person', 'fmr'),
            'all_items'          => __('All persons', 'fmr'),
            'search_items'       => __('Search persons', 'fmr'),
            'parent_item_colon'  => __('Parent persons:', 'fmr'),
            'not_found'          => __('No persons found.', 'fmr'),
            'not_found_in_trash' => __('No persons found in Trash.', 'fmr'),
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
            'rewrite'            => ['slug' => __('persons', 'fmr')],
            'capability_type'    => 'post',
            'has_archive'        => self::$archive_page,
            'hierarchical'       => false,
            'menu_position'      => 10,
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
            'slug' => 'person-image',
            'title' => __('Thumbnail', 'fmr'),
            'priority' => 1,
        ];
        $columns_to_add[] = [
            'slug' => 'person-party',
            'title' => __('Party', 'fmr'),
            'priority' => 3,
        ];
        $columns_to_add[] = [
            'slug' => 'person-birthday',
            'title' => __('Birthday', 'fmr'),
            'priority' => 4,
        ];
        $columns_to_add[] = [
            'slug' => 'person-group-leader',
            'title' => __('Group Leader', 'fmr'),
            'priority' => 6,
        ];
        $columns_to_add[] = [
            'slug' => 'person-status',
            'title' => __('Status', 'fmr'),
            'priority' => 7,
        ];

        foreach ($columns_to_add as $col) {
            arraySpliceAssoc($columns, $col['priority'], 0, [$col['slug'] => $col['title']]);
        }

        return $columns;
    }

    public static function addColumnData($column, $post_id)
    {
        switch ($column) {
            case 'person-image':
                $attachment_id = get_post_thumbnail_id($post_id);
                $link = get_edit_post_link($post_id);

                $image  = "<a href='{$link}'>";
                $image .= wp_get_attachment_image($attachment_id, 'thumbnail');
                $image .= "</a>";

                echo $image;

                break;
            case 'person-party':
                $partyId = get_meta_field($post_id, 'person_party');
                $partyTitle = get_the_title($partyId);
                $partyLink = get_edit_post_link($partyId);

                echo $partyTitle ? "<a href='{$partyLink}'>{$partyTitle}</a>" : '-';

                break;
            case 'person-birthday':
                $birthday = get_meta_field($post_id, 'person_birth_date');
                echo $birthday ? date('Y-m-d', strtotime($birthday)) : '-';

                break;
            case 'person-group-leader':
                $group_leader = get_meta_field($post_id, 'person_group_leader');
                echo $group_leader ? __('Yes', 'fmr') : __('No', 'fmr');

                break;
            case 'person-status':
                $personController = app(PersonController::class);
                $status = $personController->isActive($post_id);
                echo $status ? __('Active', 'fmr') : __('Inactive', 'fmr');

                break;
        }
    }

    public static function personImageColumnWidth()
    {
        echo '<style type="text/css">';
        echo 'td.person-image, td.person-image img, th#person-image { max-width: 70px !important; width: 70px !important; height: auto !important; }';
        echo '</style>';
    }

    /**
     * Add party filter dropdown to the persons admin list
     */
    public static function addPartyFilter()
    {
        global $typenow;
        
        if ($typenow === self::$base) {
            $parties = Post::parties()
                        ->published()
                        ->orderBy('post_title')
                        ->get();
            
            if (!empty($parties)) {
                $selected = isset($_GET['person_party']) ? $_GET['person_party'] : '';
                
                echo '<select name="person_party" id="person_party">';
                echo '<option value="">' . __('All parties', 'fmr') . '</option>';
                
                foreach ($parties as $party) {
                    $selected_attr = selected($selected, $party->ID, false);
                    echo '<option value="' . esc_attr($party->ID) . '"' . $selected_attr . '>';
                    echo esc_html($party->post_title);
                    echo '</option>';
                }
                
                echo '</select>';
            }
        }
    }

    /**
     * Filter persons by party
     */
    public static function filterByParty($query)
    {
        global $pagenow, $typenow;
        
        if ($pagenow === 'edit.php' && $typenow === self::$base && isset($_GET['person_party']) && !empty($_GET['person_party'])) {
            $party_id = intval($_GET['person_party']);
            
            $query->set('meta_query', [
                [
                    'key' => 'person_party',
                    'value' => $party_id,
                    'compare' => '='
                ]
            ]);
        }
    }
}