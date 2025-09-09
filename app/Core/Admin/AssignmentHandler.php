<?php

namespace App\Core\Admin;

use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\PersonController;
use App\Http\Controllers\Admin\BoardController;
use Illuminate\Http\Request;

use function Roots\view;

class AssignmentHandler
{
    protected $controller;
    protected $personController;
    protected $boardController;

    public function __construct()
    {
        $this->controller = new AssignmentController();
        $this->personController = new PersonController();
        $this->boardController = new BoardController();

        add_action('admin_menu', function () {
            add_submenu_page(
                'assignments',
                __('Add new assignment', 'fmr'),
                __('Add new assignment', 'fmr'),
                'manage_options',
                'assignment_edit',
                [$this, 'handleEdit']
            );
        });
        
        add_action('admin_post_save_assignment', [$this, 'handleSave']);
    }

    /**
     * Handle the edit page display.
     *
     * @return void
     */
    public function handleEdit()
    {
        $id = $_GET['id'] ?? null;
        $assignment = $this->controller->edit($id);

        echo view('admin.assignments.edit', [
            'assignment' => $assignment,
            'persons' => $this->personController->getAll(),
            'boards' => $this->boardController->getAll()
        ])->render();
    }

    /**
     * Handle form submission.
     *
     * @return void
     */
    public function handleSave()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'fmr'));
        }

        check_admin_referer('save_assignment');

        $data = $_POST;
        
        foreach (['period_start', 'period_end'] as $field) {
            if (!empty($data[$field])) {
                $data[$field] = date('Y-m-d', strtotime($data[$field]));
            }
        }

        $request = new Request($data);
        $id = $request->input('id');

        try {
            if ($id) {
                $this->controller->update($request, $id);
            } else {
                $this->controller->store($request);
            }

            $message = $id 
                ? __('Assignment updated successfully.', 'fmr')
                : __('Assignment created successfully.', 'fmr');

            add_settings_error(
                'assignment_update',
                'assignment_updated',
                $message,
                'updated'
            );

        } catch (Exception $e) {
            add_settings_error(
                'assignment_update',
                'assignment_error',
                $e->getMessage(),
                'error'
            );
        }

        wp_redirect(add_query_arg(
            ['page' => 'assignments', 'updated' => 1],
            admin_url('admin.php')
        ));
        exit;
    }
}
