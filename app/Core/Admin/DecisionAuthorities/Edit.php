<?php

namespace App\Core\Admin\DecisionAuthorities;

use App\Http\Controllers\Admin\DecisionAuthorityController;
use App\Http\Controllers\Admin\BoardController;
use Illuminate\Http\Request;

use function Roots\view;

class Edit
{
    protected $controller;
    protected $boardController;

    public function __construct()
    {
        $this->controller = new DecisionAuthorityController();
        $this->boardController = new BoardController();

        add_action('admin_menu', function () {
            add_submenu_page(
                'decision_authorities',
                __('Add new decision authority', 'fmr'),
                __('Add new decision authority', 'fmr'),
                'manage_options',
                'decision_authority_edit',
                [$this, 'handleEdit']
            );
        });
        
        add_action('admin_post_save_decision_authority', [$this, 'handleSave']);
    }

    /**
     * Handle the edit page display.
     *
     * @return void
     */
    public function handleEdit()
    {
        $id = $_GET['id'] ?? null;
        $decisionAuthority = $this->controller->show($id);

        echo view('admin.decision-authorities.edit', [
            'decisionAuthority' => $decisionAuthority,
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

        check_admin_referer('save_decision_authority');

        $data = $_POST;
        
        foreach (['start_date', 'end_date'] as $field) {
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
                ? __('Decision authority updated successfully.', 'fmr')
                : __('Decision authority created successfully.', 'fmr');

            add_settings_error(
                'decision_authority_update',
                'decision_authority_updated',
                $message,
                'updated'
            );

        } catch (Exception $e) {
            add_settings_error(
                'decision_authority_update',
                'decision_authority_error',
                $e->getMessage(),
                'error'
            );
        }

        wp_redirect(add_query_arg(
            ['page' => 'decision_authorities', 'updated' => 1],
            admin_url('admin.php')
        ));
        exit;
    }
}
