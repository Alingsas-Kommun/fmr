<?php

namespace App\Core\Assets;

use function App\Core\getLocalizedData;

class Assets
{

    public function __construct()
    {
        if (!function_exists('add_action')) {
            return;
        }

        add_action('wp_footer', [$this, 'registerLocalizedData']);
    }

    /**
     * Register the localized data.
     *
     * @return void
     */

    public function registerLocalizedData()
    {
        $data = getLocalizedData();

        if (empty($data)) {
            return;
        }

        printf(
            '<script>var variables = %s;</script>',
            wp_json_encode($data)
        );
    }
}