<?php

namespace App\Core\Admin;

use Illuminate\Support\Facades\Vite;
use App\Core\Admin\Assignments\Index as AssignmentsIndex;
use App\Core\Admin\Assignments\Edit as AssignmentsEdit;
use App\Core\Admin\DecisionAuthorities\Index as DecisionAuthoritiesIndex;
use App\Core\Admin\DecisionAuthorities\Edit as DecisionAuthoritiesEdit;
use App\Core\Admin\Cleanup;
use App\Utilities\Dir;

class Init
{
    public function __construct()
    {
        if (!function_exists('add_action')) {
            return;
        }

        if (class_exists('App\\Core\\Admin\\Assignments\\Index')) {
            add_action('admin_menu', function () {
                AssignmentsIndex::register();
            });
        }

        if (class_exists('App\\Core\\Admin\\Assignments\\Edit')) {
            new AssignmentsEdit();
        }

        if (class_exists('App\\Core\\Admin\\DecisionAuthorities\\Index')) {
            add_action('admin_menu', function () {
                DecisionAuthoritiesIndex::register();
            });
        }

        if (class_exists('App\\Core\\Admin\\DecisionAuthorities\\Edit')) {
            new DecisionAuthoritiesEdit();
        }

        if (class_exists('App\\Core\\Admin\\Whitelabel')) {
            new Whitelabel();
        }

        /**
         * Initialize admin cleanup
         */
        new cleanup();

        /**
         * Load field groups
         */
        add_action('init', [$this, 'loadFieldGroups']);
        
        /**
         * Register relation handlers
         */
        add_action('init', [$this, 'registerRelationHandlers']);

        /**
         * Enqueue admin assets
         */
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    /**
     * Load meta boxes
     */
    public function loadFieldGroups()
    {
        $dir = __DIR__ . '/FieldGroups';
        $field_groups = Dir::list($dir, 'files');
        $namespace = 'App\\Core\\Admin\\FieldGroups\\';

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
     * Load relation handlers for post types
     */
    public function registerRelationHandlers()
    {
        $dir = __DIR__ . '/RelationHandlers';
        $relation_handlers = Dir::list($dir, 'files');
        $namespace = 'App\\Core\\Admin\\RelationHandlers\\';

        if (!empty($relation_handlers)) {
            foreach ($relation_handlers as $handler) {
                $handler_class = $namespace . basename($handler, '.php');

                if (class_exists($handler_class)) {
                    new $handler_class();
                }
            }
        }
    }

    public function enqueueAdminAssets()
    {
        // Skip asset loading in CLI context
        if (php_sapi_name() === 'cli') {
            return;
        }

        $style = Vite::asset('resources/css/admin.scss');
        wp_enqueue_style('admin-css', $style, false, '');

        $script = Vite::asset('resources/js/admin.js');
        wp_enqueue_script('admin-js', $script, [], null, true);

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
