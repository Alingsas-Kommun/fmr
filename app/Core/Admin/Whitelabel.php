<?php

namespace App\Core\Admin;

use Illuminate\Support\Facades\Vite;

use function App\Core\setting;

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

        /**
         * Enqueue admin assets with CSS variables
         */
        add_action('login_enqueue_scripts', [$this, 'enqueueColorVariables']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueColorVariables']);

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

        // Get colors from settings
        $primary_color = setting('primary_color', '#fab526');
        $secondary_color = setting('secondary_color', '#bd2b30');
        $tertiary_color = setting('tertiary_color', '#236151');
        
        wp_admin_css_color(
            'fmr',
            __('fmr'),
            Vite::asset('resources/css/_admin/color-profiles/fmr/fmr.scss'),
            [
                $secondary_color,
                $tertiary_color,
                $primary_color
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
    }

    /**
     * Enqueue admin assets with CSS variables for admin area.
     *
     * @return void
     */
    public function enqueueColorVariables():void
    {
        // Skip asset loading in CLI context
        if (php_sapi_name() === 'cli') {
            return;
        }

        // Get colors from settings
        $primary_color = setting('primary_color', '#fab526');
        $secondary_color = setting('secondary_color', '#bd2b30');
        $tertiary_color = setting('tertiary_color', '#236151');

        // Build CSS variables
        $css_vars = array();
        array_push($css_vars, sprintf(
            '--wp-admin-color-primary: %s',
            esc_attr($primary_color)
        ));
        
        array_push($css_vars, sprintf(
            '--wp-admin-color-secondary: %s',
            esc_attr($secondary_color)
        ));

        array_push($css_vars, sprintf(
            '--wp-admin-color-tertiary: %s',
            esc_attr($tertiary_color)
        ));

        $css_output = ':root {' . implode(';', array_map('esc_html', $css_vars)) . '}';
        wp_add_inline_style('wp-admin', $css_output);
    }
}
