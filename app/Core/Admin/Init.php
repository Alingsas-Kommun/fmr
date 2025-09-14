<?php

namespace App\Core\Admin;

use Illuminate\Support\Facades\Vite;
use App\Utilities\Dir;

class Init
{
    public function __construct()
    {
        if (!function_exists('add_action')) {
            return;
        }

        if (class_exists('App\\Core\\Admin\\Whitelabel')) {
            new Whitelabel();
        }

        if (class_exists('App\\Core\\Admin\\Assignments')) {
            add_action('admin_menu', function () {
                Assignments::register();
            });
        }

        if (class_exists('App\\Core\\Admin\\AssignmentHandler')) {
            new AssignmentHandler();
        }

        if (class_exists('App\\Core\\Admin\\DecisionAuthorities')) {
            add_action('admin_menu', function () {
                DecisionAuthorities::register();
            });
        }

        if (class_exists('App\\Core\\Admin\\DecisionAuthorityHandler')) {
            new DecisionAuthorityHandler();
        }
        
        /**
         * Register relation handlers
         */
        add_action('init', [$this, 'registerRelationHandlers']);

        /**
         * Modify admin menu
         */
        add_action('admin_menu', function () {
            remove_menu_page('index.php');  // Remove Dashboard
            remove_menu_page('edit.php');   // Remove Posts
            remove_menu_page('edit.php?post_type=page'); // Remove Pages

            // Modify Media menu
            global $menu;
            foreach ($menu as $key => $item) {
                if ($item[2] === 'upload.php') {
                    $menu[$key][0] = __('Images', 'fmr');
                    $menu[$key][6] = 'dashicons-format-image';
                }
            }
        });

        /**
         * Disable heartbeat in admin.
         */
        add_action('init', function () {
            wp_deregister_script('heartbeat');
        });
        
        /**
         * Enqueue admin assets
         */
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);

        /**
         * Redirect to person insead of dashboard when visiting index or login
         */
        if (!is_network_admin()) {
            $user = wp_get_current_user();
            $allowed_roles = array('editor', 'administrator', 'author');
            
            if (array_intersect($allowed_roles, $user->roles)) :
                add_action('load-index.php', function () {
                    wp_redirect(admin_url('edit.php?post_type=person'));
                });

                add_filter('login_redirect', function ($redirect_to, $request, $user) {
                    return admin_url('edit.php?post_type=person');
                }, 10, 3);
            endif;
        }

        add_action( 'template_redirect', function() {
            if (is_preview()) {
                wp_redirect(home_url());
                
                exit;
            }
        });

        $this->disableComments();
        $this->disableRevisions();
    }

    /**
     * Load relation handlers for post types
     */
    public function registerRelationHandlers()
    {
        $dir = __DIR__ . '/../RelationHandlers';
        $relation_handlers = Dir::list($dir, 'files');
        $namespace = 'App\\Core\\RelationHandlers\\';

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


    public function disableRevisions()
    {
        add_filter('wp_revisions_to_keep', '__return_false');
    }

    public function disableComments()
    {
        // Close comments on the front-end
        add_filter('comments_open', '__return_false', 20, 2);
        add_filter('pings_open', '__return_false', 20, 2);

        // Hide existing comments
        add_filter('comments_array', '__return_empty_array', 10, 2);

        // Remove comments page in menu
        add_action('admin_menu', function () {
            remove_menu_page('edit-comments.php');
        });

        // Remove comments links from admin bar
        add_action('init', function () {
            if (is_admin_bar_showing()) {
                remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
            }
        });

        add_action('admin_init', function () {
            // Redirect any user trying to access comments page
            global $pagenow;

            if ($pagenow === 'edit-comments.php') {
                wp_redirect(admin_url());
                exit;
            }

            // Remove comments metabox from dashboard
            remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

            // Disable support for comments and trackbacks in post types
            foreach (get_post_types() as $post_type) {
                if (post_type_supports($post_type, 'comments')) {
                    remove_post_type_support($post_type, 'comments');
                    remove_post_type_support($post_type, 'trackbacks');
                }
            }
        });
    }
}
