<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;
use Illuminate\Support\Facades\Vite;
use App\Core\Admin\Cleanup;
use App\Core\Admin\Whitelabel;
use App\Core\Admin\Assignments\Index as AssignmentsIndex;
use App\Core\Admin\Assignments\Edit as AssignmentsEdit;
use App\Core\Admin\DecisionAuthorities\Index as DecisionAuthoritiesIndex;
use App\Core\Admin\DecisionAuthorities\Edit as DecisionAuthoritiesEdit;
use App\Core\Admin\Anniversaries\Index as AnniversariesIndex;
use App\Core\Admin\ConfigurationPage;
use App\Http\Controllers\Admin\AnniversaryController;
use App\Http\Controllers\Admin\AssignmentController as AdminAssignmentController;
use App\Http\Controllers\Admin\BoardController;
use App\Http\Controllers\Admin\DecisionAuthorityController as AdminDecisionAuthorityController;
use App\Http\Controllers\Admin\PersonController;
use App\Http\Controllers\Admin\RoleController;
use App\Utilities\Dir;

class AdminServiceProvider extends SageServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register admin services
        $this->app->singleton(Cleanup::class);
        $this->app->singleton(Whitelabel::class);
        
        // Register admin pages - these use singleton patterns, so we bind them differently
        $this->app->singleton(AssignmentsIndex::class, function () {
            return AssignmentsIndex::init();
        });
        $this->app->singleton(AssignmentsEdit::class);
        $this->app->singleton(DecisionAuthoritiesIndex::class, function () {
            return DecisionAuthoritiesIndex::init();
        });
        $this->app->singleton(DecisionAuthoritiesEdit::class);
        $this->app->singleton(AnniversariesIndex::class, function () {
            return AnniversariesIndex::init();
        });
        $this->app->singleton(ConfigurationPage::class);

        // Register admin controllers
        $this->app->singleton(AdminAssignmentController::class);
        $this->app->singleton(AdminDecisionAuthorityController::class);
        $this->app->singleton(AnniversaryController::class);
        $this->app->singleton(BoardController::class);
        $this->app->singleton(PersonController::class);
        $this->app->singleton(RoleController::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only boot admin services in admin context
        if (!is_admin()) {
            return;
        }

        // Register admin menu pages
        add_action('admin_menu', function () {
            $this->app->make(AssignmentsIndex::class)->register();
            $this->app->make(DecisionAuthoritiesIndex::class)->register();
            $this->app->make(AnniversariesIndex::class)->register();
        });

        // Initialize admin components
        add_action('init', function () {
            $this->app->make(AssignmentsEdit::class);
            $this->app->make(DecisionAuthoritiesEdit::class);
            $this->app->make(ConfigurationPage::class);
        });

        add_action('after_setup_theme', function () {
            $this->app->make(Whitelabel::class);
            $this->app->make(Cleanup::class);
        });

        // Load field groups and relation handlers
        add_action('init', [$this, 'loadFieldGroups']);
        add_action('init', [$this, 'registerRelationHandlers']);
        
        // Enqueue admin assets
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    /**
     * Load field groups dynamically.
     */
    public function loadFieldGroups(): void
    {
        $dir = __DIR__ . '/../Core/Admin/FieldGroups';
        $fieldGroups = Dir::list($dir, 'files');
        $namespace = 'App\\Core\\Admin\\FieldGroups\\';

        foreach ($fieldGroups as $group) {
            $className = $namespace . basename($group, '.php');
            
            if (class_exists($className)) {
                $this->app->singleton($className);
                $this->app->make($className);
            }
        }
    }

    /**
     * Register relation handlers dynamically.
     */
    public function registerRelationHandlers(): void
    {
        $dir = __DIR__ . '/../Core/Admin/RelationHandlers';
        $handlers = Dir::list($dir, 'files');
        $namespace = 'App\\Core\\Admin\\RelationHandlers\\';

        foreach ($handlers as $handler) {
            $className = $namespace . basename($handler, '.php');
            
            if (class_exists($className)) {
                $this->app->singleton($className);
                $this->app->make($className);
            }
        }
    }

    /**
     * Enqueue admin assets.
     */
    public function enqueueAdminAssets(): void
    {
        // Skip asset loading in CLI context
        if (php_sapi_name() === 'cli') {
            return;
        }

        // Enqueue WordPress media library for image fields
        wp_enqueue_media();

        $style = Vite::asset('resources/css/admin.scss');
        wp_enqueue_style('admin-css', $style, false, '');

        $script = Vite::asset('resources/js/admin.js');
        wp_enqueue_script('admin-js', $script, ['jquery', 'underscore', 'wp-util'], null, true);

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
