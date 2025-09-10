<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DecisionAuthorityController;
use App\Http\Controllers\HomeController;
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
Route::get(General::getRouteSlug('assignments'), [AssignmentController::class, 'index'])->name('assignments.index');
Route::get(General::getRouteSlug('assignments').'/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');

// Decision authority routes
Route::get(General::getRouteSlug('decision-authorities'), [DecisionAuthorityController::class, 'index'])->name('decision-authorities.index');
Route::get(General::getRouteSlug('decision-authorities').'/{decisionAuthority}', [DecisionAuthorityController::class, 'show'])->name('decision-authorities.show');