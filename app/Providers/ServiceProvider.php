<?php

namespace App\Providers;

use App\Exceptions\Handler;
use App\Services\ExportService;
use App\Services\AnniversaryService;
use App\Http\Controllers\HomeController;
use App\Services\AssignmentExportService;
use Roots\Acorn\Sage\SageServiceProvider;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AssignmentController;
use Illuminate\Contracts\Debug\ExceptionHandler;
use App\Http\Controllers\DecisionAuthorityController;
use Carbon\Carbon;

class ServiceProvider extends SageServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register services
        $this->app->singleton(AnniversaryService::class);
        $this->app->singleton(ExportService::class);
        $this->app->singleton(AssignmentExportService::class);
        
        // Register controllers
        $this->app->singleton(AssignmentController::class);
        $this->app->singleton(DecisionAuthorityController::class);
        $this->app->singleton(HomeController::class);
        $this->app->singleton(SearchController::class);
        
        // Register custom exception handler
        $this->app->singleton(ExceptionHandler::class, Handler::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Carbon macro for localized date formatting
        Carbon::macro('formatDate', function ($format = 'j M Y') {
            return $this->locale(get_site_locale())->translatedFormat($format);
        });
    }
}
