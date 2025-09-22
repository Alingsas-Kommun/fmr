<?php

namespace App\Core\Admin\Anniversaries;

use Illuminate\Http\Request;
use App\Services\AnniversaryService;
use App\Services\AssignmentExportService;
use App\Http\Controllers\Admin\AnniversaryController;

if (!defined('ABSPATH')) {
    exit;
}

class Index
{
    private static $instance = null;

    /**
     * Initialize the singleton instance.
     *
     * @return self
     */
    public static function init()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    /**
     * Constructor. Set up the admin page.
     */
    private function __construct()
    {
        // Constructor is kept private for singleton pattern
    }

    /**
     * Register the anniversaries menu page in WordPress admin.
     *
     * @return void
     */
    public static function register()
    {
        self::init();
        
        add_menu_page(
            __('Anniversaries', 'fmr'),
            __('Anniversaries', 'fmr'),
            'manage_options',
            'anniversaries',
            [self::$instance, 'render'],
            'dashicons-calendar',
            50
        );
    }

    /**
     * Handle the anniversaries page display.
     *
     * @return void
     */
    public function render()
    {
        // Create controller instance and handle the request
        $anniversaryService = app(AnniversaryService::class);
        $controller = app(AnniversaryController::class, [$anniversaryService]);
        
        // Create a request object from WordPress globals
        $request = Request::capture();
        
        // Handle the request and get the view
        $response = $controller->index($request);
        
        // Render the view
        echo $response->render();
    }
}