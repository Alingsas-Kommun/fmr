<?php

namespace App\Core\Admin\DecisionAuthorities;

use App\Http\Controllers\Admin\DecisionAuthorityController;
use App\Http\Controllers\Admin\BoardController;
use Illuminate\Support\Facades\Blade;
use function Roots\view;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    require_once(ABSPATH . 'wp-admin/includes/screen.php');
}

/**
 * Class DecisionAuthorities
 * @package App\Core\Admin
 */
class Index extends \WP_List_Table
{
    private static $instance = null;
    protected $controller;
    protected $boardController;

    /**
     * Initialize the singleton instance.
     *
     * @return self
     */
    public static function init()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    /**
     * Constructor. Set up the list table properties.
     */
    private function __construct()
    {
        parent::__construct([
            'singular' => 'decision_authority',
            'plural'   => 'decision_authorities',
            'ajax'     => false
        ]);

        $this->controller = app(DecisionAuthorityController::class);
        $this->boardController = app(BoardController::class);
    }

    /**
     * Register the decision authorities menu page in WordPress admin.
     *
     * @return void
     */
    public static function register()
    {
        self::init();
        
        $hook = add_menu_page(
            __('Decision Authorities', 'fmr'),
            __('Decision Authorities', 'fmr'),
            'manage_options',
            'decision_authorities',
            [self::$instance, 'render_page'],
            'dashicons-list-view',
            30
        );

        add_submenu_page(
            'decision_authorities',
            __('Types', 'fmr'),
            __('Types', 'fmr'),
            'manage_options',
            'edit-tags.php?taxonomy=type',
            null
        );

        // Hook into admin actions
        add_action('admin_action_delete_decision_authority', [self::$instance, 'handle_delete_decision_authority']);

        // Add screen options for per page
        add_action("load-$hook", function() {
            add_screen_option('per_page', array(
                'label'   => __('Decision Authorities per page', 'fmr'),
                'default' => 20,
                'option'  => 'edit_decision_authority_per_page'
            ));
        });

        // Handle screen options saving ourselves
        add_action('admin_init', function() {
            if (isset($_POST['wp_screen_options']) && isset($_POST['wp_screen_options']['option']) && $_POST['wp_screen_options']['option'] === 'edit_decision_authority_per_page') {
                $per_page = (int) $_POST['wp_screen_options']['value'];
                if ($per_page > 0) {
                    update_user_meta(get_current_user_id(), 'edit_decision_authority_per_page', $per_page);
                }
            }
        });
    }

    /**
     * Render the decision authorities list table page.
     *
     * @return void
     */
    public function render_page()
    {
        $this->process_bulk_action();

        set_current_screen('decision_authorities');

        echo view('admin.decision-authorities.index', [
            'list' => $this,
            'filter_data' => [
                'boards' => $this->boardController->getAll(),
            ]
        ])->render();
    }

    /**
     * Handle single decision authority deletion via admin action.
     *
     * @return void
     */
    public function handle_delete_decision_authority()
    {
        if (!isset($_REQUEST['decision_authority_id']) || !isset($_REQUEST['_wpnonce'])) {
            wp_die(__('Invalid request.', 'fmr'));
        }

        $authority_id = intval($_REQUEST['decision_authority_id']);
        $nonce = $_REQUEST['_wpnonce'];

        if (!wp_verify_nonce($nonce, 'delete_decision_authority_' . $authority_id)) {
            wp_die(__('Security check failed. Please try again.', 'fmr'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'fmr'));
        }

        $this->controller->destroy($authority_id);

        wp_redirect(wp_get_referer());

        exit;
    }

    /**
     * Process bulk actions (e.g., delete multiple decision authorities).
     *
     * @return void
     */
    public function process_bulk_action()
    {
        $action = $this->current_action();

        if ($action !== 'delete') {
            return;
        }

        check_admin_referer('bulk-' . $this->_args['plural']);

        $authorities = $_REQUEST['decision_authorities'] ?? [];
        if (empty($authorities)) {
            return;
        }

        $deleted = 0;

        foreach ($authorities as $id) {
            if ($this->controller->destroy($id)) {
                $deleted++;
            }
        }

        wp_redirect(wp_get_referer());
    }

