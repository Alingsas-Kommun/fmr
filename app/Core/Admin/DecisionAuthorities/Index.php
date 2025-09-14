<?php

namespace App\Core\Admin\DecisionAuthorities;

use App\Http\Controllers\Admin\DecisionAuthorityController;
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

        $this->controller = new DecisionAuthorityController();
    }

    /**
     * Register the decision authorities menu page in WordPress admin.
     *
     * @return void
     */
    public static function register()
    {
        self::init();
        
        add_menu_page(
            __('Decision Authorities', 'fmr'),
            __('Decision Authorities', 'fmr'),
            'manage_options',
            'decision_authorities',
            [self::$instance, 'render_page'],
            'dashicons-list-view',
            30
        );
    }

    /**
     * Render the decision authorities list table page.
     *
     * @return void
     */
    public function render_page()
    {
        $this->process_bulk_action();

        settings_errors('bulk_action');

        set_current_screen('decision_authorities');

        echo view('admin.decision-authorities.index', ['list' => $this])->render();
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

        if ($deleted > 0) {
            add_settings_error(
                'bulk_action',
                'decision_authorities_deleted',
                sprintf(
                    _n(
                        '%s decision authority was deleted.',
                        '%s decision authorities were deleted.',
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

        $views['all'] = sprintf(
            '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>',
            esc_url(remove_query_arg('period_status')),
            $current === 'all' ? 'current' : '',
            __('All', 'fmr'),
            number_format_i18n($counts['all'])
        );

        $views['ongoing'] = sprintf(
            '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>',
            esc_url(add_query_arg('period_status', 'ongoing')),
            $current === 'ongoing' ? 'current' : '',
            __('Ongoing', 'fmr'),
            number_format_i18n($counts['ongoing'])
        );

        $views['past'] = sprintf(
            '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>',
            esc_url(add_query_arg('period_status', 'past')),
            $current === 'past' ? 'current' : '',
            __('Past', 'fmr'),
            number_format_i18n($counts['past'])
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
            'edit'          => '<span class="screen-reader-text">' . __('Actions', 'fmr') . '</span>'
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
            'type'          => ['type', false]
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
            echo '<style>
                .wp-list-table .column-edit { 
                    width: 65px;
                    text-align: right;
                }
            </style>';
        }
    }

    /**
     * Render the edit column.
     *
     * @param object $item The current decision authority item.
     * @return string The column output.
     */
    public function column_edit($item)
    {
        $edit_link = add_query_arg(
            ['page' => 'decision_authority_edit', 'id' => $item->id],
            admin_url('admin.php')
        );

        return sprintf(
            '<a href="%s" class="button button-small" style="padding: 0 6px;"><span class="dashicons dashicons-edit" style="margin: 3px 0;"></span></a>',
            esc_url($edit_link)
        );
    }

    /**
     * Render the checkbox column.
     *
     * @param object $item The current decision authority item.
     * @return string The column output.
     */
    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="decision_authorities[]" value="%s" />',
            $item->id
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
        return sprintf(
            '<strong>%s</strong>',
            esc_html($item->title)
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
        
        return sprintf(
            '<a href="%s">%s</a>',
            esc_url($edit_link),
            $title
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
        return esc_html($item->type);
    }

    /**
     * Render the period column.
     *
     * @param object $item The current decision authority item.
     * @return string The column output.
     */
    public function column_period($item)
    {
        $start = $item->start_date ? wp_date('j M Y', strtotime($item->start_date)) : '—';
        $end = $item->end_date ? wp_date('j M Y', strtotime($item->end_date)) : '—';
        
        return sprintf(
            '%s – %s',
            esc_html($start),
            esc_html($end)
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
            'per_page' => 15,
            'current_page' => $this->get_pagenum(),
            'orderby' => $_REQUEST['orderby'] ?? 'id',
            'order' => $_REQUEST['order'] ?? 'desc',
            'period_status' => $_REQUEST['period_status'] ?? 'all',
            'search' => isset($_REQUEST['s']) ? trim($_REQUEST['s']) : ''
        ]);

        $this->items = $result['items'];

        $this->set_pagination_args([
            'total_items' => $result['total_items'],
            'per_page'   => $result['per_page'],
            'total_pages'=> $result['total_pages']
        ]);
    }
}