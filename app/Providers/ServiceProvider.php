<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;
use App\Services\AnniversaryService;
use App\Services\ExportService;
use App\Services\AssignmentExportService;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DecisionAuthorityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\AssignmentController as ApiAssignmentController;

class ServiceProvider extends SageServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register business services
        $this->app->singleton(AnniversaryService::class);
        $this->app->singleton(ExportService::class);
        $this->app->singleton(AssignmentExportService::class);
        
        // Register controllers
        $this->app->singleton(AssignmentController::class);
        $this->app->singleton(DecisionAuthorityController::class);
        $this->app->singleton(HomeController::class);
        $this->app->singleton(ApiAssignmentController::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Services are ready to be injected wherever needed
        // No specific boot logic required for these services
    }
}
