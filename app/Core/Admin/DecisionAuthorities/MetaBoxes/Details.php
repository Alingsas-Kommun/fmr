<?php

namespace App\Core\Admin\DecisionAuthorities\MetaBoxes;

use App\Core\Admin\Abstracts\MetaBox;
use App\Http\Controllers\Admin\BoardController;
use App\Http\Controllers\Admin\TypeController;
use function Roots\view;

class Details extends MetaBox
{
    /**
     * The board controller
     *
     * @var BoardController
     */
    protected $boardController;

    /**
     * The type controller
     *
     * @var TypeController
     */
    protected $typeController;

    /**
     * Constructor. Set up the meta box properties.
     *
     * @param EditPage $editPage
     * @return void
     */
    public function __construct($editPage)
    {
        $this->boardController = app(BoardController::class);
        $this->typeController = app(TypeController::class);
        
        parent::__construct($editPage);
    }

    /**
     * Initialize the meta box properties.
     */
    protected function initializeProperties()
    {
        $this->id = 'decision_authorities_details';
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
        echo view('admin.decision-authorities.meta-boxes.details', [
            'object' => $object,
            'boards' => $this->boardController->getAll(),
            'types' => $this->typeController->getAll(),
            'getFieldValue' => function($field, $default = '') {
                return $this->getFieldValue($field, $default);
            }
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
