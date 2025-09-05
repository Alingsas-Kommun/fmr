<?php

namespace App\Core\Admin;

use App\Http\Controllers\Admin\AssignmentController;
use Illuminate\Http\Request;
use App\Models\Assignment;

use function Roots\view;

class AssignmentHandler
{
    protected $controller;

    public function __construct()
    {
        $this->controller = new AssignmentController();
    }

    /**
     * Handle the edit page display.
     *
     * @return void
     */
    public function handleEdit()
    {
        $id = $_GET['id'] ?? null;
        $assignment = $id ? $this->controller->show($id) : new Assignment();

        echo view('admin.assignments.edit', ['assignment' => $assignment])->render();
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

        // Format dates for database
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

        } catch (\Exception $e) {
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
