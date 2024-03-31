<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AddUserTestController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ListController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', [TestController::class, 'getTestAll']);
Route::post('/addUserTest', [TestController::class, 'addUserTest']);
Route::post('/serchUserTest', [TestController::class, 'serchUserTest']);

Route::prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/{id}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/{id}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/search-myproject', [ProjectController::class, 'search_myproject'])->name('projects.search_myproject');
    Route::post('/delete', [ProjectController::class, 'delete'])->name('projects.delete');
});

Route::prefix('account')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('account.index');
    Route::get('/create', [AccountController::class, 'create'])->name('account.create');
    Route::post('/', [AccountController::class, 'store'])->name('account.store');
    Route::post('/add-account', [AccountController::class, 'add_account'])->name('account.add_account');
    //Route::post('/store-default', [AccountController::class, 'store_default'])->name('account.store-default');
    // Route::get('/{id}', [ProjectController::class, 'show'])->name('projects.show');
    // Route::get('/{id}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::post('/update', [AccountController::class, 'update'])->name('account.update');
    Route::post('/delete', [AccountController::class, 'delete'])->name('account.delete');
    Route::post('/friend-search',[AccountController::class, 'friend_search'])->name('account.friend_search');
    Route::post('/friend-add',[AccountController::class, 'friend_add'])->name('account.friend_add');
    
    //Route::delete('/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
});

Route::prefix('tasks')->group(function () {
    Route::post('/store', [TaskController::class, 'store'])->name('tasks.store');
    Route::post('/get', [TaskController::class, 'get'])->name('tasks.get');
    Route::post('/update-progress', [TaskController::class, 'update_progress'])->name('tasks.update_progress');
    Route::post('/delete', [TaskController::class, 'delete'])->name('tasks.delete');
});

Route::prefix('lists')->group(function () {
    Route::post('/store', [ListController::class, 'store'])->name('lists.store');
    Route::post('/get', [ListController::class, 'get'])->name('lists.get');
    Route::post('/update-progress-particle', [ListController::class, 'update_progress_particle'])->name('lists.update_progress_particle');
    Route::post('/delete', [ListController::class, 'delete'])->name('lists.delete');
    // Route::post('/update-progress', [TaskController::class, 'update_progress'])->name('tasks.update_progress');
});

