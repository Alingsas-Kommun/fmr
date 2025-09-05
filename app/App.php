<?php

namespace App;

use App\Core\Theme;
use App\Core\Filters;
use App\Core\Assets\Assets;
use App\Core\Admin\Whitelabel;
use App\Core\Admin\Tinymce;
use App\Core\Admin\Init as Admin;

class App // phpcs:ignore
{
    public function __construct()
    {
        if (! function_exists('get_bloginfo')) {
            return;
        }

        $this->systemRequirement();
        $this->initCore();
    }

    public function systemRequirement()
    {
        /**
         * Ensure compatible version of PHP is used
         */
        if (version_compare('8.2', phpversion(), '>=') === -1) {
            $this->error(
                __('You must be using PHP 8.2 or greater.', 'fmr'),
                __('Invalid PHP version', 'fmr')
            );
        }

        /**
         * Ensure compatible version of WordPress is used
         */
        if (version_compare('6.8.0', \get_bloginfo('version'), '>=') === -1) {
            $this->error(
                __('You must be using WordPress 6.8.0 or greater.', 'fmr'),
                __('Invalid WordPress version', 'fmr')
            );
        }
    }

    /**
     * Initializing of theme core classes
     */

    public function initCore()
    {
        /**
         * Not all classes has to be after_setup_theme
         */

        add_action('after_setup_theme', function () {
            if (class_exists('App\\Core\\Theme')) {
                new Theme();
            } else {
                $this->error(__('Theme setup class is missing', 'fmr'));
            }

            if (class_exists('App\\Core\\Filters')) {
                new Filters();
            } else {
                $this->error(__('Filter setup class is missing', 'fmr'));
            }

            if (class_exists('App\\Core\\Admin\\Tinymce')) {
                new Tinymce();
            }

            if (class_exists('App\\Core\\Admin\\Whitelabel')) {
                new Whitelabel();
            }

            if (class_exists('App\\Core\\Admin\\Init')) {
                new Admin();
            } else {
                $this->error(__('Admin init class is missing', 'fmr'));
            }

            if (class_exists('App\\Core\\Assets\\Assets')) {
                new Assets();
            } else {
                $this->error(__('Assets class is missing', 'fmr'));
            }
        }, 0);
    }

    /**
     * Helper function for prettying up errors
     * @param string $message
     * @param string $subtitle
     * @param string $title
     */
    public function error($message, $subtitle = '', $title = '')
    {
        $title = $title ?: __('App &rsaquo; Error', 'fmr');
        $message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p>";
        wp_die($message);
    }
}

new App(); // phpcs:ignore