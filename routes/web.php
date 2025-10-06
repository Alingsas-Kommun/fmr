<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DecisionAuthorityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Utilities\General;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

// Front page route
Route::get('/', [HomeController::class, 'index'])->name('homepage');

// Style guide route
Route::view(General::getRouteSlug('style-guide'), 'styleguide')->name('styleguide');

// Assignment routes
Route::get(General::getRouteSlug('assignments').'/{assignment}', [AssignmentController::class, 'show'])
    ->where('assignment', '[0-9]+')
    ->name('assignments.show');

// Decision authority routes
Route::get(General::getRouteSlug('decision-authorities').'/{decisionAuthority}', [DecisionAuthorityController::class, 'show'])
    ->where('decisionAuthority', '[0-9]+')
    ->name('decision-authorities.show');

// Search routes
Route::get(General::getRouteSlug('search'), [SearchController::class, 'search'])->name('search.show');