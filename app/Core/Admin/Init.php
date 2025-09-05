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
    }
}
