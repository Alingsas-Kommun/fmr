<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ThemeServiceProvider extends SageServiceProvider
{
    public function register()
    {
        parent::register();
    }

    public function boot()
    {
        parent::boot();

        // DB::listen(function ($query) {
        //     logger()->info('Query executed', [
        //         'sql' => $query->sql,
        //         'bindings' => $query->bindings,
        //         'time' => $query->time,
        //     ]);
        // });

        $this->runMigrationsOnce();
    }

    /**
     * Run migrations only once per theme activation
     */
    protected function runMigrationsOnce()
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