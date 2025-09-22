<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;
use Illuminate\Support\Facades\Artisan;

class DatabaseServiceProvider extends SageServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Database-related services can be registered here in the future
        // For example: repositories, database utilities, etc.
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->runMigrationsOnce();

        add_action('switch_theme', function () {
            delete_option('theme_migrations_ran');
        });
    }

    /**
     * Run migrations only once per theme activation
     */
    protected function runMigrationsOnce(): void
    {
        if (! get_option('theme_migrations_ran')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
                update_option('theme_migrations_ran', true);
            } catch (\Exception $e) {
                error_log('Theme migration error: ' . $e->getMessage());
            }
        }
    }
}
