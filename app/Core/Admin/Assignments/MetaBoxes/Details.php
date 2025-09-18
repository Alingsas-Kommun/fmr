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
        $this->roleController = new RoleController();
        $this->personController = new PersonController();
        $this->decisionAuthorityController = new DecisionAuthorityController();
        
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
            'persons' => $this->getPersons(),
            'decisionAuthorities' => $this->getDecisionAuthorities(),
            'roles' => $this->getRoles()
        ])->render();
    }

    /**
     * Handle saving the meta box data.
     */
    public function save($data, $object) {}

    /**
     * Get all roles from the role taxonomy.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRoles()
    {
        return $this->roleController->getAll();
    }

    /**
     * Get all persons for the meta box.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPersons()
    {
        return $this->personController->getAll();
    }

    /**
     * Get all decision authorities for the meta box.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDecisionAuthorities()
    {
        return $this->decisionAuthorityController->getAll();
    }
}
