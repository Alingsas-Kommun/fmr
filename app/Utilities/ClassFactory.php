<?php

namespace App\Utilities;

class ClassFactory
{

    /**
     * List of all classes
     *
     * @author Adam Alexandersson
     * @since 1.0.0
     */
    private $classes = [];

    /**
     * List of all classes ( combined string )
     *
     * @author Adam Alexandersson
     * @since 1.0.0
     */
    private $class_string;

    /**
     * Modifiers separator
     *
     * @author Adam Alexandersson
     * @since 1.0.0
     */
    private $mod_separator = '--';

    /**
     * Element separator
     *
     * @author Adam Alexandersson
     * @since 1.0.0
     */
    private $element_separator = '__';

    /**
     * Utility prefix
     *
     * @author Adam Alexandersson
     * @since 1.0.0
     */
    private $utility_prefix = 'u-';

    public function add($class = '')
    {
        if (is_array($class)) {
            foreach ($class as $c) {
                if ($c !== '' && ! in_array($c, $this->classes)) {
                    $this->classes[] = trim($c);
                    $this->classes = array_unique($this->classes);
                }
            }
        } else {
            if ($class !== '' && ! in_array($class, $this->classes)) {
                $this->classes[] = trim($class);
                $this->classes = array_unique($this->classes);
            }
        }

        $this->join();
    }

    public function get()
    {
        return esc_attr($this->class_string);
    }

    public function main()
    {
        $class = $this->classes[0];

        return $class;
    }

    public function addModifier($class = false, $mod = false, $value = false, $bp = '')
    {
        if ($class === false || $mod === false || $value === false) {
            return;
        }

        if (in_array($class, $this->classes) && strpos($class, '--') === false) {
            if ($value === true) {
                $this->classes[] = sprintf('%1$s%2$s%3$s%4$s', $class, $bp, $this->mod_separator, $mod);
            } else {
                $this->classes[] = sprintf('%1$s%2$s%3$s%4$s-%5$s', $class, $bp, $this->mod_separator, $mod, $value);
            }

            $this->join();
        }
    }

    public function addElement($parent = '', $name = '')
    {
        if ($name !== '') {
            $this->classes[] = sprintf('%1$s%2$s%3$s', $parent, $this->element_separator, $name);
            $this->classes = array_unique($this->classes);

            $this->join();
        }
    }

    public function addUtility($name = '')
    {
        if ($name !== '' && ! in_array($this->utility_prefix . $name, $this->classes)) {
            $this->classes[] = sprintf('%1$s%2$s', $this->utility_prefix, $name);
            $this->classes = array_unique($this->classes);

            $this->join();
        }
    }

    public function remove($remove)
    {
        if (( $key = array_search($remove, $this->classes) ) !== false) {
            unset($this->classes[$key]);
            return true;
        }

        return false;
    }

    private function join()
    {
        if (! empty($this->classes)) {
            $this->class_string = join(' ', array_map('trim', array_filter($this->classes)));
        }
    }
}
