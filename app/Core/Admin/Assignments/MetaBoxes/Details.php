<?php

namespace App\Core\Admin\Assignments\MetaBoxes;

use App\Core\Admin\Abstracts\MetaBox;
use App\Http\Controllers\Admin\PersonController;
use App\Http\Controllers\Admin\DecisionAuthorityController;
use App\Http\Controllers\Admin\RoleController;
use function Roots\view;

class Details extends MetaBox
{
    protected $roleController;
    protected $personController;
    protected $decisionAuthorityController;

    public function __construct($editPage)
    {   
        $this->roleController = app(RoleController::class);
        $this->personController = app(PersonController::class);
        $this->decisionAuthorityController = app(DecisionAuthorityController::class);
        
        parent::__construct($editPage);
    }

    /**
     * Initialize the meta box properties.
     */
    protected function initializeProperties()
    {
        $this->id = 'assignments_details';
        $this->title = __('Details', 'fmr');
        $this->context = 'normal';
        $this->priority = 'high';
    }

    /**
     * Render the meta box content.
     */
    public function render($object, $box)
    {        
        echo view('admin.assignments.meta-boxes.details', [
            'getFieldValue' => function($field, $default = '') {
                return $this->getFieldValue($field, $default);
            },
            'persons' => $this->personController->getAll(),
            'decisionAuthorities' => $this->decisionAuthorityController->getAll(),
            'roles' => $this->roleController->getAll()
        ])->render();
    }

    /**
     * Handle saving the meta box data.
     */
    public function save($data, $object) {}
}
