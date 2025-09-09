<?php

namespace App\Core\Admin;

class Init
{
    public function __construct()
    {
        if (!function_exists('add_action')) {
            return;
        }

        /**
         * Disable heartbeat in admin.
         */
        add_action('init', function () {
            wp_deregister_script('heartbeat');
        });

        if (class_exists('App\\Core\\Admin\\Tinymce')) {
            new Tinymce();
        }

        if (class_exists('App\\Core\\Admin\\Whitelabel')) {
            new Whitelabel();
        }

        if (class_exists('App\\Core\\Admin\\AssignmentListTable')) {
            add_action('admin_menu', function () {
                AssignmentListTable::register();
            });
        }

        if (class_exists('App\\Core\\Admin\\AssignmentHandler')) {
            new AssignmentHandler();
        }

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

        $this->disableComments();
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
