<?php

namespace App\Core\Admin;

// phpcs:disable
if (! defined('ABSPATH')) {
    exit;
}
// phpcs:enable

class Tinymce
{
    public function __construct()
    {
        add_filter('mce_buttons', [$this, 'tinymceFirstRow']);
        add_filter('mce_buttons_2', [$this, 'tinymceSecondRow']);

        /**
         * Remove all plugins from tinyMCE.
         */
        add_filter('tiny_mce_plugins', function ($plugins) {
            $plugins[] = 'wordpress';
            return $plugins;
        });
    }

    /**
     * Filter buttons from the first row of the tiny mce editor
     * @param    array    $buttons    The default array of buttons in the kitchen sink
     * @return   array                The updated array of buttons that exludes some items
     */

    public function tinymceFirstRow($buttons)
    {
        $remove_buttons = array(
            'alignleft',
            'wp_more', // read more link
            'spellchecker',
            'dfw', // distraction free writing mode
        );
        foreach ($buttons as $button_key => $button_value) {
            if (in_array($button_value, $remove_buttons)) {
                unset($buttons[ $button_key ]);
            }
        }
        return $buttons;
    }

    /**
     * Filter buttons from the second row of the tiny mce editor
     * @param    array    $buttons    The default array of buttons in the kitchen sink
     * @return   array                The updated array of buttons that exludes some items
     */

    public function tinymceSecondRow($buttons)
    {
        $remove_buttons = array(
            'formatselect', // format dropdown menu for <p>, headings, etc
            'alignjustify',
            'forecolor', // text color
        );
        foreach ($buttons as $button_key => $button_value) {
            if (in_array($button_value, $remove_buttons)) {
                unset($buttons[ $button_key ]);
            }
        }
        return $buttons;
    }
}
