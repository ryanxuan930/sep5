<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController as AuthUserController;
use App\Http\Controllers\Auth\AdminController as AuthAdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameTagController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TribeController;
use App\Http\Controllers\SchoolTeamController;
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
        Route::post('/login', [AuthAdminController::class, 'login']);
        Route::get('/info', [AuthAdminController::class, 'info']);
        Route::post('/logout', [AuthAdminController::class, 'logout']);
    });
    //user
    Route::group([
        'prefix' => '/user'
    ], function () {
        Route::post('/login', [AuthUserController::class, 'login']);
        Route::get('/info', [AuthUserController::class, 'info']);
        Route::patch('/edit/{u_id}', [AuthUserController::class, 'edit']); // invalid
        Route::post('/logout', [AuthUserController::class, 'logout']);
        Route::post('/register', [AuthUserController::class, 'register']);
        Route::get('/exist/{account}', [AuthUserController::class, 'exist']);
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

Route::group([
    'prefix' => '{org_id}'
], function () {
    Route::apiResource('/bulletin', BulletinController::class);

    Route::apiResource('/game', GameController::class);
    Route::get('/game-all', [GameController::class, 'indexAll']);

    Route::apiResource('/game-tag', GameTagController::class);

    Route::apiResource('/school-team', SchoolTeamController::class);
});

Route::apiResource('/department', DepartmentController::class);
Route::get('/department/org/{org_id}', [DepartmentController::class, 'indexByOrg']);

Route::apiResource('/event', EventController::class);

Route::apiResource('/sport', SportController::class);

Route::apiResource('/organization', OrganizationController::class);

Route::apiResource('/user', UserController::class);

Route::apiResource('/country', CountryController::class);

Route::apiResource('/city', CityController::class);

Route::apiResource('/tribe', TribeController::class);

Route::group([
    'prefix' => '/file'
], function () {
    Route::post('/upload', [FileController::class, 'upload']);
});