<?php

namespace App\Core\Admin;

class Cleanup
{
    public function __construct()
    {
        if (!function_exists('add_action')) {
            return;
        }

        $this->cleanupMenu();
        $this->disableHeartbeat();
        $this->setupRedirects();
        $this->disableComments();
        $this->disableRevisions();
        $this->disablePreview();
    }

    /**
     * Clean up admin menu by removing unnecessary items
     */
    private function cleanupMenu()
    {
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
    }

    /**
     * Disable WordPress heartbeat in admin
     */
    private function disableHeartbeat()
    {
        add_action('init', function () {
            wp_deregister_script('heartbeat');
        });
    }

    /**
     * Setup redirects for better UX
     */
    private function setupRedirects()
    {
        // Redirect to persons instead of dashboard when visiting index or login
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
    }

    /**
     * Disable preview functionality
     */
    private function disablePreview()
    {
        add_action('template_redirect', function() {
            if (is_preview()) {
                wp_redirect(home_url());
                exit;
            }
        });
    }

    /**
     * Disable WordPress comments system
     */
    private function disableComments()
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

    /**
     * Disable WordPress revisions
     */
    private function disableRevisions()
    {
        add_filter('wp_revisions_to_keep', '__return_false');
    }
}
