<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController as AuthUserController;
use App\Http\Controllers\Auth\AdminController as AuthAdminController;
use App\Http\Controllers\ConfigController;
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
use App\Http\Controllers\Game\Main\DateController as GameDateController;
use App\Http\Controllers\Game\Main\DivisionController as GameDivisionController;
use App\Http\Controllers\Game\Main\LaneController as GameLaneController;
use App\Http\Controllers\Game\Main\ParamsController as GameParamsController;
use App\Http\Controllers\Game\Main\EventController as GameEventController;
use App\Http\Controllers\Game\Registration\IndividualController as GameIndividualController;

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

Route::apiResource('/config', ConfigController::class);

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
        Route::get('/info/pure', [AuthUserController::class, 'pureInfo']);
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
    Route::get('/game-by-tag/{gameTag}', [GameController::class, 'indexByTag']);

    Route::apiResource('/game-tag', GameTagController::class);

    Route::apiResource('/school-team', SchoolTeamController::class);
    Route::get('/school-team/code/{orgCode}', [SchoolTeamController::class, 'indexByCode']);
});

Route::apiResource('/department', DepartmentController::class);
Route::get('/department/org/{org_id}', [DepartmentController::class, 'indexByOrg']);
Route::get('/department/org/code/{org_code}', [DepartmentController::class, 'indexByOrgCode']);

Route::apiResource('/event', EventController::class);
Route::get('/event/sport/{sportId}', [EventController::class, 'indexBySport']);

Route::apiResource('/sport', SportController::class);

Route::apiResource('/organization', OrganizationController::class);

Route::apiResource('/user', UserController::class);
Route::get('/user-partial', [UserController::class, 'indexByUser']);
Route::get('/user/athlete/{id}', [UserController::class, 'showByAthleteId']);

Route::apiResource('/country', CountryController::class);

Route::apiResource('/city', CityController::class);

Route::apiResource('/tribe', TribeController::class);

Route::group([
    'prefix' => '/file'
], function () {
    Route::post('/upload', [FileController::class, 'upload']);
});

Route::group([
    'prefix' => 'game/{sportCode}/{gameId}'
], function () {
    Route::group([
        'prefix' => 'main'
    ], function () {
        Route::group([
            'prefix' => 'date'
        ], function () {
            $ControllerClass = GameDateController::class;
            Route::get('', [$ControllerClass, 'getters']);
            Route::post('', [$ControllerClass, 'setters']);
        });
        Route::group([
            'prefix' => 'division'
        ], function () {
            $ControllerClass = GameDivisionController::class;
            Route::get('', [$ControllerClass, 'getters']);
            Route::post('', [$ControllerClass, 'setters']);
        });
        Route::group([
            'prefix' => 'lane'
        ], function () {
            $ControllerClass = GameLaneController::class;
            Route::get('', [$ControllerClass, 'getters']);
            Route::post('', [$ControllerClass, 'setters']);
        });
        Route::group([
            'prefix' => 'event'
        ], function () {
            $ControllerClass = GameEventController::class;
            Route::get('', [$ControllerClass, 'getters']);
            Route::get('/full', [$ControllerClass, 'gettersFull']);
            Route::post('', [$ControllerClass, 'setters']);
        });
        Route::group([
            'prefix' => 'params'
        ], function () {
            $ControllerClass = GameParamsController::class;
            Route::get('', [$ControllerClass, 'getters']);
            Route::get('/full', [$ControllerClass, 'gettersFull']);
            Route::post('', [$ControllerClass, 'setters']);
        });
    });
    Route::group([
        'prefix' => 'registration'
    ], function () {
        Route::group([
            'prefix' => 'individual'
        ], function () {
            $ControllerClass = GameIndividualController::class;
            Route::apiResource('', $ControllerClass);
            Route::patch('partial', [$ControllerClass, 'updatePartial']);
        });
    });
});