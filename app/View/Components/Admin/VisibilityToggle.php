<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use App\Utilities\ClassFactory;
use App\Utilities\AttributeFactory;

class VisibilityToggle extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public ?bool $isVisible = false,
        public string $size = '',
        public array $attr = [],
    ) {
        $this->name = $name;
        $this->isVisible = $isVisible;
        $this->size = $size;

        $this->attr = $this->buildAttributes();
    }

    public function buildAttributes()
    {
        $classes = new ClassFactory();

        $classes->add('visibility-toggle-button');

        if ($this->size) {
            $classes->add($this->size);
        }

        $attr = new AttributeFactory();
        $attr->add('class', $classes->get());

        $attr->add('type', 'button');

        return $attr->toArray();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return $this->view('admin.components.visibility-toggle');
    }
}