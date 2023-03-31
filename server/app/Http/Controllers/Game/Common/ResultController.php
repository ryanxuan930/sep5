<?php

namespace App\Http\Controllers\Game\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function ranking($sportCode, $gameId, $num = 8)
    {
        $userArray = array();
        $num = intval($num);
        $tempIndividual = DB::table($sportCode.'_'.$gameId.'_individuals')->leftJoin($sportCode.'_'.$gameId.'_divisions', $sportCode.'_'.$gameId.'_individuals.division_id', '=', $sportCode.'_'.$gameId.'_divisions.division_id')->leftJoin('events', $sportCode.'_'.$gameId.'_individuals.event_code', '=', 'events.event_code')->leftJoin('users', 'users.u_id', '=', $sportCode.'_'.$gameId.'_individuals.u_id')->leftJoin('organizations', 'organizations.org_code', '=', 'users.org_code')->leftJoin('departments', 'departments.dept_id', '=', 'users.dept_id')->select('users.org_code', 'users.dept_id', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en', $sportCode.'_'.$gameId.'_divisions.division_ch', $sportCode.'_'.$gameId.'_divisions.division_en', $sportCode.'_'.$gameId.'_divisions.division_id', 'events.event_ch', 'events.event_en', 'events.event_code', 'events.event_id', $sportCode.'_'.$gameId.'_individuals.r4_ranking', DB::raw('count(*) as count'))->where($sportCode.'_'.$gameId.'_individuals.r4_ranking', '<=', $num)->where($sportCode.'_'.$gameId.'_individuals.r4_ranking', '>', 0)->groupBy('users.org_code', 'users.dept_id', $sportCode.'_'.$gameId.'_individuals.r4_ranking')->get();
        $tempIndividual = json_decode(json_encode($tempIndividual), true, 512, JSON_BIGINT_AS_STRING);
        $tempGroup = DB::table($sportCode.'_'.$gameId.'_groups')->leftJoin($sportCode.'_'.$gameId.'_teams', $sportCode.'_'.$gameId.'_groups.team_id', '=', $sportCode.'_'.$gameId.'_teams.team_id')->leftJoin($sportCode.'_'.$gameId.'_divisions', $sportCode.'_'.$gameId.'_groups.division_id', '=', $sportCode.'_'.$gameId.'_divisions.division_id')->leftJoin('events', $sportCode.'_'.$gameId.'_groups.event_code', '=', 'events.event_code')->leftJoin('organizations', 'organizations.org_id', '=', $sportCode.'_'.$gameId.'_teams.org_id')->leftJoin('departments', 'departments.dept_id', '=', $sportCode.'_'.$gameId.'_teams.dept_id')->select($sportCode.'_'.$gameId.'_teams.org_id', $sportCode.'_'.$gameId.'_teams.dept_id', 'organizations.org_code', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en', $sportCode.'_'.$gameId.'_divisions.division_ch', $sportCode.'_'.$gameId.'_divisions.division_en', $sportCode.'_'.$gameId.'_divisions.division_id', 'events.event_ch', 'events.event_en', 'events.event_code', 'events.event_id', $sportCode.'_'.$gameId.'_groups.r4_ranking', DB::raw('count(*) as count'))->where($sportCode.'_'.$gameId.'_groups.r4_ranking', '<=', $num)->where($sportCode.'_'.$gameId.'_groups.r4_ranking', '>', 0)->groupBy($sportCode.'_'.$gameId.'_teams.org_id', $sportCode.'_'.$gameId.'_teams.dept_id', $sportCode.'_'.$gameId.'_groups.r4_ranking')->get();
        $tempGroup = json_decode(json_encode($tempGroup), true, 512, JSON_BIGINT_AS_STRING);
        $dataArray = array();
        $dataArray = array_merge($tempIndividual, $tempGroup);
        $col1 = array_column($dataArray, 'org_code');
        $col2 = array_column($dataArray, 'dept_id');
        $col3 = array_column($dataArray, 'r4_ranking');
        array_multisort($dataArray, SORT_ASC, $col1, SORT_ASC, $col2, SORT_ASC, $col3, SORT_ASC);
        $tempOrg = '';
        $tempDept = '';
        $tempArray = array();
        for ($i = 0; $i < count($dataArray); $i++) {
            if ($dataArray[$i]['org_code'] != $tempOrg || $dataArray[$i]['dept_id'] != $tempDept) {
                $tempOrg = $dataArray[$i]['org_code'];
                $tempDept = $dataArray[$i]['dept_id'];
                array_push($tempArray, $dataArray[$i]);
            }
        }
        print_r($tempArray);
        for ($i = 0; $i < count($tempArray); $i++) {
            for ($j = 0; $j < $num; $j++) {
                $count = 0;
                $flag = false;
                for ($k = 0; $k < count($tempIndividual); $k++) {
                    if ($tempIndividual[$k]['org_code'] == $tempArray[$i]['org_code'] && $tempIndividual[$k]['dept_id'] == $tempArray[$i]['dept_id'] && $tempIndividual[$j]['r4_ranking'] == $j + 1) {
                        $count += $tempIndividual[$k]['count'];
                        $flag = true;
                    }
                }
                for ($k = 0; $k < count($tempGroup); $k++) {
                    if ($tempGroup[$k]['org_code'] == $tempArray[$i]['org_code'] && $tempGroup[$k]['dept_id'] == $tempArray[$i]['dept_id'] && $tempGroup[$j]['r4_ranking'] == $j + 1) {
                        $count += $tempGroup[$k]['count'];
                        $flag = true;
                    }
                }
                if ($flag) {
                    array_push($userArray, [
                        'org_code' => $tempArray[$i]['org_code'],
                        'dept_id' => $tempArray[$i]['dept_id'],
                        'org_name_full_ch' => $tempArray[$i]['org_name_full_ch'],
                        'org_name_ch' => $tempArray[$i]['org_name_ch'],
                        'org_name_full_en' => $tempArray[$i]['org_name_full_en'],
                        'org_name_en' => $tempArray[$i]['org_name_en'],
                        'dept_name_ch' => $tempArray[$i]['dept_name_ch'],
                        'dept_name_en' => $tempArray[$i]['dept_name_en'],
                        'division_ch' => $tempArray[$i]['division_ch'],
                        'division_en' => $tempArray[$i]['division_en'],
                        'division_id' => $tempArray[$i]['division_id'],
                        'event_ch' => $tempArray[$i]['event_ch'],
                        'event_en' => $tempArray[$i]['event_en'],
                        'event_code' => $tempArray[$i]['event_code'],
                        'event_id' => $tempArray[$i]['event_id'],
                        'r4_ranking' => $j + 1,
                        'count' => $count,
                    ]);
                }
            }

        }
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
