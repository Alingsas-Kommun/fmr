<?php

namespace App\Core\PostTypes;

use function App\Core\{arraySpliceAssoc};
use App\Models\Post;
use Illuminate\Support\Facades\Blade;

use function Roots\view;

class Parties
{
    /**
     * The base of the post type
     *
     * @var string
     */
    public static $base = 'party';

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
    public static $icon = 'dashicons-groups';

    /**
     * The supports of the post type
     *
     * @var array
     */
    public static $supports = [
        'title',
        'thumbnail',
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
        self::$singular = __('Party', 'fmr');
        self::$plural = __('Parties', 'fmr');
        self::$labels = self::getLabels();

        self::register();

        // Manage columns
        add_filter('manage_' . self::$base . '_posts_columns', [__CLASS__, 'addColumns']);
        add_action('manage_' . self::$base . '_posts_custom_column', [__CLASS__, 'addColumnData'], 10, 2);
        add_action('admin_head', [__CLASS__, 'partyImageColumnWidth']);
        
        // Add members meta box
        add_action('add_meta_boxes', [__CLASS__, 'addMembersMetaBox']);
        
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
            'name'               => _x('Parties', 'Post type general name', 'fmr'),
            'singular_name'      => _x('Party', 'Post type singular name', 'fmr'),
            'menu_name'          => _x('Parties', 'Admin Menu text', 'fmr'),
            'name_admin_bar'     => _x('Party', 'Add New on Toolbar', 'fmr'),
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
            case 'party-image':
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
                            <div class="party-image-fallback">
                                <span class="dashicons dashicons-groups"></span>
                            </div>
                        </a>',
                        ['link' => $link]
                    );
                }

                break;
            case 'party-shortening':
                echo get_meta_field($post_id, 'party_shortening');

                break;
            case 'party-group-leader':
                $party = Post::find($post_id);
                $group_leader = $party ? $party->getGroupLeader() : null;

                if ($group_leader) {
                    $leaderName = $group_leader->post_title;
                    $leaderLink = get_edit_post_link($group_leader->ID);

                    echo Blade::render(
                        '<a href="{!! $link !!}">{!! $name !!}</a>',
                        [
                            'link' => $leaderLink,
                            'name' => $leaderName
                        ]
                    );
                } else {
                    echo '-';
                }

                break;
        }
    }

    /**
     * Set the width of the party image column
     *
     * @return void
     */
    public static function partyImageColumnWidth()
    {
        echo '<style type="text/css">';
        echo 'td.party-image, td.party-image img, th#party-image { max-width: 50px !important; width: 50px !important; height: auto !important; }';
        echo '.party-image-fallback { width: 50px; height: 50px; border-radius: 50rem; background-color: #e5e5e5; display: flex; align-items: center; justify-content: center; color: white; } .party-image-fallback .dashicons { font-size: 24px; width: 24px; height: 24px; color: var(--wp-admin-color-primary, #0073aa); }';
        echo '</style>';
    }

    /**
     * Add members meta box
     *
     * @return void
     */
    public static function addMembersMetaBox()
    {
        add_meta_box(
            'party_members',
            __('Party Members', 'fmr'),
            [__CLASS__, 'renderMembersMetaBox'],
            self::$base,
            'normal',
            'low'
        );
    }

    /**
     * Render members meta box
     *
     * @param object $post
     * @return void
     */
    public static function renderMembersMetaBox($post)
    {
        $active_members = self::getActiveMembers($post->ID);
        $inactive_members = self::getInactiveMembers($post->ID);
        
        echo view('admin.parties.party-members', [
            'active_members' => $active_members,
            'inactive_members' => $inactive_members
        ])->render();
    }

    /**
     * Get active members
     *
     * @param int $party_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveMembers($party_id)
    {
        return self::getPartyMembers($party_id, true);
    }

    /**
     * Get inactive members
     *
     * @param int $party_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getInactiveMembers($party_id)
    {
        return self::getPartyMembers($party_id, false);
    }

    /**
     * Get party members with optional active assignment filter
     *
     * @param int $party_id
     * @param bool $active
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private static function getPartyMembers($party_id, $active = true)
    {   
        $query = Post::persons()
            ->published()
            ->withMeta('person_party', $party_id)
            ->with(['meta']);

        if ($active) {
            $query->whereHas('personAssignments', function($query) {
                $query->active();
            });
        } else {
            $query->whereDoesntHave('personAssignments', function($query) {
                $query->active();
            });
        }

        $members = $query->orderBy('post_title')->get();

        return $members->format();
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
