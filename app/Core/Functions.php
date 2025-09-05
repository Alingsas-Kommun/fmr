<?php

namespace App\Core;

/**
 * Helper function to retrieve necessary localized data for theme.
 * @return array
 */
function getLocalizedData()
{
    $localized = [];

    $localized['ajaxUrl'] = admin_url('admin-ajax.php');
    $localized['isLoggedIn'] = is_user_logged_in();

    return apply_filters('fmr/core/localized', $localized);
}

/**
 * Helper function to splice associative array
 */

function arraySpliceAssoc(&$input, $offset, $length, $replacement = [])
{
    $replacement = (array) $replacement;
    $key_indices = array_flip(array_keys($input));

    if (isset($input[$offset]) && is_string($offset)) {
        $offset = $key_indices[ $offset ];
    }

    if (isset($input[$length]) && is_string($length)) {
        $length = $key_indices[ $length ] - $offset;
    }

    $input = array_slice($input, 0, $offset, true) + $replacement + array_slice($input, $offset + $length, null, true);
}

/**
 * Helper function to replace array keys
 */
function replaceArrayKey($array, $oldKey, $newKey)
{
    if (!isset($array[$oldKey])) {
        return $array;
    }

    $arrayKeys = array_keys($array);

    $oldKeyIndex = array_search($oldKey, $arrayKeys);
    $arrayKeys[$oldKeyIndex] = $newKey;

    $newArray =  array_combine($arrayKeys, $array);

    return $newArray;
}

/**
 * Custom version of is_iterable!
 *
 * Also checks wether the iterable variable is empty or not!
 *
 * @param iterable $var
 *
 * @return bool
 */
function is_iterable($var)
{
    return (\is_iterable($var) && !empty($var));
}

/**
 * Quick helper function to get the image src from an wordpress attachment id
 *
 * @since 1.0.0
 * @return bool
 */

function getImageSrcFromId($imageId, $imageSize = 'large')
{
    $imageSrc = '';

    if ($imageId) {
        $imageSrc = wp_get_attachment_image_src($imageId, $imageSize);

        if (is_array($imageSrc)) {
            $imageSrc = reset($imageSrc);
        }
    }

    return $imageSrc;
}

/**
 * Quick helper function to get the image element from an wordpress attachment id
 *
 * @since 1.0.0
 * @return string
 */

function getImageElement($imageId, $imageSize = 'large', $class = '')
{
    $image = '';

    if ($imageId) {
        $image = wp_get_attachment_image($imageId, $imageSize, false, ['class' => $class]);
    }

    return $image;
}
