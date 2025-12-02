<?php

namespace App\Core;

use App\Utilities\AttributeFactory;

class Filters
{
    public function __construct()
    {
        if (! function_exists('add_action')) {
            return;
        }

        do_action('before_setup_theme_filters');

        add_action('body_class', [$this, 'filterBodyClasses'], PHP_INT_MAX, 1);
        add_action('excerpt_more', [$this, 'filterReadMore'], 10);
        add_filter('get_the_archive_title', [$this, 'removeArchivePrefix']);
        add_filter('theme_file_path', [$this, 'themeFilePath'], 10, 2);
        add_action('admin_menu', [$this, 'removeSubmenu'], 999);
        add_filter('script_loader_tag', [$this, 'addTypeModuleAttribute'], 10, 2);
        add_filter('script_loader_tag', [$this, 'cleanScriptTagOutput'], 999, 2);
        add_filter('style_loader_tag', [$this, 'cleanStyleTagOutput'], 999, 2);

        do_action('after_setup_theme_filters');
    }

    /**
     * Filter body attributes.
     *
     * @param $classes
     *
     * @return array|mixed
     */
    public function filterBodyAttributes($attr)
    {
        $attributes = new AttributeFactory();

        if ($attr) {
            foreach ($attr as $key => $att) {
                $attributes->add($key, $att);
            }
        }

        return $attributes->get();
    }

    /**
     * Filter body classes.
     *
     * @param $classes
     *
     * @return array|mixed
     */
    public function filterBodyClasses($classes)
    {
        if (wp_doing_ajax()) {
            return $classes;
        }

        $allowed = [
            'home',
            'page',
            'single',
            'archive',
            'blog',
            'device-supports-hover',
        ];

        $classes = array_map('trim', array_map(function ($class) use ($allowed) {
            if (in_array($class, $allowed)) {
                return $class;
            }

            if (isTailwindClass($class)) {
                return $class;
            }

            return false;
        }, $classes));

        return array_filter(array_unique($classes));
    }

    /**
     * Add "read more" link to blog posts and so on, excerpts.
     *
     * @return string
     */

    public function filterReadMore()
    {
        return '&hellip;';
    }

    /*
     * Simply remove anything that looks like an archive title prefix ("Archive:", "Category:", "Taxonomy:").
     */
    public function removeArchivePrefix($title)
    {
        return preg_replace('/^\w+: /', '', $title);
    }

    /**
     * Use the generated theme.json file.
     *
     * @return string
     */

    public function themeFilePath($path, $file)
    {
        return $file === 'theme.json'
            ? public_path('build/assets/theme.json')
            : $path;
    }

    /**
     * Remove submenu page.
     *
     * @return void
     */
    public function removeSubmenu()
    {
        remove_submenu_page('options-general.php', 'to-options');
    }

    /**
     * Add type="module" attribute to app and admin scripts.
     *
     * @param string $tag
     * @param string $handle
     *
     * @return string
     */
    public function addTypeModuleAttribute($tag, $handle)
    {
        if ($handle === 'app-js' || $handle === 'admin-js') {
            return str_replace(' src', ' type="module" src', $tag);
        }
        
        return $tag;
    }

    /**
     * Clean corrupted HTML comments from script tags (FreeBSD encoding issue).
     * WordPress may add conditional comments that get corrupted on FreeBSD.
     *
     * @param string $tag The complete script tag HTML
     * @param string $handle The script handle
     * @return string Cleaned script tag
     */
    public function cleanScriptTagOutput(string $tag, string $handle): string
    {
        // Remove corrupted HTML comment patterns
        $tag = preg_replace('/\d+"?-->/', '', $tag);
        $tag = preg_replace('/<!--\[if[^\]]*\]>.*?<!\[endif\]-->/s', '', $tag);
        $tag = preg_replace('/<!--.*?-->/s', '', $tag);
        
        return $tag;
    }

    /**
     * Clean corrupted HTML comments from style tags (FreeBSD encoding issue).
     *
     * @param string $tag The complete style tag HTML
     * @param string $handle The style handle
     * @return string Cleaned style tag
     */
    public function cleanStyleTagOutput(string $tag, string $handle): string
    {
        // Remove corrupted HTML comment patterns
        $tag = preg_replace('/\d+"?-->/', '', $tag);
        $tag = preg_replace('/<!--\[if[^\]]*\]>.*?<!\[endif\]-->/s', '', $tag);
        $tag = preg_replace('/<!--.*?-->/s', '', $tag);
        
        return $tag;
    }
}