    /**
     * Get an array of views available on this table (all, ongoing, past).
     *
     * @return array Array of views with their labels and counts.
     */
    public function get_views()
    {
        $views = [];
        $current = isset($_REQUEST['period_status']) ? $_REQUEST['period_status'] : 'all';

        $counts = $this->controller->getStatusCounts();

        $views['all'] = Blade::render(
            '<a href="{!! $url !!}" class="{{ $class }}">{!! $label !!} <span class="count">({!! $count !!})</span></a>',
            [
                'url' => esc_url(remove_query_arg('period_status')),
                'class' => $current === 'all' ? 'current' : '',
                'label' => __('All', 'fmr'),
                'count' => number_format_i18n($counts['all'])
            ]
        );

        $views['ongoing'] = Blade::render(
            '<a href="{!! $url !!}" class="{{ $class }}">{!! $label !!} <span class="count">({!! $count !!})</span></a>',
            [
                'url' => esc_url(add_query_arg('period_status', 'ongoing')),
                'class' => $current === 'ongoing' ? 'current' : '',
                'label' => __('Ongoing', 'fmr'),
                'count' => number_format_i18n($counts['ongoing'])
            ]
        );

        $views['past'] = Blade::render(
            '<a href="{!! $url !!}" class="{{ $class }}">{!! $label !!} <span class="count">({!! $count !!})</span></a>',
            [
                'url' => esc_url(add_query_arg('period_status', 'past')),
                'class' => $current === 'past' ? 'current' : '',
                'label' => __('Past', 'fmr'),
                'count' => number_format_i18n($counts['past'])
            ]
        );

        return $views;
    }

    /**
     * Get an array of bulk actions available on this table.
     *
     * @return array Array of bulk actions.
     */
    public function get_bulk_actions()
    {
        return [
            'delete' => __('Delete', 'fmr')
        ];
    }

    /**
     * Get an array of columns to display in the list table.
     *
     * @return array Array of column names and their labels.
     */
    public function get_columns()
    {
        return [
            'cb'            => '<input type="checkbox" />',
            'title'         => __('Title', 'fmr'),
            'board'         => __('Board', 'fmr'),
            'type'          => __('Type', 'fmr'),
            'period'        => __('Period', 'fmr'),
            'author'        => __('Author', 'fmr')
        ];
    }

    /**
     * Get a list of CSS classes for the table tag.
     *
     * @return array Array of CSS classes.
     */
    protected function get_table_classes()
    {
        return ['widefat', 'fixed', 'striped', 'decision-authority-table'];
    }

    /**
     * Get a list of sortable columns.
     *
     * @return array Array of sortable columns with their sort direction.
     */
    public function get_sortable_columns()
    {
        return [
            'title'         => ['title', false],
            'board'         => ['board', false],
            'type'          => ['type', false],
            'author'        => ['author', false]
        ];
    }

    /**
     * Render the checkbox column.
     *
     * @param object $item The current decision authority item.
     * @return string The column output.
     */
    public function column_cb($item)
    {
        return Blade::render(
            '<input type="checkbox" name="decision_authorities[]" value="{!! $value !!}" />',
            ['value' => $item->id]
        );
    }

    /**
     * Render the title column.
     *
     * @param object $item The current decision authority item.
     * @return string The column output.
     */
    public function column_title($item)
    {
        $edit_link = add_query_arg(
            ['page' => 'decision_authority_edit', 'id' => $item->id],
            admin_url('admin.php')
        );

        $delete_link = wp_nonce_url(
            add_query_arg([
                'action' => 'delete_decision_authority',
                'decision_authority_id' => $item->id
            ], admin_url('admin.php')),
            'delete_decision_authority_' . $item->id
        );

        $row_actions = Blade::render(
            '<div class="row-actions">
                <span class="edit"><a href="{!! $edit_url !!}">{!! $edit_label !!}</a> | </span>
                <span class="trash"><a href="{!! $delete_url !!}" onclick="return confirm(\'{!! $confirm_message !!}\')">{!! $delete_label !!}</a></span>
            </div>',
            [
                'edit_url' => esc_url($edit_link),
                'edit_label' => __('Edit', 'fmr'),
                'delete_url' => esc_url($delete_link),
                'confirm_message' => esc_js(__('Are you sure you want to delete this decision authority?', 'fmr')),
                'delete_label' => __('Remove', 'fmr')
            ]
        );

        return Blade::render(
            '<strong>{!! $title !!}</strong>{!! $row_actions !!}',
            [
                'title' => esc_html($item->title),
                'row_actions' => $row_actions
            ]
        );
    }

