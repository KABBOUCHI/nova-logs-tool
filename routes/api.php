<?php

use Illuminate\Support\Facades\Route;
use KABBOUCHI\LogsTool\Http\Controllers\LogsController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('logs', [LogsController::class, 'index']);
Route::get('logs/permissions', [LogsController::class, 'permissions']);
Route::get('logs/{log}', [LogsController::class, 'show']);
Route::get('daily-log-files', [LogsController::class, 'dailyLogFiles']);
Route::delete('logs', [LogsController::class, 'destroy']);
