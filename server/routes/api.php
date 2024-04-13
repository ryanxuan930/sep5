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
use App\Http\Controllers\FileLogController;
use App\Http\Controllers\TribeController;
use App\Http\Controllers\SchoolTeamController;
use App\Http\Controllers\Admin\AdminDepartmentController;
use App\Http\Controllers\Admin\AdminOrganizationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RegistrationFormController as RFController;
use App\Http\Controllers\ConsentFormController as CFController;
use App\Http\Controllers\RegistrationConfigController as RCController;
use App\Http\Controllers\Game\Main\DateController as GameDateController;
use App\Http\Controllers\Game\Main\DivisionController as GameDivisionController;
use App\Http\Controllers\Game\Main\LaneController as GameLaneController;
use App\Http\Controllers\Game\Main\ParamsController as GameParamsController;
use App\Http\Controllers\Game\Main\EventController as GameEventController;
use App\Http\Controllers\Game\Registration\IndividualController as GameIndividualController;
use App\Http\Controllers\Game\Registration\GroupController as GameGroupController;
use App\Http\Controllers\Game\Common\TempController as GameTempController;
use App\Http\Controllers\Game\Common\AthleteController as GameAthleteController;
use App\Http\Controllers\Game\Common\ScheduleController as GameScheduleController;
use App\Http\Controllers\Game\Common\ResultController as GameResultController;

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

Route::get('/password-hash/{password}', function($password) {
    return password_hash($password, PASSWORD_DEFAULT);
});

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
    Route::get('/admin-dept-by-org/{adminOrgId}', [AdminDepartmentController::class, 'indexByOrg']);

    Route::apiResource('/admin-org', AdminOrganizationController::class);

    Route::apiResource('/user', AdminController::class);
});

Route::group([
    'prefix' => '{org_id}'
], function () {
    Route::apiResource('/bulletin', BulletinController::class);
    Route::get('/bulletin-game/{game_id}', [BulletinController::class, 'indexByGame']);

    Route::apiResource('/game', GameController::class);
    Route::get('/game-all', [GameController::class, 'indexAll']);
    Route::get('/game-dept/{dept_id}/{all?}', [GameController::class, 'indexByDept']);
    Route::get('/game-by-tag/{gameTag}', [GameController::class, 'indexByTag']);

    Route::apiResource('/game-tag', GameTagController::class);

    Route::apiResource('/school-team', SchoolTeamController::class);
    Route::get('/school-team-all', [SchoolTeamController::class, 'indexAll']);
});

Route::apiResource('/department', DepartmentController::class);
Route::get('/department/org/{org_id}', [DepartmentController::class, 'indexByOrg']);
Route::get('/department/org/code/{org_code}', [DepartmentController::class, 'indexByOrgCode']);

Route::apiResource('/event', EventController::class);
Route::get('/event/sport/{sportId}', [EventController::class, 'indexBySport']);

Route::apiResource('/sport', SportController::class);

Route::apiResource('/reg-form', RFController::class);
Route::get('/reg-form-game/{gameId}', [RFController::class, 'indexByGame']);

Route::apiResource('/consent-form', CFController::class);
Route::get('/consent-form-game/{gameId}', [CFController::class, 'indexByGame']);

Route::apiResource('/organization', OrganizationController::class);
Route::post('/organization-create', [OrganizationController::class, 'create']);

Route::apiResource('/user', UserController::class);
Route::get('/user-partial', [UserController::class, 'indexByUser']);
Route::get('/user/athlete/{id}', [UserController::class, 'showByAthleteId']);
Route::get('/user/name/{firstName}/{lastName}', [UserController::class, 'showByName']);
Route::post('/user-upload', [UserController::class, 'storeByBatch']);
Route::post('/user-search', [UserController::class, 'search']);
Route::post('/user-search-unit', [UserController::class, 'searchByUnit']);
Route::post('/user-from-list', [UserController::class, 'getUserFromList']);

Route::apiResource('/country', CountryController::class);

Route::apiResource('/city', CityController::class);

Route::apiResource('/tribe', TribeController::class);

Route::apiResource('/file-list', FileLogController::class);

Route::apiResource('/reg-config', RCController::class);
Route::get('/reg-config-game/{gameId}', [RCController::class, 'showByGame']);

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
            Route::get('/{divisionId}/{eventCode}', [$ControllerClass, 'gettersByEvent']);
            Route::post('', [$ControllerClass, 'setters']);
            Route::post('/patch', [$ControllerClass, 'patcher']);
        });
    });
    Route::group([
        'prefix' => 'common'
    ], function () {
        Route::apiResource('individual', GameIndividualController::class);
        Route::group([
            'prefix' => 'individual'
        ], function () {
            $ControllerClass = GameIndividualController::class;
            Route::apiResource('', $ControllerClass);
            Route::get('/by/user', [$ControllerClass, 'indexByUser']);
            Route::get('/by/athlete', [$ControllerClass, 'indexByAthlete']);
            Route::get('/by/athlete/unit/{unit}', [$ControllerClass, 'indexByAthleteUnit']);
            Route::get('/by/event/{divisionId}/{eventCode}', [$ControllerClass, 'indexByEvent']);
            Route::get('/by/count/{unit}', [$ControllerClass, 'indexByCount']);
            Route::get('/by/count', [$ControllerClass, 'indexByCountFull']);
            Route::patch('/update/heat-lane', [$ControllerClass, 'updateHeatLane']);
            Route::patch('/update/result', [$ControllerClass, 'updateResult']);
            Route::patch('/update/road-result', [$ControllerClass, 'updateRoadResult']);
        });
        Route::apiResource('group', GameGroupController::class);
        Route::group([
            'prefix' => 'group'
        ], function () {
            $ControllerClass = GameGroupController::class;
            Route::apiResource('', $ControllerClass);
            Route::get('/by/user', [$ControllerClass, 'indexByUser']);
            Route::get('/by/team/{teamId}', [$ControllerClass, 'showTeam']);
            Route::get('/by/event/{divisionId}/{eventCode}', [$ControllerClass, 'indexByEvent']);
            Route::get('/by/count/{unit}', [$ControllerClass, 'indexByCount']);
            Route::get('/by/count', [$ControllerClass, 'indexByCountFull']);
            Route::patch('/update/team/{id}', [$ControllerClass, 'updateTeam']);
            Route::patch('/update/heat-lane', [$ControllerClass, 'updateHeatLane']);
            Route::patch('/update/result', [$ControllerClass, 'updateResult']);
        });
        Route::apiResource('temp', GameTempController::class);
        Route::get('athlete/list', [GameAthleteController::class, 'index']);
        Route::get('athlete/find/{uid}', [GameAthleteController::class, 'find']);
        Route::get('athlete/bib/{bib}', [GameAthleteController::class, 'findByBib']);
        Route::get('result/ranking/{num?}', [GameResultController::class, 'ranking']);
        Route::post('result/champion/{num?}', [GameResultController::class, 'champion']);
        Route::get('result/all', [GameResultController::class, 'result']);
        Route::get('result/medal/{orgCode}/{deptId}/{place}', [GameResultController::class, 'resultByRanking']);
        Route::group([
            'prefix' => 'schedule'
        ], function () {
            $ControllerClass = GameScheduleController::class;
            Route::get('', [$ControllerClass, 'getters']);
            Route::get('/full', [$ControllerClass, 'gettersFull']);
            Route::get('/{id}', [$ControllerClass, 'getter']);
            Route::post('', [$ControllerClass, 'setters']);
            Route::post('/update/{id}', [$ControllerClass, 'patcher']);
        });
    });
});