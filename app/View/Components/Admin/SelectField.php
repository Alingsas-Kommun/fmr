<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class SelectField extends Component
{
    public $id;
    public $name;
    public $value;
    public $optional;
    public $label;
    public $description;
    public $fullWidth;
    public $options;

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
        bool $fullWidth = false,
        array $options = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->optional = $optional;
        $this->label = $label;
        $this->description = $description;
        $this->fullWidth = $fullWidth;
        $this->options = $options;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('admin.components.select-field');
    }
}
