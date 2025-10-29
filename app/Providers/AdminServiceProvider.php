<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;
use Illuminate\Support\Facades\Vite;
use App\Core\Admin\Cleanup;
use App\Core\Admin\Assets;
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
use App\Http\Controllers\Admin\PartyController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TypeController;
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
        $this->app->singleton(Assets::class);
        
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
        $this->app->singleton(PartyController::class);
        $this->app->singleton(RoleController::class);
        $this->app->singleton(TypeController::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only boot admin services in admin context (including login, password reset, etc.)
        if (!is_admin() && !$this->isLoginPage()) {
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

        $this->app->make(Assets::class);
        $this->app->make(Cleanup::class);

        // Load field groups and relation handlers
        add_action('init', [$this, 'loadFieldGroups']);
        add_action('init', [$this, 'registerRelationHandlers']);
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
     * Check if we're on a WordPress login page.
     */
    private function isLoginPage(): bool
    {
        global $pagenow;
        
        // Check for login page and related pages
        $loginPages = [
            'wp-login.php',
            'wp-register.php',
            'wp-signup.php'
        ];
        
        return in_array($pagenow, $loginPages) || 
               (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false);
    }
}
