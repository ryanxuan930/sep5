<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;

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
    /*
    Route::group([
        'prefix' => '/admin'
    ], function () {
        Route::post('/login', [AdminController::class, 'login']);
        Route::get('/info', [AdminController::class, 'info']);
        Route::post('/logout', [AdminController::class, 'logout']);
    });
    */
    //user
    Route::group([
        'prefix' => '/user'
    ], function () {
        Route::post('/login', [UserController::class, 'login']);
        /*
        Route::get('/info', [UserController::class, 'info']);
        Route::patch('/edit/{u_id}', [UserController::class, 'edit']); // invalid
        Route::post('/logout', [UserController::class, 'logout']);
        Route::post('/register', [UserController::class, 'register']);
        Route::get('/exist/{account}', [UserController::class, 'exist']);
        Route::group([
            'prefix' => '/verify'
        ], function () {
            Route::post('/email', [UserController::class, 'sendVerifyMail']);
            Route::post('/email/code', [UserController::class, 'codeVerify']);
        });
        Route::post('/reset', [UserController::class, 'reset']);
        Route::post('/reset/password/{account}/{token}', [UserController::class, 'resetPassword']);
        Route::post('/upload', [UserController::class, 'upload']);
        */
    });
});
