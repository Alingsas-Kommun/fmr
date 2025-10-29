<?php

namespace App\Core\Admin\Assignments\MetaBoxes;

use App\Core\Admin\Abstracts\MetaBox;
use App\Http\Controllers\Admin\PersonController;
use App\Http\Controllers\Admin\DecisionAuthorityController;
use App\Http\Controllers\Admin\RoleController;
use function Roots\view;

class Details extends MetaBox
{
    /**
     * The role controller
     *
     * @var RoleController
     */
    protected $roleController;

    /**
     * The person controller
     *
     * @var PersonController
     */
    protected $personController;

    /**
     * The decision authority controller
     *
     * @var DecisionAuthorityController
     */
    protected $decisionAuthorityController;

    /**
     * Constructor. Set up the meta box properties.
     *
     * @param EditPage $editPage
     * @return void
     */
    public function __construct($editPage)
    {   
        $this->roleController = app(RoleController::class);
        $this->personController = app(PersonController::class);
        $this->decisionAuthorityController = app(DecisionAuthorityController::class);
        
        parent::__construct($editPage);
    }

    /**
     * Initialize the meta box properties.
     * 
     * @return void
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
     *
     * @param object $object
     * @param string $box
     * @return void
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
     *
     * @param array $data
     * @param object $object
     * @return void
     */
    public function save($data, $object) {}
}
