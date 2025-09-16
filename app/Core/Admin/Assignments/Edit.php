<?php

namespace App\Core\Admin\Assignments;

use App\Core\Admin\Abstracts\EditPage;
use App\Core\Admin\Assignments\MetaBoxes\Details;
use App\Http\Controllers\Admin\AssignmentController;
use Illuminate\Http\Request;

class Edit extends EditPage
{
    protected $controller;

    public function __construct()
    {
        $this->controller = new AssignmentController();

        parent::__construct();
    }

    /**
     * Initialize the properties that must be defined by child classes.
     */
    protected function initializeProperties()
    {
        $this->parentMenuSlug = 'assignments';
        $this->menuTitle = __('Add new assignment', 'fmr');
        $this->pageSlug = 'assignment_edit';
        $this->pageTitle = __('Add new assignment', 'fmr');
        $this->routeName = 'assignments.show';
        $this->pageTitleEdit = __('Edit assignment', 'fmr');
        $this->addNewButtonTitle = __('Add new assignment', 'fmr');
        $this->capability = 'manage_options';
        $this->nonceAction = 'save_assignment';
        $this->nonceField = '_wpnonce';
        $this->formAction = 'save_assignment';
        $this->redirectPage = 'assignment_edit';
        $this->showTitleField = false;
    }

    /**
     * Initialize button text properties with custom values.
     */
    protected function initializeSubmitTexts()
    {
        $this->createButtonText = __('Create Assignment', 'fmr');
        $this->updateButtonText = __('Update Assignment', 'fmr');
    }

    /**
     * Register custom meta boxes. Override this method in child classes.
     */
    protected function registerCustomMetaBoxes()
    {
        new Details($this);
    }

    /**
     * Get the current object being edited.
     */
    protected function getCurrentObject($id = null)
    {
        return $this->controller->edit($id);
    }

    /**
     * Handle the save operation.
     */
    protected function handleSave(Request $request, $id = null)
    {
        foreach (['period_start', 'period_end'] as $field) {
            if ($request->has($field) && !empty($request->input($field))) {
                $request->merge([$field => date('Y-m-d', strtotime($request->input($field)))]);
            }
        }

        try {
            if ($id) {
                $this->controller->update($request, $id);
            } else {
                $this->controller->store($request);
            }
            
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get the success message for create operation.
     */
    protected function getCreateSuccessMessage()
    {
        return __('Assignment created successfully.', 'fmr');
    }

    /**
     * Get the success message for update operation.
     */
    protected function getUpdateSuccessMessage()
    {
        return __('Assignment updated successfully.', 'fmr');
    }
}
