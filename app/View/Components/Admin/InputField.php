<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class InputField extends Component
{
    public $id;
    public $name;
    public $value;
    public $optional;
    public $label;
    public $description;
    public $type;
    public $min;
    public $max;
    public $step;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id,
        string $name,
        $value = null,
        bool $optional = false,
        string $label = '',
        string $description = '',
        string $type = 'text',
        $min = null,
        $max = null,
        $step = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->optional = $optional;
        $this->label = $label;
        $this->description = $description;
        $this->type = $type;
        $this->min = $min;
        $this->max = $max;
        $this->step = $step;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('admin.components.input-field');
    }
}
