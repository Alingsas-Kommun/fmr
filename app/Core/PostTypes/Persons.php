<?php

namespace App\Core\PostTypes;

use App\Models\Post;

use function App\Core\{arraySpliceAssoc};
use App\Http\Controllers\Admin\PersonController;
use Illuminate\Support\Facades\Blade;

class Persons
{
    public static $base = 'person';

    public static $singular;

    public static $plural;

    public static $icon = 'dashicons-businessperson';

    public static $supports = [
        'title',
        'thumbnail',
        'author',
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
        
        // Thumbnail visibility toggle
        add_filter('admin_post_thumbnail_html', [__CLASS__, 'addThumbnailVisibilityToggle'], 10, 2);
        add_action('save_post', [__CLASS__, 'saveThumbnailVisibility']);
        
        // Disable months dropdown filter
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
        
        if (isset($columns['title'])) {
            $columns['title'] = __('Name', 'fmr');
        }

        $columns_to_add = [];
        $columns_to_add[] = [
            'slug' => 'person-image',
            'title' => '',
            'priority' => 1,
        ];
        $columns_to_add[] = [
            'slug' => 'person-party',
            'title' => __('Party', 'fmr'),
            'priority' => 3,
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

                if ($attachment_id) {
                    $image_html = wp_get_attachment_image($attachment_id, 'thumbnail');
                    echo Blade::render(
                        '<a href="{!! $link !!}">{!! $image_html !!}</a>',
                        [
                            'link' => $link,
                            'image_html' => $image_html
                        ]
                    );
                } else {
                    echo Blade::render(
                        '<a href="{!! $link !!}">
                            <div class="person-image-fallback">
                                <span class="dashicons dashicons-businessperson"></span>
                            </div>
                        </a>',
                        ['link' => $link]
                    );
                }

                break;
            case 'person-party':
                $partyId = get_meta_field($post_id, 'person_party');
                $partyTitle = get_the_title($partyId);
                $partyLink = get_edit_post_link($partyId);

                if ($partyTitle) {
                    echo Blade::render(
                        '<a href="{!! $link !!}">{!! $title !!}</a>',
                        [
                            'link' => $partyLink,
                            'title' => $partyTitle
                        ]
                    );
                } else {
                    echo '-';
                }

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
        echo 'td.person-image, th#person-image {width: 50px;}';
        echo 'td.person-image img { max-width: 50px !important; width: 50px !important; height: auto !important; } td.person-image img { border-radius: 50rem !important; aspect-ratio: 1/1 !important; object-fit: cover !important; }';
        echo '.person-image-fallback { width: 50px; height: 50px; border-radius: 50rem; background-color: #e5e5e5; display: flex; align-items: center; justify-content: center; color: white; } .person-image-fallback .dashicons { font-size: 24px; width: 24px; height: 24px; color: var(--wp-admin-color-primary, #0073aa); }';
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
                
                $options = ['' => __('All parties', 'fmr')];
                foreach ($parties as $party) {
                    $options[$party->ID] = $party->post_title;
                }
                
                echo Blade::render(
                    '<x-admin.select-field :full-width="false" id="person_party" name="person_party" :value="$selected" :optional="true" :options="$options" />',
                    [
                        'selected' => $selected,
                        'options' => $options
                    ]
                );
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

    /**
     * Add thumbnail visibility toggle to the featured image meta box
     *
     * @param string $content
     * @param int $post_id
     * @return string
     */
    public static function addThumbnailVisibilityToggle($content, $post_id)
    {
        if (get_post_type($post_id) !== self::$base) {
            return $content;
        }

        $visibility_value = get_post_meta($post_id, '_thumbnail_id_visibility', true);
        $is_visible = $visibility_value === '' ? true : (bool) $visibility_value;

        $visibility_toggle = view('admin.partials.thumbnail-visibility-toggle', [
            'is_visible' => $is_visible,
            'post_id' => $post_id,
            'size' => 'lg'
        ])->render();
        
        $content .= $visibility_toggle;

        return $content;
    }

    /**
     * Save thumbnail visibility meta
     *
     * @param int $post_id
     * @return void
     */
    public static function saveThumbnailVisibility($post_id)
    {
        if (get_post_type($post_id) !== self::$base) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $visibility = isset($_POST['_thumbnail_id_visibility']) && $_POST['_thumbnail_id_visibility'] == '1' ? 1 : 0;
        update_post_meta($post_id, '_thumbnail_id_visibility', $visibility);
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