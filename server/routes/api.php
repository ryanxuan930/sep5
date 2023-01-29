<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameTagController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Admin\AdminDepartmentController;
use App\Http\Controllers\Admin\AdminOrganizationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'auth'
], function () {
    // admin
    Route::group([
        'prefix' => '/admin'
    ], function () {
        Route::post('/login', [AdminController::class, 'login']);
        Route::get('/info', [AdminController::class, 'info']);
        Route::post('/logout', [AdminController::class, 'logout']);
    });
    //user
    Route::group([
        'prefix' => '/user'
    ], function () {
        Route::post('/login', [UserController::class, 'login']);
        Route::get('/info', [UserController::class, 'info']);
        Route::patch('/edit/{u_id}', [UserController::class, 'edit']); // invalid
        Route::post('/logout', [UserController::class, 'logout']);
        Route::post('/register', [UserController::class, 'register']);
        Route::get('/exist/{account}', [UserController::class, 'exist']);
        /*
        Route::post('/reset', [UserController::class, 'reset']);
        Route::post('/reset/password/{account}/{token}', [UserController::class, 'resetPassword']);
        Route::post('/upload', [UserController::class, 'upload']);
        */
    });
});

Route::group([
    'prefix' => 'admin'
], function () {
    Route::apiResource('/admin-dept', AdminDepartmentController::class);

    Route::apiResource('/admin-org', AdminOrganizationController::class);
});

Route::apiResource('/bulletin', BulletinController::class);

Route::apiResource('/game', GameController::class);
Route::get('/game/all', [GameController::class, 'indexAll']);

Route::apiResource('/game-tag', GameTagController::class);

Route::apiResource('/event', EventController::class);

Route::apiResource('/department', DepartmentController::class);
Route::get('/department/org/{id}', [DepartmentController::class, 'showByOrg']);