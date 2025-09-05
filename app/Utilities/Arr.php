<?php

namespace App\Utilities;

class Arr
{
    public static function search($key, $value, $array)
    {
        $results = [];

        if (is_array($array)) {
            if (isset($array[ $key ]) && $array[ $key ] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, self::search($key, $value, $subarray));
            }
        }

        return $results;
    }

    /**
     * Check wether list if iterable and not empty.
     *
     * @param $var
     *
     * @return bool
     */
    public static function iterable($var)
    {
        return (\is_iterable($var) && !empty($var));
    }

    public static function findParentKey($array, $needle, $parent = null)
    {
        foreach ($array as $key => $value) {
            if (! is_array($value)) {
                $pass = $parent;

                if (is_string($key)) {
                    $pass = $key;
                }

                $found = self::findParentKey($value, $needle, $pass);

                if ($found !== false) {
                    return $found;
                }
            } elseif ($key === 'id' && $value === $needle) {
                return $parent;
            }
        }

        return false;
    }

    /**
     * Parse array, all string values that are "true" or "false" will be returned as booleans.
     *
     * @param array &$array
     *
     * @return void
     */
    public static function parseBool(&$array)
    {
        if (! is_array($array)) {
            return;
        }

        array_walk_recursive($array, function (&$a) {
            if ($a === "true") {
                $a = true;
            } elseif ($a === "false") {
                $a = false;
            }
        });
    }
}
