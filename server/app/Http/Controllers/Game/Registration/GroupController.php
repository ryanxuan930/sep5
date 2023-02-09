<?php

namespace App\Http\Controllers\Game\Registration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\SportList;
use App\Mods\GameFunctions;

class GroupController extends Controller
{
    private $tableName = 'groups';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($sportCode, $gameId)
    {
        return response()->json(DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)
        ->leftJoin($sportCode.'_'.$gameId.'_divisions', $sportCode.'_'.$gameId.'_divisions.division_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id')
        ->leftJoin($sportCode.'_'.$gameId.'_teams', $sportCode.'_'.$gameId.'_teams.team_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.team_id')
        ->leftJoin('organizations', $sportCode.'_'.$gameId.'_teams.org_id', '=', 'organizations.org_id')
        ->leftJoin('departments', $sportCode.'_'.$gameId.'_teams.dept_id', '=', 'departments.dept_id')
        ->leftJoin('events', 'events.event_code', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code')
        ->select($sportCode.'_'.$gameId.'_'.$this->tableName.'.*', $sportCode.'_'.$gameId.'_teams.*', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en', 'events.event_ch', 'events.event_en', 'events.event_jp', 'events.event_abbr', $sportCode.'_'.$gameId.'_divisions.*')
        ->get());
    }
    public function indexByUser($sportCode, $gameId)
    {
        if (is_null($user = auth('user')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $permission = $user->permission;
        $query = DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)
        ->leftJoin($sportCode.'_'.$gameId.'_divisions', $sportCode.'_'.$gameId.'_divisions.division_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id')
        ->leftJoin($sportCode.'_'.$gameId.'_teams', $sportCode.'_'.$gameId.'_teams.team_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.team_id')
        ->leftJoin('organizations', $sportCode.'_'.$gameId.'_teams.org_id', '=', 'organizations.org_id')
        ->leftJoin('departments', $sportCode.'_'.$gameId.'_teams.dept_id', '=', 'departments.dept_id')
        ->leftJoin('events', 'events.event_code', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code')
        ->select($sportCode.'_'.$gameId.'_'.$this->tableName.'.*', $sportCode.'_'.$gameId.'_teams.*', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en', 'events.event_ch', 'events.event_en', 'events.event_jp', 'events.event_abbr', $sportCode.'_'.$gameId.'_divisions.*');
        if ($permission == 0){
            return response()->json($query->whereJsonContains($sportCode.'_'.$gameId.'_teams.member_list', $user->u_id)->get());
        } else if ($permission == 1) {
            return response()->json($query->where($sportCode.'_'.$gameId.'_teams.dept_id', $user->dept_id)->get());
        } else if ($permission == 2) {
            return response()->json($query->where('organizations.org_code', $user->org_code)->get());
        } else {
            return response()->json([]);
        }
    }
    public function indexByCount($sportCode, $gameId, $unit)
    {
        if (is_null($user = auth('user')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $query = DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->leftJoin($sportCode.'_'.$gameId.'_teams', $sportCode.'_'.$gameId.'_teams.team_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.team_id')->leftJoin('organizations', $sportCode.'_'.$gameId.'_teams.org_id', '=', 'organizations.org_id');
        $table = DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->leftJoin($sportCode.'_'.$gameId.'_teams', $sportCode.'_'.$gameId.'_teams.team_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.team_id')->leftJoin('organizations', $sportCode.'_'.$gameId.'_teams.org_id', '=', 'organizations.org_id');
        if ($unit == 2) {
            $query->where('organizations.org_code', $user->org_code);
            $table->where('organizations.org_code', $user->org_code);
        } else if ($user == 1) {
            $query->where($sportCode.'_'.$gameId.'_teams.dept_id', $user->dept_id);
            $table->where($sportCode.'_'.$gameId.'_teams.dept_id', $user->dept_id);
        } else {
            $query->whereJsonContains($sportCode.'_'.$gameId.'_teams.member_list', $user->u_id);
            $table->whereJsonContains($sportCode.'_'.$gameId.'_teams.member_list', $user->u_id);
        }
        $result = array();
        $result['event'] = $query->select($sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id', $sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code', DB::raw('count(*) as total'))->groupBy($sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id', $sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code')->get();
        $result['athlete'] = $table->select($sportCode.'_'.$gameId.'_'.$this->tableName.'.team_id', DB::raw('count(*) as total'))->groupBy($sportCode.'_'.$gameId.'_'.$this->tableName.'.team_id')->get();
        return response()->json($result);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
