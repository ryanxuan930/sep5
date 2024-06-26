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
        if ($sportCode != 'mrth') {
            $tempGroup = DB::table($sportCode.'_'.$gameId.'_groups')->leftJoin($sportCode.'_'.$gameId.'_teams', $sportCode.'_'.$gameId.'_teams.team_id', '=', $sportCode.'_'.$gameId.'_groups'.'.team_id')->select($sportCode.'_'.$gameId.'_teams.team_id', $sportCode.'_'.$gameId.'_teams.member_list')->get();
            if ($tempGroup->count() > 0) {
                foreach ($tempGroup as $row) {
                    if ($row->member_list != null) {
                        $userArray = array_merge($userArray, json_decode($row->member_list, true));
                    }
                }
            }
        }
        $userArray = array_unique($userArray, SORT_NUMERIC);
        $result = User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')
        ->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')
        ->select('users.*', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en')->whereIn('users.u_id', $userArray)
        ->get();
        return response()->json($result);
    }
    public function find($sportCode, $gameId, $uid)
    {
        if (is_null($user = auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $userArray = array();
        $tempIndividual = DB::table($sportCode.'_'.$gameId.'_individuals')->distinct('u_id')->select('u_id')->get();
        foreach ($tempIndividual as $row) {
            array_push($userArray, $row->u_id);
        }
        if ($sportCode != 'mrth') {
            $tempGroup = DB::table($sportCode.'_'.$gameId.'_groups')->leftJoin($sportCode.'_'.$gameId.'_teams', $sportCode.'_'.$gameId.'_teams.team_id', '=', $sportCode.'_'.$gameId.'_groups'.'.team_id')->select($sportCode.'_'.$gameId.'_teams.team_id', $sportCode.'_'.$gameId.'_teams.member_list')->get();
            foreach ($tempGroup as $row) {
                $userArray = array_merge($userArray, json_decode($row->member_list, true));
            }
        }
        if (in_array($uid, $userArray)) {
            return response()->json(true);
        } else {
            return response()->json(false);
        }
    }
    public function findByBib($sportCode, $gameId, $bib)
    {
        return response()->json(DB::table($sportCode.'_'.$gameId.'_bibs')->leftJoin('users', $sportCode.'_'.$gameId.'_bibs.u_id', '=', 'users.u_id')->leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->leftJoin('countries', 'users.nationality', '=', 'countries.country_code')->leftJoin('tribes', 'users.indigenous_tribe_id', '=', 'tribes.tribe_id')->leftJoin('sport_lists', 'users.gifited_sport_id', '=', 'sport_lists.sport_id')->leftJoin('cities', 'users.household_city_code', '=', 'cities.city_code')->select('users.*', 'organizations.*', 'departments.*', 'countries.*', 'tribes.*', 'sport_lists.*', 'cities.*')->where($sportCode.'_'.$gameId.'_bibs.bib', $bib)->first());
    }

    public function getAllBib($sportCode, $gameId)
    {
        response()->json(DB::table($sportCode.'_'.$gameId.'_bibs')->leftJoin('users', $sportCode.'_'.$gameId.'_bibs.u_id', '=', 'users.u_id')->leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->leftJoin('countries', 'users.nationality', '=', 'countries.country_code')->leftJoin('tribes', 'users.indigenous_tribe_id', '=', 'tribes.tribe_id')->leftJoin('sport_lists', 'users.gifited_sport_id', '=', 'sport_lists.sport_id')->leftJoin('cities', 'users.household_city_code', '=', 'cities.city_code')->select('users.*', 'organizations.*', 'departments.*', 'countries.*', 'tribes.*', 'sport_lists.*', 'cities.*')->get());
    }
}
