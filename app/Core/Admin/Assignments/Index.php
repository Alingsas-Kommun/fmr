<?php

namespace App\Core\Admin\Assignments;

use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\BoardController;
use App\Http\Controllers\Admin\PersonController;
use App\Http\Controllers\Admin\RoleController;
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
 * Class Assignments
 * @package App\Core\Admin
 */
class Index extends \WP_List_Table
{
    private static $instance = null;
    protected $controller;

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
            'singular' => 'assignment',
            'plural'   => 'assignments',
            'ajax'     => false
        ]);

        $this->controller = app(AssignmentController::class);
        
    }

    /**
     * Register the assignments menu page in WordPress admin.
     *
     * @return void
     */
    public static function register()
    {
        self::init();
        
        $hook = add_menu_page(
            __('Assignments', 'fmr'),
            __('Assignments', 'fmr'),
            'manage_options',
            'assignments',
            [self::$instance, 'render_page'],
            'dashicons-portfolio',
            40
        );

        add_submenu_page(
            'assignments',
            __('Roles', 'fmr'),
            __('Roles', 'fmr'),
            'manage_options',
            'edit-tags.php?taxonomy=role',
            null
        );

        // Hook into admin actions
        add_action('admin_action_delete_assignment', [self::$instance, 'handle_delete_assignment']);

        // Add screen options for per page
        add_action("load-$hook", function() {
            add_screen_option('per_page', array(
                'label'   => __('Assignments per page', 'fmr'),
                'default' => 20,
                'option'  => 'edit_assignment_per_page'
            ));
        });

        // Handle screen options saving ourselves
        add_action('admin_init', function() {
            if (isset($_POST['wp_screen_options']) && isset($_POST['wp_screen_options']['option']) && $_POST['wp_screen_options']['option'] === 'edit_assignment_per_page') {
                $per_page = (int) $_POST['wp_screen_options']['value'];
                if ($per_page > 0) {
                    update_user_meta(get_current_user_id(), 'edit_assignment_per_page', $per_page);
                }
            }
        });
    }

    /**
     * Render the assignments list table page.
     *
     * @return void
     */
    public function render_page()
    {
        // Handle export requests
        $export = $_REQUEST['export'] ?? null;
        if ($export && in_array($export, ['excel', 'csv'])) {
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have permission to perform this action.', 'fmr'));
            }
    
            $this->controller->handleExport($export);
            
            return;
        }

        $this->process_bulk_action();

        set_current_screen('assignments');

        // Initialize controllers
        $roleController = app(RoleController::class);
        $boardController = app(BoardController::class);
        $personController = app(PersonController::class);

        echo view('admin.assignments.index', [
            'list' => $this,
            'filter_data' => [
                'roles' => $roleController->getAll(),
                'boards' => $boardController->getAll(),
                'persons' => $personController->getAll(),
            ]
        ])->render();
    }

    /**
     * Handle single assignment deletion via admin action.
     *
     * @return void
     */
    public function handle_delete_assignment()
    {
        if (!isset($_REQUEST['assignment_id']) || !isset($_REQUEST['_wpnonce'])) {
            wp_die(__('Invalid request.', 'fmr'));
        }

        $assignment_id = intval($_REQUEST['assignment_id']);
        $nonce = $_REQUEST['_wpnonce'];

        if (!wp_verify_nonce($nonce, 'delete_assignment_' . $assignment_id)) {
            wp_die(__('Security check failed. Please try again.', 'fmr'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'fmr'));
        }

        $this->controller->destroy($assignment_id);

        wp_redirect(wp_get_referer());

        exit;
    }

    /**
     * Process bulk actions (e.g., delete multiple assignments).
     *
     * @return void
     */
    public function process_bulk_action()
    {
        $action = $this->current_action();

        if ($action !== 'delete') {
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'fmr'));
        }

        check_admin_referer('bulk-' . $this->_args['plural']);

        $assignments = $_REQUEST['assignments'] ?? [];
        if (empty($assignments)) {
            return;
        }

        $deleted = 0;

        foreach ($assignments as $id) {
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
            'cb'                    => '<input type="checkbox" />',
            'person'                => __('Person', 'fmr'),
            'role'                  => __('Role', 'fmr'),
            'board'                 => __('Board', 'fmr'),
            'decision_authority'    => __('Decision Authority', 'fmr'),
            'period'                => __('Period', 'fmr'),
            'author'                => __('Author', 'fmr')
        ];
    }

    /**
     * Get a list of CSS classes for the table tag.
     *
     * @return array Array of CSS classes.
     */
    protected function get_table_classes()
    {
        return ['widefat', 'fixed', 'striped', 'assignment-table'];
    }

    /**
     * Get a list of sortable columns.
     *
     * @return array Array of sortable columns with their sort direction.
     */
    public function get_sortable_columns()
    {
        return [
            'person'             => ['person', false],
            'role'               => ['role', false],
            'board'              => ['board', false],
            'decision_authority' => ['decision_authority', false],
            'period'             => ['period', false],
            'author'             => ['author', false]
        ];
    }

    /**
     * Extra controls to be displayed between bulk actions and pagination.
     *
     * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
     * @return void
     */
    protected function extra_tablenav($which)
    {
        if ($which === 'top') {
            echo Blade::render(
                '<div class="export-actions">
                    <a href="{!! $excel_url !!}" class="button button-secondary" title="{!! $excel_title !!}">
                        📊 {!! $excel_label !!}
                    </a>
                    <a href="{!! $csv_url !!}" class="button button-secondary" title="{!! $csv_title !!}">
                        📄 {!! $csv_label !!}
                    </a>
                </div>',
                [
                    'excel_url' => esc_url(add_query_arg(['export' => 'excel'], $_SERVER['REQUEST_URI'])),
                    'excel_title' => __('Export to Excel', 'fmr'),
                    'excel_label' => __('Export Excel', 'fmr'),
                    'csv_url' => esc_url(add_query_arg(['export' => 'csv'], $_SERVER['REQUEST_URI'])),
                    'csv_title' => __('Export to CSV', 'fmr'),
                    'csv_label' => __('Export CSV', 'fmr')
                ]
            );
        }
    }

    /**
     * Render the checkbox column.
     *
     * @param object $item The current assignment item.
     * @return string The column output.
     */
    public function column_cb($item)
    {
        return Blade::render(
            '<input type="checkbox" name="assignments[]" value="{!! $value !!}" />',
            ['value' => $item->id]
        );
    }

    /**
     * Render the person column.
     *
     * @param object $item The current assignment item.
     * @return string The column output.
     */
    public function column_person($item)
    {
        if (!$item->person) {
            return '—';
        }

        $edit_link = get_edit_post_link($item->person->ID);
        $title = esc_html($item->person->post_title);
        
        $edit_assignment_link = add_query_arg(
            ['page' => 'assignment_edit', 'id' => $item->id],
            admin_url('admin.php')
        );

        $delete_link = wp_nonce_url(
            add_query_arg([
                'action' => 'delete_assignment',
                'assignment_id' => $item->id
            ], admin_url('admin.php')),
            'delete_assignment_' . $item->id
        );

        $row_actions = Blade::render(
            '<div class="row-actions">
                <span class="edit"><a href="{!! $edit_url !!}">{!! $edit_label !!}</a> | </span>
                <span class="trash"><a href="{!! $delete_url !!}" onclick="return confirm(\'{!! $confirm_message !!}\')">{!! $delete_label !!}</a></span>
            </div>',
            [
                'edit_url' => esc_url($edit_assignment_link),
                'edit_label' => __('Edit', 'fmr'),
                'delete_url' => esc_url($delete_link),
                'confirm_message' => esc_js(__('Are you sure you want to delete this assignment?', 'fmr')),
                'delete_label' => __('Remove', 'fmr')
            ]
        );
        
        return Blade::render(
            '<strong><a href="{!! $edit_url !!}">{!! $title !!}</a></strong>{!! $row_actions !!}',
            [
                'edit_url' => esc_url($edit_link),
                'title' => $title,
                'row_actions' => $row_actions
            ]
        );
    }

    /**
     * Render the board (board) column.
     *
     * @param object $item The current assignment item.
     * @return string The column output.
     */
    public function column_board($item)
    {
        if (!$item->board) {
            return '—';
        }

        $edit_link = get_edit_post_link($item->board->ID);
        $title = esc_html($item->board->post_title);
        
        return sprintf('<a href="%s">%s</a>', esc_url($edit_link), $title);
    }

    /**
     * Render the decision authority column.
     *
     * @param object $item The current assignment item.
     * @return string The column output.
     */
    public function column_decision_authority($item)
    {
        if (!$item->decisionAuthority) {
            return '—';
        }

        $title = esc_html($item->decisionAuthority->title);

        $edit_link = add_query_arg(
            ['page' => 'decision_authority_edit', 'id' => $item->decisionAuthority->id],
            admin_url('admin.php')
        );

        return sprintf('<a href="%s">%s</a>', $edit_link, $title);
    }

    /**
     * Render the period column.
     *
     * @param object $item The current assignment item.
     * @return string The column output.
     */
    public function column_period($item)
    {
        $start = $item->period_start ? wp_date('Y-m-d', strtotime($item->period_start)) : '—';
        $end = $item->period_end ? wp_date('Y-m-d', strtotime($item->period_end)) : '—';
        
        return sprintf(
            '%s – %s',
            esc_html($start),
            esc_html($end)
        );
    }

    /**
     * Render the role column.
     *
     * @param object $item The current assignment item.
     * @return string The column output.
     */
    public function column_role($item)
    {
        if (!$item->roleTerm) {
            return '—';
        }

        $edit_link = add_query_arg(
            [
                'taxonomy' => 'role',
                'tag_ID' => $item->roleTerm->term_id,
            ],
            admin_url('term.php')
        );

        $role_name = esc_html($item->roleTerm->name);

        return Blade::render(
            '<a href="{!! $url !!}">{!! $name !!}</a>',
            [
                'url' => esc_url($edit_link),
                'name' => $role_name
            ]
        );
    }

    /**
     * Render the author column.
     *
     * @param object $item The current assignment item.
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
     * @param object $item The current assignment item.
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
            'person'                       // primary column
        ];

        $result = $this->controller->getPaginatedAssignments([
            'per_page' => $this->get_items_per_page('edit_assignment_per_page'),
            'current_page' => $this->get_pagenum(),
            'orderby' => $_REQUEST['orderby'] ?? 'id',
            'order' => $_REQUEST['order'] ?? 'desc',
            'period_status' => $_REQUEST['period_status'] ?? 'all',
            'search' => isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '',
            'role_filter' => $_REQUEST['role_filter'] ?? '',
            'board_filter' => $_REQUEST['board_filter'] ?? '',
            'person_filter' => $_REQUEST['person_filter'] ?? '',
            'author_filter' => $_REQUEST['author_filter'] ?? '',
            'period_start' => $_REQUEST['period_start'] ?? '',
            'period_end' => $_REQUEST['period_end'] ?? ''
        ]);

        $this->items = $result['items'];

        $this->set_pagination_args([
            'total_items' => $result['total_items'],
            'per_page'   => $result['per_page'],
            'total_pages'=> $result['total_pages']
        ]);
    }
}

