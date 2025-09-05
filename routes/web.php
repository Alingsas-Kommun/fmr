<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\PersonController;
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

// Party routes
Route::get(General::getRouteSlug('parties'), [PartyController::class, 'index'])->name('parties.index');
Route::get(General::getRouteSlug('parties').'/{party}', [PartyController::class, 'show'])->name('parties.show');

// Style guide route
Route::view(General::getRouteSlug('style-guide'), 'styleguide')->name('styleguide');

// Assignment routes
Route::get(General::getRouteSlug('assignments'), [AssignmentController::class, 'index'])->name('assignments.index');
Route::get(General::getRouteSlug('assignments').'/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');

// Board routes
Route::get(General::getRouteSlug('boards'), [BoardController::class, 'index'])->name('boards.index');
Route::get(General::getRouteSlug('boards').'/{board}', [BoardController::class, 'show'])->name('boards.show');

// Person routes
Route::get(General::getRouteSlug('persons'), [PersonController::class, 'index'])->name('persons.index');
Route::get(General::getRouteSlug('persons').'/{person}', [PersonController::class, 'show'])->name('persons.show');