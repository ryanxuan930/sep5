<?php

namespace App\Http\Controllers\Game\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AthleteController extends Controller
{
    public function index($sportCode, $gameId)
    {
        if (is_null($user = auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $userArray = array();
        $tempIndividual = DB::table($sportCode.'_'.$gameId.'_individuals')->distinct('u_id')->select('u_id')->get();
        foreach ($tempIndividual as $row) {
            array_push($userArray, $row->u_id);
        }
        $tempGroup = DB::table($sportCode.'_'.$gameId.'_groups')->leftJoin($sportCode.'_'.$gameId.'_teams', $sportCode.'_'.$gameId.'_teams.team_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.team_id')->select($sportCode.'_'.$gameId.'_teams.team_id', $sportCode.'_'.$gameId.'_teams.member_list')->get();
        foreach ($tempGroup as $row) {
            $userArray = array_merge($userArray, json_decode($row->member_list, true));
        }
        $userArray = array_unique($userArray, SORT_NUMERIC);
        $result = User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')
        ->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')
        ->select('users.u_id', 'users.first_name_ch', 'users.last_name_ch', 'users.first_name_en', 'users.last_name_en', 'users.org_code', 'users.dept_id', 'users.sex', 'users.num_in_dept', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en')->whereIn('users.u_id', $userArray)
        ->get();
        return response()->json($result);
    }
}
