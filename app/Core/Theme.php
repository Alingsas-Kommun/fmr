<?php

namespace App\Core;

use App\Utilities\Dir;
use ReflectionMethod;

class Theme
{
    public function __construct()
    {
        if (!function_exists('add_action')) {
            return;
        }

        include_once 'Functions.php';

        add_action('init', [$this, 'loadTextDomain']);
        add_action('after_setup_theme', [$this, 'addThemeSupport'], 20);
        add_action('after_setup_theme', [$this, 'removeThemeSupport'], 20);

        add_action('init', [$this, 'loadPostTypes']);
        add_action('init', [$this, 'loadTaxonomies']);
    }

    /**
     * Them localization
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
    }

    /**
     * Load post types
     */
    public function loadPostTypes()
    {
        $dir = __DIR__ . '/PostTypes';
        $post_types = Dir::list($dir, 'files');
        $namespace = 'App\\Core\\PostTypes\\';

        if (! empty($post_types)) {
            foreach ($post_types as $type) {
                $post_type = $namespace . basename($type, '.php');

                if (class_exists($post_type)) {
                    new $post_type();
                }
            }
        }
    }

    /**
     * Load taxonomies
     */
    public function loadTaxonomies()
    {
        $dir = __DIR__ . '/Taxonomies';
        $taxonomies = Dir::list($dir, 'files');
        $namespace = 'App\\Core\\Taxonomies\\';

        if (! empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                $taxonomy = $namespace . basename($taxonomy, '.php');

                if (class_exists($taxonomy)) {                    
                    new $taxonomy();
                }
            }
        }
    }
}
