<?php

namespace App\Core\Admin;

use Illuminate\Support\Facades\Vite;

use function App\Core\setting;

class Assets
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
        add_action('login_enqueue_scripts', [$this, 'enqueueLoginAssets'], 0);

        /**
         * Enqueue dynamic login logo
         */
        add_action('login_enqueue_scripts', [$this, 'enqueueLoginLogo'], 10);

        /**
         * Enqueue admin assets with CSS variables
         */
        add_action('login_enqueue_scripts', [$this, 'enqueueColorVariables']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueColorVariables']);

        // Set the color profile to only use the custom one
        add_action('user_edit_form_tag', [$this, 'removeSchemePicker']);
        add_filter('get_user_option_admin_color', [$this, 'userAdminColor'], 5);
    }

    /**
     * Register custom admin colors
     * 
     * @return void
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
     * 
     * @param string $color_scheme
     * @return string
     */
    public static function userAdminColor($color_scheme):string
    {
        $color_scheme = 'fmr';

        return $color_scheme;
    }

    /**
     * Remove color scheme picker
     *
     * @return void
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
    public function enqueueLoginAssets():void
    {
        // Skip asset loading in CLI context
        if (php_sapi_name() === 'cli') {
            return;
        }

        $style = Vite::asset('resources/css/admin.scss');
        wp_enqueue_style('admin-css', $style, false, '');
    }

    /**
     * Enqueue admin assets.
     * 
     * @param string|null $hook_suffix
     * @return void
     */
    public function enqueueAdminAssets($hook_suffix = null): void
    {
        // Skip asset loading in CLI context
        if (php_sapi_name() === 'cli') {
            return;
        }

        // Don't enqueue on taxonomy pages
        $excluded_pages = ['term.php', 'edit-tags.php'];
        if ($hook_suffix && in_array($hook_suffix, $excluded_pages)) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('admin-css', Vite::asset('resources/css/admin.scss'), false, '');
        wp_enqueue_script(
            'admin-js', 
            Vite::asset('resources/js/admin.js'), 
            ['jquery', 'wp-util'], 
            null, 
            [
                'strategy'  => 'defer',
                'in_footer' => true,
            ]
        );
    }

    /**
     * Enqueue admin assets with CSS variables for admin area and login pages.
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
        
        // Determine if we're on login page or admin page
        if (is_admin()) {
            wp_add_inline_style('wp-admin', $css_output);
        } else {
            wp_add_inline_style('admin-css', $css_output);
        }
    }

    /**
     * Enqueue dynamic logo CSS for login screen.
     *
     * @return void
     */
    public function enqueueLoginLogo():void
    {
        // Skip asset loading in CLI context
        if (php_sapi_name() === 'cli') {
            return;
        }

        // Get logotype from settings
        $logotype_id = setting('logotype_default');
        
        if (!$logotype_id) {
            return;
        }

        // Get image data
        $image_data = wp_get_attachment_image_src($logotype_id, 'full');
        
        if (!$image_data) {
            return;
        }

        $image_url = $image_data[0];
        $image_width = $image_data[1];
        $image_height = $image_data[2];

        // Calculate aspect ratio and dimensions
        $aspect_ratio = $image_width / $image_height;
        $max_height = 70; // Keep the same height as before
        $calculated_width = $max_height * $aspect_ratio;

        // Build CSS for login logo
        $css = sprintf(
            'body.login #login h1 a,
            body.login .login h1 a {
                background-image: url("%s") !important;
                width: %spx !important;
                height: %spx !important;
            }',
            esc_url($image_url),
            esc_attr($calculated_width),
            esc_attr($max_height)
        );

        wp_add_inline_style('admin-css', $css);
    }
}
