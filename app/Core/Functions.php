<?php

namespace App\Core;

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

/**
 * Check if a class is a valid Tailwind CSS class using regex.
 *
 * @param string $class
 * @return bool
 */
function isTailwindClass($class)
{
    // Tailwind CSS class patterns
    $patterns = [
        // Layout: flex, grid, block, inline, etc.
        '/^(flex|grid|block|inline|hidden|table|table-cell|table-row|flow-root|contents|list-item|truncate|ellipsis|clip|visible|invisible|static|fixed|absolute|relative|sticky)(-\w+)*$/',
        
        // Flexbox & Grid: flex-*, grid-*, gap-*, etc.
        '/^(flex|grid|gap|place|justify|items|content|self|order|flex-grow|flex-shrink|flex-basis|grid-cols|grid-rows|col|row|auto-cols|auto-rows|grid-flow|grid-template|grid-area)(-\w+)*$/',
        
        // Spacing: p-*, m-*, space-*, etc.
        '/^(p|m|space|divide)(-[xytrbl])?(-\d+|-\w+)*$/',
        
        // Sizing: w-*, h-*, min-*, max-*, etc.
        '/^(w|h|min-w|min-h|max-w|max-h)(-\d+|-\w+)*$/',
        
        // Typography: text-*, font-*, leading-*, etc.
        '/^(text|font|leading|tracking|list|placeholder|align|whitespace|break|hyphens|content)(-\w+)*$/',
        
        // Colors: bg-*, text-*, border-*, etc.
        '/^(bg|text|border|ring|shadow|outline|decoration|accent|caret|fill|stroke|from|via|to)(-\w+)*$/',
        
        // Borders: border-*, rounded-*, etc.
        '/^(border|rounded|outline)(-\w+)*$/',
        
        // Effects: shadow-*, blur-*, etc.
        '/^(shadow|blur|brightness|contrast|drop-shadow|grayscale|hue-rotate|invert|saturate|sepia|backdrop)(-\w+)*$/',
        
        // Transforms: transform, scale-*, rotate-*, etc.
        '/^(transform|scale|rotate|translate|skew|origin)(-\w+)*$/',
        
        // Transitions: transition-*, duration-*, etc.
        '/^(transition|duration|ease|delay)(-\w+)*$/',
        
        // Interactivity: hover-*, focus-*, active-*, etc.
        '/^(hover|focus|active|visited|disabled|group-hover|group-focus|group-active|group-disabled|motion-safe|motion-reduce|dark|light)(-\w+)*$/',
        
        // Responsive prefixes: sm:, md:, lg:, xl:, 2xl:
        '/^(sm|md|lg|xl|2xl):(\w+(-\w+)*)$/',
        
        // Arbitrary values: [value]
        '/^\[[^\]]+\]$/',
        
        // Dark mode prefix
        '/^dark:(\w+(-\w+)*)$/',
        
        // Print media query
        '/^print:(\w+(-\w+)*)$/',
        
        // Reduced motion
        '/^motion-(safe|reduce):(\w+(-\w+)*)$/',
        
        // Container queries
        '/^@(\w+):(\w+(-\w+)*)$/',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $class)) {
            return true;
        }
    }

    return false;
}
