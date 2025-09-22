<?php

namespace App\Core\Admin\DecisionAuthorities\MetaBoxes;

use App\Core\Admin\Abstracts\MetaBox;
use App\Http\Controllers\Admin\BoardController;
use function Roots\view;

class Details extends MetaBox
{
    protected $boardController;

    public function __construct($editPage)
    {
        $this->boardController = app(BoardController::class);
        
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
     */
    public function render($object, $box)
    {        
        echo view('admin.decision-authorities.meta-boxes.details', [
            'object' => $object,
            'boards' => $this->boardController->getAll(),
            'getFieldValue' => function($field, $default = '') {
                return $this->getFieldValue($field, $default);
            }
        ])->render();
    }

    /**
     * Handle saving the meta box data.
     */
    public function save($data, $object) {}
}
