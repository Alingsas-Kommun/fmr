<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\PartyController;
use App\Http\Controllers\Api\BoardController;
use App\Http\Controllers\Api\DecisionAuthorityController;
use App\Http\Middleware\ApiKeyMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| All routes are protected by API key authentication middleware.
|
*/

Route::middleware(ApiKeyMiddleware::class)->group(function () {
    // Assignment routes
    Route::prefix('assignments')->group(function () {
        Route::get('/', [AssignmentController::class, 'index'])->name('api.assignments.index');
        Route::get('/{id}', [AssignmentController::class, 'show'])->name('api.assignments.show');
    });

    // Person routes
    Route::prefix('persons')->group(function () {
        Route::get('/', [PersonController::class, 'index'])->name('api.persons.index');
        Route::get('/{id}', [PersonController::class, 'show'])->name('api.persons.show');
    });

    // Party routes
    Route::prefix('parties')->group(function () {
        Route::get('/', [PartyController::class, 'index'])->name('api.parties.index');
        Route::get('/{id}', [PartyController::class, 'show'])->name('api.parties.show');
    });

    // Board routes
    Route::prefix('boards')->group(function () {
        Route::get('/', [BoardController::class, 'index'])->name('api.boards.index');
        Route::get('/{id}', [BoardController::class, 'show'])->name('api.boards.show');
    });

    // Decision Authority routes
    Route::prefix('decision-authorities')->group(function () {
        Route::get('/', [DecisionAuthorityController::class, 'index'])->name('api.decision-authorities.index');
        Route::get('/{id}', [DecisionAuthorityController::class, 'show'])->name('api.decision-authorities.show');
    });
});