    /**
     * Render the board column.
     *
     * @param object $item The current decision authority item.
     * @return string The column output.
     */
    public function column_board($item)
    {
        if (!$item->board) {
            return '—';
        }

        $edit_link = get_edit_post_link($item->board->ID);
        $title = esc_html($item->board->post_title);
        
        return Blade::render(
            '<a href="{!! $url !!}">{!! $title !!}</a>',
            [
                'url' => esc_url($edit_link),
                'title' => $title
            ]
        );
    }

    /**
     * Render the type column.
     *
     * @param object $item The current decision authority item.
     * @return string The column output.
     */
    public function column_type($item)
    {
        if (!$item->typeTerm) {
            return '—';
        }

        $edit_link = add_query_arg(
            [
                'taxonomy' => 'type',
                'tag_ID' => $item->typeTerm->term_id,
            ],
            admin_url('term.php')
        );

        $type_name = esc_html($item->typeTerm->name);
        
        return Blade::render(
            '<a href="{!! $url !!}">{!! $name !!}</a>',
            [
                'url' => esc_url($edit_link),
                'name' => $type_name
            ]
        );
    }

    /**
     * Render the period column.
     *
     * @param object $item The current decision authority item.
     * @return string The column output.
     */
    public function column_period($item)
    {
        $start = $item->start_date ? wp_date('Y-m-d', strtotime($item->start_date)) : '—';
        $end = $item->end_date ? wp_date('Y-m-d', strtotime($item->end_date)) : '—';
        
        return sprintf(
            '%s – %s',
            esc_html($start),
            esc_html($end)
        );
    }

    /**
     * Render the author column.
     *
     * @param object $item The current decision authority item.
     * @return string The column output.
     */
    public function column_author($item)
    {
        if (!$item->author) {
            return '—';
        }

        $author_name = esc_html($item->author->display_name ?: $item->author->user_login);
        
        $filter_link = add_query_arg(
            ['author_filter' => $item->author->ID],
            $_SERVER['REQUEST_URI']
        );

        return Blade::render(
            '<a href="{!! $url !!}">{!! $name !!}</a>',
            [
                'url' => esc_url($filter_link),
                'name' => $author_name
            ]
        );
    }

    /**
     * Handle any custom columns that don't have a specific method.
     *
     * @param object $item The current decision authority item.
     * @param string $column_name The name of the column being rendered.
     * @return string The column output.
     */
    public function column_default($item, $column_name)
    {
        return print_r($item, true);
    }

    /**
     * Prepare the items for display in the list table.
     * 
     * This sets up the pagination, sorting, filtering and displays the items.
     *
     * @return void
     */
    public function prepare_items()
    {
        $this->_column_headers = [
            $this->get_columns(),          // columns
            [],                            // hidden columns
            $this->get_sortable_columns(), // sortable columns
            'title'                        // primary column
        ];

        $result = $this->controller->getPaginatedDecisionAuthorities([
            'per_page' => $this->get_items_per_page('edit_decision_authority_per_page'),
            'current_page' => $this->get_pagenum(),
            'orderby' => $_REQUEST['orderby'] ?? 'id',
            'order' => $_REQUEST['order'] ?? 'desc',
            'period_status' => $_REQUEST['period_status'] ?? 'all',
            'search' => isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '',
            'board_filter' => $_REQUEST['board_filter'] ?? '',
            'author_filter' => $_REQUEST['author_filter'] ?? '',
            'start_date' => $_REQUEST['start_date'] ?? '',
            'end_date' => $_REQUEST['end_date'] ?? ''
        ]);

        $this->items = $result['items'];

        $this->set_pagination_args([
            'total_items' => $result['total_items'],
            'per_page'   => $result['per_page'],
            'total_pages'=> $result['total_pages']
        ]);
    }
}