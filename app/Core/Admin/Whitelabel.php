<?php

namespace App\Core\Admin;

use Illuminate\Support\Facades\Vite;

class Whitelabel
{
    public function __construct()
    {
        if (! function_exists('add_action')) {
            return;
        }

        /**
         * Register custom admin colors
         */
        add_action('init', [$this, 'registerAdminColors']);

        /**
         * Enqueue custom login assets
         */
        add_action('login_enqueue_scripts', [$this, 'enqueueAdminAssets'], 0);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets'], 0);

        // Set the color profile to only use the custom one
        add_action('user_edit_form_tag', [$this, 'removeSchemePicker']);
        add_filter('get_user_option_admin_color', [$this, 'userAdminColor'], 5);
    }

    /**
     * Register custom admin colors
     *
     */
    public static function registerAdminColors():void
    {
        // Skip asset loading in CLI context
        if (php_sapi_name() === 'cli') {
            return;
        }
        
        wp_admin_css_color(
            'fmr',
            __('fmr'),
            Vite::asset('resources/css/_admin/color-profiles/fmr/fmr.scss'),
            [
                '#fab526',
                '#bd2b30',
                '#236151'
            ]
        );
    }

    /**
     * Change the admin colors to the new custom one
     */
    public static function userAdminColor($color_scheme):string
    {
        $color_scheme = 'fmr';

        return $color_scheme;
    }

    /**
     * Remove color scheme picker
     */
    public static function removeSchemePicker():void
    {
        remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
    }

    /**
     * Enqueue JavaScript and CSS assets for admin login screen.
     *
     * @return void
     */
    public function enqueueAdminAssets():void
    {
        // Skip asset loading in CLI context
        if (php_sapi_name() === 'cli') {
            return;
        }

        $style = Vite::asset('resources/css/admin.scss');
        wp_enqueue_style('admin-css', $style, false, '');

        wp_enqueue_script('alpine-safe', 'https://unpkg.com/alpinejs@3.15.0/dist/cdn.min.js', [], null, true);

        // Add the noConflict wrapper to avoid conflicts with WP Underscore library
        wp_add_inline_script(
            'alpine-safe',
            <<<JS
            (function() {
                var old_ = window._; // save WP's Underscore
                delete window._;     // remove temporarily

                // Start Alpine after load
                document.addEventListener('alpine:init', function() {
                    if (old_) window._ = old_; // restore Underscore
                });

                if (window.Alpine) {
                    window.Alpine.start();
                }
            })();
            JS
        );
    }
}
