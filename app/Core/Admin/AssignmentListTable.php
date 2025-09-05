<?php

namespace App\Core\Admin;

use App\Models\Assignment;
use function Roots\view;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    require_once(ABSPATH . 'wp-admin/includes/screen.php');
}

/**
 * Class AssignmentListTable
 * @package App\Core\Admin
 */
class AssignmentListTable extends \WP_List_Table
{
    private static $instance = null;

    public static function init()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    private function __construct()
    {
        parent::__construct([
            'singular' => 'assignment',
            'plural'   => 'assignments',
            'ajax'     => false
        ]);
    }

    public static function register()
    {
        self::init();
        
        // Add main menu page
        add_menu_page(
            __('Assignments', 'fmr'),
            __('Assignments', 'fmr'),
            'manage_options',
            'assignments',
            [self::$instance, 'render_page'],
            'dashicons-list-view',
            30
        );

        $assignmentHandler = new AssignmentHandler();

        // Add hidden edit page
        add_submenu_page(
            null,
            __('Add/Edit Assignment', 'fmr'),
            __('Add/Edit Assignment', 'fmr'),
            'manage_options',
            'assignment_edit',
            [$assignmentHandler, 'handleEdit']
        );

        // Register save handler
        add_action('admin_post_save_assignment', [$assignmentHandler, 'handleSave']);
    }

    public function render_page()
    {
        // Process bulk actions
        $this->process_bulk_action();

        // Display any error messages
        settings_errors('bulk_action');

        // Ensure we have the correct screen
        set_current_screen('assignments');

        echo view('admin.assignments.list-table', ['list_table' => $this])->render();
    }

    public function process_bulk_action()
    {
        $action = $this->current_action();

        if ($action !== 'delete') {
            return;
        }

        // Verify nonce
        check_admin_referer('bulk-' . $this->_args['plural']);

        // Get assignments to delete
        $assignments = $_REQUEST['assignments'] ?? [];
        if (empty($assignments)) {
            return;
        }

        // Process deletions
        $controller = new \App\Http\Controllers\Admin\AssignmentController();
        $deleted = 0;

        foreach ($assignments as $id) {
            if ($controller->destroy($id)) {
                $deleted++;
            }
        }

        if ($deleted > 0) {
            add_settings_error(
                'bulk_action',
                'assignments_deleted',
                sprintf(
                    _n(
                        '%s assignment was deleted.',
                        '%s assignments were deleted.',
                        $deleted,
                        'fmr'
                    ),
                    number_format_i18n($deleted)
                ),
                'updated'
            );
        }

        wp_redirect(wp_get_referer());
    }

    public function get_views()
    {
        $views = [];
        $current = isset($_REQUEST['period_status']) ? $_REQUEST['period_status'] : 'all';

        // Get counts
        $all_count = Assignment::count();
        $ongoing_count = Assignment::where(function($query) {
            $query->where('period_start', '<=', date('Y-m-d'))
                ->where(function($q) {
                    $q->where('period_end', '>=', date('Y-m-d'))
                        ->orWhereNull('period_end');
                });
        })->count();
        $past_count = Assignment::where('period_end', '<', date('Y-m-d'))->count();

        // Build views array
        $views['all'] = sprintf(
            '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>',
            esc_url(\remove_query_arg('period_status')),
            $current === 'all' ? 'current' : '',
            __('All', 'fmr'),
            number_format_i18n($all_count)
        );

        $views['ongoing'] = sprintf(
            '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>',
            esc_url(\add_query_arg('period_status', 'ongoing')),
            $current === 'ongoing' ? 'current' : '',
            __('Ongoing', 'fmr'),
            number_format_i18n($ongoing_count)
        );

        $views['past'] = sprintf(
            '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>',
            esc_url(\add_query_arg('period_status', 'past')),
            $current === 'past' ? 'current' : '',
            __('Past', 'fmr'),
            number_format_i18n($past_count)
        );

        return $views;
    }

    public function get_bulk_actions()
    {
        return [
            'delete' => __('Delete', 'fmr')
        ];
    }

    public function get_columns()
    {
        return [
            'cb'            => '<input type="checkbox" />',
            'person'        => __('Person', 'fmr'),
            'institution'   => __('Board', 'fmr'),
            'role'          => __('Role', 'fmr'),
            'period'        => __('Period', 'fmr'),
            'edit'          => ''
        ];
    }

    public function column_edit($item)
    {
        $edit_link = add_query_arg(
            ['page' => 'assignment_edit', 'id' => $item->id],
            admin_url('admin.php')
        );

        return sprintf(
            '<a href="%s" class="button button-small" style="padding: 0 6px;"><span class="dashicons dashicons-edit" style="margin: 3px 0;"></span></a>',
            esc_url($edit_link)
        );
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="assignments[]" value="%s" />',
            $item->id
        );
    }

    public function column_person($item)
    {
        if (!$item->person) {
            return '—';
        }

        $edit_link = get_edit_post_link($item->person->ID);
        $title = esc_html($item->person->post_title);
        
        return sprintf(
            '<a href="%s">%s</a>',
            esc_url($edit_link),
            $title
        );
    }

    public function column_institution($item)
    {
        if (!$item->board) {
            return '—';
        }

        $edit_link = get_edit_post_link($item->board->ID);
        $title = esc_html($item->board->post_title);
        
        return sprintf(
            '<a href="%s">%s</a>',
            esc_url($edit_link),
            $title
        );
    }

    public function column_period($item)
    {
        $start = $item->period_start ? wp_date('j M Y', strtotime($item->period_start)) : '—';
        $end = $item->period_end ? wp_date('j M Y', strtotime($item->period_end)) : '—';
        
        return sprintf(
            '%s – %s',
            esc_html($start),
            esc_html($end)
        );
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'role':
                return esc_html($item->$column_name);
            default:
                return print_r($item, true);
        }
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = [];
        
        $this->_column_headers = [$columns, $hidden, $sortable];

        $per_page = 20;
        $current_page = $this->get_pagenum();

        // Build query
        $query = Assignment::with(['person', 'board']);

        // Handle period status filter
        $period_status = isset($_REQUEST['period_status']) ? $_REQUEST['period_status'] : 'all';
        if ($period_status === 'ongoing') {
            $query->where(function($q) {
                $q->where('period_start', '<=', date('Y-m-d'))
                    ->where(function($q) {
                        $q->where('period_end', '>=', date('Y-m-d'))
                            ->orWhereNull('period_end');
                    });
            });
        } elseif ($period_status === 'past') {
            $query->where('period_end', '<', date('Y-m-d'));
        }

        // Handle search
        $search = isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->whereHas('person', function($q) use ($search) {
                    $q->where('post_title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('board', function($q) use ($search) {
                    $q->where('post_title', 'like', '%' . $search . '%');
                })
                ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        $total_items = $query->count();

        $this->items = $query->skip(($current_page - 1) * $per_page)
            ->take($per_page)
            ->get();

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'   => $per_page,
            'total_pages'=> ceil($total_items / $per_page)
        ]);
    }
}

