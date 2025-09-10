<?php

namespace App\Core;

use App\Utilities\Dir;

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

        add_action('switch_theme', function () {
            delete_option('theme_migrations_ran');
        });

        add_action('init', [$this, 'loadPostTypes']);
        add_action('init', [$this, 'loadFieldGroups']);
    }

    /**
     * Load meta boxes
     */
    public function loadFieldGroups()
    {
        $dir = __DIR__ . '/FieldGroups';
        $field_groups = Dir::list($dir, 'files');
        $namespace = 'App\\Core\\FieldGroups\\';

        if (!empty($field_groups)) {
            foreach ($field_groups as $box) {
                $field_group = $namespace . basename($box, '.php');

                if (class_exists($field_group)) {
                    new $field_group();
                }
            }
        }
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
}
