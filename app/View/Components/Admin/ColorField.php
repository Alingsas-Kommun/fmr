<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class ColorField extends Component
{
    public $id;
    public $name;
    public $value;
    public $default;
    public $optional;
    public $label;
    public $description;
    public $cssVar;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id,
        string $name,
        $value = null,
        string $default = '#000000',
        bool $optional = false,
        string $label = '',
        string $description = '',
        string $cssVar = ''
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value ?: $default;
        $this->default = $default;
        $this->optional = $optional;
        $this->label = $label;
        $this->description = $description;
        $this->cssVar = $cssVar;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('admin.components.color-field');
    }
}
