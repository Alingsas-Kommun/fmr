<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AssignmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

Route::prefix('assignments')->group(function () {
    Route::get('/', [AssignmentController::class, 'index'])->name('api.assignments.index');
    Route::get('/{id}', [AssignmentController::class, 'show'])->name('api.assignments.show');
});