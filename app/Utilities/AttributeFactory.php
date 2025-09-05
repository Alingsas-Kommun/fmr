<?php

namespace App\Utilities;

// phpcs:disable
if (! defined('ABSPATH')) {
    exit;
}
// phpcs:enable

class AttributeFactory
{

    /**
     * List of all classes
     * @author Adam Alexandersson
     * @since 1.0.0
     */
    private $attributes = [];

    /**
     * List of all classes ( combined string )
     * @author Adam Alexandersson
     * @since 1.0.0
     */
    private $attributes_string;

    public function __construct()
    {
    }

    public function add($attr = '', $value = '')
    {
        if ($attr !== '' && $value !== '') {
            if (! isset($this->attributes[ $attr ])) {
                $this->attributes[ $attr ] = [];
            }

            // Escape JSON
            if (is_array($value) || is_object($value)) {
                $value = htmlspecialchars(json_encode($value), ENT_QUOTES, 'UTF-8');
            }

            $this->attributes[ $attr ] = $value;

            $this->join();
        }
    }

    public function data($attr = '', $value = '')
    {
        if ($attr !== '' && $value !== '') {
            $attr = 'data-' . $attr;

            if (! isset($this->attributes[ $attr ])) {
                $this->attributes[ $attr ] = [];
            }

            // Escape JSON
            if (is_array($value) || is_object($value)) {
                $value = htmlspecialchars(json_encode($value), ENT_QUOTES, 'UTF-8');
            }

            $this->attributes[ $attr ] = $value;

            $this->join();
        }
    }

    public function remove($attr = false)
    {
        if ($attr && $attr != '' && isset($this->attributes[ $attr ])) {
            unset($this->attributes[ $attr ]);
        }

        $this->join();
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function get()
    {
        return $this->attributes_string;
    }

    private function join()
    {
        ksort($this->attributes);

        // Convert all attributes to string
        $this->attributes_string = join(' ', array_map(function ($key) {
            if (is_bool($this->attributes[ $key ])) {
                return $this->attributes[ $key ] ? $key : '';
            }

            return $key . '="' . $this->attributes[ $key ] . '"';
        }, array_keys($this->attributes)));
    }
}
