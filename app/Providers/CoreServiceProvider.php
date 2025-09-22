<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;
use App\Core\Theme;
use App\Core\Filters;
use App\Utilities\Dir;

class CoreServiceProvider extends SageServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register core theme services
        $this->app->singleton(Theme::class);
        $this->app->singleton(Filters::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Boot theme
        $this->app->make(Theme::class);
        $this->app->make(Filters::class);
        
        // Load post types and taxonomies dynamically
        add_action('init', [$this, 'loadPostTypes']);
        add_action('init', [$this, 'loadTaxonomies']);
    }

    /**
     * Load post types dynamically.
     */
    public function loadPostTypes(): void
    {
        $dir = __DIR__ . '/../Core/PostTypes';
        $postTypes = Dir::list($dir, 'files');
        $namespace = 'App\\Core\\PostTypes\\';

        foreach ($postTypes as $postType) {
            $className = $namespace . basename($postType, '.php');
            
            if (class_exists($className)) {
                $this->app->singleton($className);
                $this->app->make($className);
            }
        }
    }

    /**
     * Load taxonomies dynamically.
     */
    public function loadTaxonomies(): void
    {
        $dir = __DIR__ . '/../Core/Taxonomies';
        $taxonomies = Dir::list($dir, 'files');
        $namespace = 'App\\Core\\Taxonomies\\';

        foreach ($taxonomies as $taxonomy) {
            $className = $namespace . basename($taxonomy, '.php');
            
            if (class_exists($className)) {
                $this->app->singleton($className);
                $this->app->make($className);
            }
        }
    }
}
