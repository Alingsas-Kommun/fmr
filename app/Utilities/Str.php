<?php

namespace App\Utilities;

class Str
{
    /**
     * Simple function to convert a slug like a-random-slug to A Random Slug.
     *
     * @param string $word
     * @return string
     */
    public static function wordify($word)
    {
        return ucwords(str_replace(['-', '_'], ' ', $word));
    }

    /**
     * Check wether string starts with a specific word/string.
     *
     * @param string $str
     * @param string $match
     *
     * @return bool
     */
    public static function lmatch($str, $match = '')
    {
        if ((!is_string($match) || !is_string($str)) || ($match === '' || $str === '')) {
            return false;
        }

        return substr($str, 0, strlen($match)) === $match;
    }

    /**
     * Alias for self::lmatch();
     *
     * @param string $str
     * @param string $match
     *
     * @return bool
     */
    public static function startsWith($str, $match = '')
    {
        return self::lmatch($str, $match);
    }

    /**
     * Check wether string ends with a specific word/string.
     *
     * @param string $str
     * @param string $match
     *
     * @return bool
     */
    public static function rmatch($str, $match = '')
    {
        if ((!is_string($match) || !is_string($str)) || ($match === '' || $str === '')) {
            return false;
        }

        return substr($str, strlen($str) - strlen($match), strlen($str)) === $match;
    }

    /**
     * Alias for self::rmatch();
     *
     * @param string $str
     * @param string $match
     *
     * @return bool
     */
    public static function endsWith($str, $match = '')
    {
        return self::rmatch($str, $match);
    }

    /**
     * Helper function to genereate a unique alphanumeric string.
     *
     * @param string $limit
     * @return string
     */
    public static function uniqueId($limit = 10)
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }

     /**
     * Helper function to find and replace last occurence of a word in string.
      *
     * @return string
     */
    public static function lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);

        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }

    /**
     * Reverse string
     *
     * @param $string
     *
     * @return string
     */
    public static function reverse($string)
    {
        return join('', array_reverse(mb_str_split($string)));
    }

    /**
     * Check if string contains another string
     *
     * @param string $needle
     * @param string $haystack
     *
     * @return bool
     */
     public static function contains($needle, $haystack)
    {
        foreach ((array) $needle as $ndl) {
            if (strpos($haystack, $ndl) !== false) {
                return true;
            }
        }

        return false;
    }
}
