<?php

namespace App\Core;

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
}
