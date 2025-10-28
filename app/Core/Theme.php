<?php

namespace App\Core;

use App\Utilities\Color;

use function App\Core\setting;
use Illuminate\Support\Facades\Vite;

class Theme
{
    public function __construct()
    {
        if (!function_exists('add_action')) {
            return;
        }

        add_action('init', [$this, 'loadTextDomain']);
        add_action('after_setup_theme', [$this, 'addThemeSupport'], 20);
        add_action('after_setup_theme', [$this, 'removeThemeSupport'], 20);
        add_action('wp_enqueue_scripts', [$this, 'themeAssets'], 100);
        add_action('wp_enqueue_scripts', [$this, 'dequeueWordPressStyles'], 100);
        add_action('wp_head', [$this, 'enqueueFrontendColorVariables']);
    }

    /**
     * Theme localization
     * @link https://roots.io/sage/docs/theme-localization/
     */
    public function loadTextDomain()
    {
        load_theme_textdomain('fmr', get_stylesheet_directory() . '/resources/lang');
    }

    public function addThemeSupport()
    {
        /**
         * Enable plugins to manage the document title.
         * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
         */
        add_theme_support('title-tag');

        /**
         * Enable post thumbnail support.
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');

        /**
         * Enable wide alignment support.
         * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#wide-alignment
         */
        add_theme_support('align-wide');

        /**
         * Enable responsive embed support.
         * @link https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/#responsive-embedded-content
         */
        add_theme_support('responsive-embeds');

        /**
         * Enable HTML5 markup support.
         * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
         */
        add_theme_support('html5', [
            'caption',
            'comment-form',
            'comment-list',
            'gallery',
            'search-form',
            'script',
            'style'
        ]);
    }

    public function removeThemeSupport()
    {
        /**
         * Disable full-site editing support.
         *
         * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
         */
        remove_theme_support('block-templates');

        /**
         * Disable the default block patterns.
         *
         * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
         */
        remove_theme_support('core-block-patterns');
        
        /**
         * Disable WordPress global styles and CSS custom properties.
         * This prevents WordPress from adding CSS variables from theme.json
         */
        remove_theme_support('wp-block-styles');
        
        /**
         * Disable editor styles (prevents WordPress from adding editor-specific CSS)
         */
        remove_editor_styles();
    }

    /**
     * Register the theme assets.
     *
     * @return void
     */

     public function themeAssets()
     {        
        wp_enqueue_script('app-js', Vite::asset('resources/js/app.js'), ['wp-i18n'], null, true);
        wp_set_script_translations('app-js', 'fmr', get_stylesheet_directory() . '/resources/lang/');
     }

    /**
     * Dequeue WordPress default styles and remove inline styles.
     *
     * @return void
     */
    public function dequeueWordPressStyles()
    {
        wp_dequeue_style('global-styles');
    }

    /**
     * Enqueue frontend assets with CSS variables for primary hue.
     *
     * @return void
     */
    public function enqueueFrontendColorVariables():void
    {
        // Skip asset loading in CLI context
        if (php_sapi_name() === 'cli') {
            return;
        }

        // Get primary color from settings
        $primary_color = setting('primary_color', '#236151');
        $secondary_color = setting('secondary_color', '#bd2b30');
        $tertiary_color = setting('tertiary_color', '#fab526');

        // Calculate lightness values from the colors
        $primary_lightness = Color::hexToLightness($primary_color);
        $secondary_lightness = Color::hexToLightness($secondary_color);
        $tertiary_lightness = Color::hexToLightness($tertiary_color);

        // Convert colors to hue and chroma for frontend
        $primary_hue = Color::hexToHue($primary_color);
        $secondary_hue = Color::hexToHue($secondary_color);
        $tertiary_hue = Color::hexToHue($tertiary_color);
        
        // Calculate chroma values from the colors
        $primary_chroma = Color::hexToChroma($primary_color);
        $secondary_chroma = Color::hexToChroma($secondary_color);
        $tertiary_chroma = Color::hexToChroma($tertiary_color);

        // Build CSS variables for frontend
        $css_vars = array();
        array_push($css_vars, sprintf(
            '--primary-lightness: %s',
            esc_attr($primary_lightness). ' !important'
        ));
        array_push($css_vars, sprintf(
            '--secondary-lightness: %s',
            esc_attr($secondary_lightness). ' !important'
        ));
        array_push($css_vars, sprintf(
            '--tertiary-lightness: %s',
            esc_attr($tertiary_lightness). ' !important'
        ));
        
        array_push($css_vars, sprintf(
            '--primary-hue: %s',
            esc_attr($primary_hue). ' !important'
        ));
        array_push($css_vars, sprintf(
            '--primary-chroma: %s',
            esc_attr($primary_chroma). ' !important'
        ));

        array_push($css_vars, sprintf(
            '--secondary-hue: %s',
            esc_attr($secondary_hue). ' !important'
        ));
        array_push($css_vars, sprintf(
            '--secondary-chroma: %s',
            esc_attr($secondary_chroma). ' !important'
        ));
        
        array_push($css_vars, sprintf(
            '--tertiary-hue: %s',
            esc_attr($tertiary_hue). ' !important'
        ));
        array_push($css_vars, sprintf(
            '--tertiary-chroma: %s',
            esc_attr($tertiary_chroma). ' !important'
        ));

        $css_output = ':root {' . implode(';', array_map('esc_html', $css_vars)) . '}';
        echo '<style id="fmr-color-hues">' . $css_output . '</style>';
    }
}
