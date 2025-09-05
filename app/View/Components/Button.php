<?php

namespace App\View\Components;

use Roots\Acorn\View\Component;
use App\Utilities\ClassFactory;
use App\Utilities\AttributeFactory;
use App\Utilities\General as General;

class Button extends Component
{
    /**
     * The button element.
     *
     * @var string
     */
    public $element;

    /**
     * The button theme type.
     *
     * @var string
     */
    public $themeType;

    /**
     * The button theme color
     *
     * @var string
     */
    public $themeColor;

    /**
     * The button size
     *
     * @var string
     */
    public $size;

    /**
     * The button chevron
     *
     * @var string
     */
    public $chevron;

    /**
     * The button pill
     *
     * @var bool
     */
    public $pill;

    /**
     * The button type
     *
     * @var string
     */
    public $type;

    /**
     * The button link
     *
     * @var mixed
     */
    public $link;

    /**
     * The button class
     *
     * @var string
     */
    public $class;

    /**
     * The button custom attributes.
     *
     * @var array
     */
    public $atts;

    /**
     * The button computed html attributes.
     *
     * @var array
     */
    public $attr;

    /**
     * Create the component instance.
     */
    public function __construct(
        $element = 'button',
        $themeType = 'solid',
        $themeColor = false,
        $size = false,
        $chevron = false,
        $pill = false,
        $type = false,
        $link = [
            'href' => false,
            'title' => false,
            'target' => false,
            'rel' => false,
            'download' => false
        ],
        $class = false,
        $atts = false
    ) {
        $this->element = $element;
        $this->themeType = $themeType;
        $this->themeColor = $themeColor;
        $this->size = $size;
        $this->chevron = $chevron;
        $this->pill = $pill;
        $this->type = $type;
        $this->link = $link;
        $this->class = $class;
        $this->atts = $atts;

        $this->attr = $this->buildAttributes();
    }

    public function buildAttributes()
    {
        $classes = new ClassFactory();

        // Base button classes
        $classes->add('inline-flex items-center justify-center font-semibold transition-colors duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 no-underline');

        // Theme type and color combinations
        if ($this->themeColor) {
            switch ($this->themeColor) {
                case 'primary':
                    if ($this->themeType == 'outline') {
                        $classes->add('border border-indigo-600 text-indigo-600 hover:bg-indigo-50 focus-visible:outline-indigo-600');
                    } else {
                        $classes->add('bg-indigo-600 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-indigo-600');
                    }
                    break;
                case 'secondary':
                    if ($this->themeType == 'outline') {
                        $classes->add('border border-gray-300 text-gray-700 hover:bg-gray-50 focus-visible:outline-gray-600');
                    } else {
                        $classes->add('bg-gray-600 text-white shadow-sm hover:bg-gray-500 focus-visible:outline-gray-600');
                    }
                    break;
                case 'success':
                    if ($this->themeType == 'outline') {
                        $classes->add('border border-green-600 text-green-600 hover:bg-green-50 focus-visible:outline-green-600');
                    } else {
                        $classes->add('bg-green-600 text-white shadow-sm hover:bg-green-500 focus-visible:outline-green-600');
                    }
                    break;
                case 'danger':
                    if ($this->themeType == 'outline') {
                        $classes->add('border border-red-600 text-red-600 hover:bg-red-50 focus-visible:outline-red-600');
                    } else {
                        $classes->add('bg-red-600 text-white shadow-sm hover:bg-red-500 focus-visible:outline-red-600');
                    }
                    break;
                case 'warning':
                    if ($this->themeType == 'outline') {
                        $classes->add('border border-yellow-600 text-yellow-600 hover:bg-yellow-50 focus-visible:outline-yellow-600');
                    } else {
                        $classes->add('bg-yellow-600 text-white shadow-sm hover:bg-yellow-500 focus-visible:outline-yellow-600');
                    }
                    break;
                default:
                    if ($this->themeType == 'outline') {
                        $classes->add('border border-gray-300 text-gray-700 hover:bg-gray-50 focus-visible:outline-gray-600');
                    } else {
                        $classes->add('bg-gray-900 text-white shadow-sm hover:bg-gray-700 focus-visible:outline-gray-600');
                    }
            }
        } else {
            // Default styling if no theme color specified
            if ($this->themeType == 'outline') {
                $classes->add('border border-gray-300 text-gray-700 hover:bg-gray-50 focus-visible:outline-gray-600');
            } else {
                $classes->add('bg-gray-900 text-white shadow-sm hover:bg-gray-700 focus-visible:outline-gray-600');
            }
        }

        // Pill styling
        if ($this->pill) {
            $classes->add('rounded-full');
        } else {
            $classes->add('rounded-md');
        }

        // Size variations
        if ($this->size) {
            switch ($this->size) {
                case 'xs':
                    $classes->add('px-2 py-1 text-xs');
                    break;
                case 'sm':
                    $classes->add('px-2.5 py-1.5 text-sm');
                    break;
                case 'lg':
                    $classes->add('px-4 py-2.5 text-base');
                    break;
                case 'xl':
                    $classes->add('px-6 py-3 text-lg');
                    break;
                default: // md
                    $classes->add('px-3.5 py-2 text-sm');
            }
        } else {
            // Default size (md)
            $classes->add('px-3.5 py-2 text-sm');
        }

        // Chevron styling
        if ($this->chevron) {
            $classes->add('gap-x-1');
        }

        if ($this->class) {
            $classes->add($this->class);
        }

        $attr = new AttributeFactory();
        $attr->add('class', $classes->get());

        if ($this->type) {
            $attr->add('type', $this->type);
        }

        if (gettype($this->link) === 'string') {
            $this->link = General::buildLink($this->link);
        } else {
            $this->link = (object) $this->link;
        }

        if (isset($this->link->href) && $this->link->href) {
            $attr->add('href', $this->link->href);
            $attr->add('role', 'button');
            $attr->remove('type');

            $this->element = 'a';
        }

        if (isset($this->link->url) && $this->link->url) {
            $attr->add('href', $this->link->url);

            $attr->add('role', 'button');
            $attr->remove('type');

            $this->element = 'a';
        }

        if (isset($this->link->title) && $this->link->title) {
            $attr->add('title', $this->link->title);
        }

        if (isset($this->link->target) && $this->link->target) {
            $attr->add('target', $this->link->target);
        }

        if (isset($this->link->rel) && $this->link->rel) {
            $attr->add('rel', $this->link->rel);
        }

        if (isset($this->link->download) && $this->link->download) {
            $attr->add('download', $this->link->download);
        }

        return $attr->toArray();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return $this->view('components.button');
    }
}