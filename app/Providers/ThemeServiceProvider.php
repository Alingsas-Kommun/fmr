<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class ThemeServiceProvider extends SageServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        parent::register();
        
        // Register other service providers
        $this->app->register(DatabaseServiceProvider::class);
        $this->app->register(CoreServiceProvider::class);
        $this->app->register(AdminServiceProvider::class);
        $this->app->register(ServiceProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();
    }
}