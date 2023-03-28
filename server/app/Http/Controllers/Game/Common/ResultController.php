<?php

namespace App\Http\Controllers\Game\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function ranking($sportCode, $gameId)
    {
        $userArray = array();
        $tempIndividual = DB::table($sportCode.'_'.$gameId.'_individuals')->leftJoin('users', 'users.u_id', '=', $sportCode.'_'.$gameId.'_individuals.u_id')->leftJoin('organizations', 'organizations.org_code', '=', 'users.org_code')->leftJoin('departments', 'departments.dept_id', '=', 'users.dept_id')->select('users.org_code', 'users.dept_id', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en', $sportCode.'_'.$gameId.'_individuals.r4_ranking', DB::raw('count(*) as count'))->where($sportCode.'_'.$gameId.'_individuals.r4_ranking', '<=', 8)->where($sportCode.'_'.$gameId.'_individuals.r4_ranking', '>', 0)->groupBy('users.org_code', 'users.dept_id', $sportCode.'_'.$gameId.'_individuals.r4_ranking')->get();
        $tempGroup = DB::table($sportCode.'_'.$gameId.'_groups')->leftJoin($sportCode.'_'.$gameId.'_teams', $sportCode.'_'.$gameId.'_groups.team_id', '=', $sportCode.'_'.$gameId.'_teams.team_id')->leftJoin('organizations', 'organizations.org_id', '=', $sportCode.'_'.$gameId.'_teams.org_id')->leftJoin('departments', 'departments.dept_id', '=', $sportCode.'_'.$gameId.'_teams.dept_id')->select($sportCode.'_'.$gameId.'_teams.org_id', $sportCode.'_'.$gameId.'_teams.dept_id', 'organizations.org_code', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en', $sportCode.'_'.$gameId.'_groups.r4_ranking', DB::raw('count(*) as count'))->where($sportCode.'_'.$gameId.'_groups.r4_ranking', '<=', 8)->where($sportCode.'_'.$gameId.'_groups.r4_ranking', '>', 0)->groupBy($sportCode.'_'.$gameId.'_teams.org_id', $sportCode.'_'.$gameId.'_teams.dept_id', $sportCode.'_'.$gameId.'_groups.r4_ranking')->get();
        for ($i = 0; $i < count($tempIndividual); $i++) {
            for ($j = 0; $j < count($tempGroup); $j++) {
                if ($tempIndividual[$i]->org_code == $tempGroup[$j]->org_code && $tempIndividual[$i]->dept_id == $tempGroup[$j]->dept_id && $tempIndividual[$i]->r4_ranking == $tempGroup[$j]->r4_ranking) {
                    $tempIndividual[$i]->count += $tempGroup[$j]->count;
                    array_splice($tempGroup, $j, 1);
                    break;
                }
            }
        }
        $userArray = array_merge(json_decode(json_encode($tempIndividual), true), json_decode(json_encode($tempGroup), true));
        $col1 = array_column($userArray, 'org_code');
        $col2 = array_column($userArray, 'dept_id');
        $col3 = array_column($userArray, 'r4_ranking');
        array_multisort($userArray, SORT_ASC, $col1, SORT_ASC, $col2, SORT_ASC, $col3, SORT_ASC);
        return response()->json($userArray);
    }

    public function result($sportCode, $gameId)
    {
        $userArray = array();
        $tempIndividual = DB::table($sportCode.'_'.$gameId.'_individuals')->leftJoin('users', 'users.u_id', '=', $sportCode.'_'.$gameId.'_individuals.u_id')->leftJoin($sportCode.'_'.$gameId.'_divisions', $sportCode.'_'.$gameId.'_individuals.division_id', '=', $sportCode.'_'.$gameId.'_divisions.division_id')->leftJoin('events', $sportCode.'_'.$gameId.'_individuals.event_code', '=', 'events.event_code')->leftJoin('organizations', 'organizations.org_code', '=', 'users.org_code')->leftJoin('departments', 'departments.dept_id', '=', 'users.dept_id')->select('users.org_code', 'users.dept_id', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en', $sportCode.'_'.$gameId.'_individuals.r4_result', $sportCode.'_'.$gameId.'_individuals.r4_ranking', $sportCode.'_'.$gameId.'_individuals.r4_options', 'users.last_name_ch', 'users.first_name_ch', 'users.last_name_en', 'users.first_name_en', 'users.num_in_dept', $sportCode.'_'.$gameId.'_divisions.division_id', $sportCode.'_'.$gameId.'_divisions.division_ch', $sportCode.'_'.$gameId.'_divisions.division_en', 'events.event_ch', 'events.event_en', 'events.event_code', 'events.event_id')->where($sportCode.'_'.$gameId.'_individuals.r4_ranking', '<=', 8)->where($sportCode.'_'.$gameId.'_individuals.r4_ranking', '>', 0)->get();
        $tempGroup = DB::table($sportCode.'_'.$gameId.'_groups')->leftJoin($sportCode.'_'.$gameId.'_teams', $sportCode.'_'.$gameId.'_groups.team_id', '=', $sportCode.'_'.$gameId.'_teams.team_id')->leftJoin($sportCode.'_'.$gameId.'_divisions', $sportCode.'_'.$gameId.'_groups.division_id', '=', $sportCode.'_'.$gameId.'_divisions.division_id')->leftJoin('events', $sportCode.'_'.$gameId.'_groups.event_code', '=', 'events.event_code')->leftJoin('organizations', 'organizations.org_id', '=', $sportCode.'_'.$gameId.'_teams.org_id')->leftJoin('departments', 'departments.dept_id', '=', $sportCode.'_'.$gameId.'_teams.dept_id')->select($sportCode.'_'.$gameId.'_teams.org_id', $sportCode.'_'.$gameId.'_teams.dept_id', 'organizations.org_code', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en', $sportCode.'_'.$gameId.'_teams.team_name', $sportCode.'_'.$gameId.'_groups.r4_result', $sportCode.'_'.$gameId.'_groups.r4_ranking', $sportCode.'_'.$gameId.'_groups.r4_options', $sportCode.'_'.$gameId.'_divisions.division_ch', $sportCode.'_'.$gameId.'_divisions.division_en', $sportCode.'_'.$gameId.'_divisions.division_id', 'events.event_ch', 'events.event_en', 'events.event_code', 'events.event_id')->where($sportCode.'_'.$gameId.'_groups.r4_ranking', '<=', 8)->where($sportCode.'_'.$gameId.'_groups.r4_ranking', '>', 0)->get();
        $userArray = array_merge(json_decode(json_encode($tempIndividual), true), json_decode(json_encode($tempGroup), true));
        $col1 = array_column($userArray, 'division_id');
        $col2 = array_column($userArray, 'event_id');
        $col3 = array_column($userArray, 'r4_ranking');
        array_multisort($userArray, SORT_ASC, $col1, SORT_ASC, $col2, SORT_ASC, $col3, SORT_ASC);
        return response()->json($userArray);
    }
}
