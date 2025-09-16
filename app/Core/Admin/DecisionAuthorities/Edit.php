<?php

namespace App\Core\Admin\DecisionAuthorities;

use App\Core\Admin\Abstracts\EditPage;
use App\Core\Admin\DecisionAuthorities\MetaBoxes\Details;
use App\Core\Admin\DecisionAuthorities\MetaBoxes\Assignments;
use App\Http\Controllers\Admin\DecisionAuthorityController;
use App\Http\Controllers\Admin\BoardController;
use Illuminate\Http\Request;

class Edit extends EditPage
{
    protected $controller;
    protected $boardController;

    public function __construct()
    {
        $this->controller = new DecisionAuthorityController();
        $this->boardController = new BoardController();

        parent::__construct();
    }

    /**
     * Initialize the properties that must be defined by child classes.
     */
    protected function initializeProperties()
    {
        $this->parentMenuSlug = 'decision_authorities';
        $this->menuTitle = __('Add new decision authority', 'fmr');
        $this->pageSlug = 'decision_authority_edit';
        $this->routeName = 'decision-authorities.show';
        $this->pageTitle = __('Add new decision authority', 'fmr');
        $this->pageTitleEdit = __('Edit decision authority', 'fmr');
        $this->addNewButtonTitle = __('Add new decision authority', 'fmr');
        $this->capability = 'manage_options';
        $this->nonceAction = 'save_decision_authority';
        $this->nonceField = '_wpnonce';
        $this->formAction = 'save_decision_authority';
        $this->redirectPage = 'decision_authority_edit';
        $this->showTitleField = true;
    }

    /**
     * Initialize button text properties with custom values.
     */
    protected function initializeSubmitTexts()
    {
        $this->createButtonText = __('Create Decision Authority', 'fmr');
        $this->updateButtonText = __('Update Decision Authority', 'fmr');
    }

    /**
     * Register custom meta boxes. Override this method in child classes.
     */
    protected function registerCustomMetaBoxes()
    {
        new Details($this);
        new Assignments($this);
    }

    /**
     * Get the current object being edited.
     */
    protected function getCurrentObject($id = null)
    {
        return $this->controller->show($id);
    }

    /**
     * Handle the save operation.
     */
    protected function handleSave(Request $request, $id = null)
    {
        foreach (['start_date', 'end_date'] as $field) {
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
        return __('Decision authority created successfully.', 'fmr');
    }

    /**
     * Get the success message for update operation.
     */
    protected function getUpdateSuccessMessage()
    {
        return __('Decision authority updated successfully.', 'fmr');
    }
}
