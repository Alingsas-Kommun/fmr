<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use App\Utilities\ClassFactory;
use App\Utilities\AttributeFactory;

class ToggleSwitch extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $id = '',
        public bool $checked = false,
        public string $onLabel = 'On',
        public string $offLabel = 'Off',
        public array $attr = [],
    ) {
        $this->id = $id ?: $name;
        $this->attr = $this->buildAttributes();
    }

    public function buildAttributes()
    {
        $classes = new ClassFactory();
        $classes->add('toggle-switch');

        $attr = new AttributeFactory();
        $attr->add('class', $classes->get());

        return $attr->toArray();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return $this->view('admin.components.toggle-switch');
    }
}