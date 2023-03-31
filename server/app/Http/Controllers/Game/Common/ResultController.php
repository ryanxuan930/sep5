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
        /*
        $groups = [];
        for ($i = 0; $i < count($tempIndividual); $i++) {
            for ($j = 0; $j < count($tempGroup); $j++) {
                if ($tempIndividual[$i]['org_code'] == $tempGroup[$j]['org_code'] && $tempIndividual[$i]['dept_id'] == $tempGroup[$j]['dept_id']) {
                    if ($tempIndividual[$i]['r4_ranking'] == $tempGroup[$j]['r4_ranking']) {
                        $tempIndividual[$i]['count'] += $tempGroup[$j]['count'];
                    } else {
                        $tempIndividual[$i]['count'] = $tempGroup[$j]['count'];
                    }
                    continue;
                } else {
                    array_push($groups, $tempGroup[$j]);
                }
            }
        }
        $userArray = array_merge($tempIndividual, $groups);
        $col1 = array_column($userArray, 'org_code');
        $col2 = array_column($userArray, 'dept_id');
        $col3 = array_column($userArray, 'r4_ranking');
        array_multisort($userArray, SORT_ASC, $col1, SORT_ASC, $col2, SORT_ASC, $col3, SORT_ASC);*/
        $dataArray = array();
        for ($i = 0; $i < count($tempIndividual); $i++) {
            array_push($dataArray, [
                'org_code' => $tempIndividual[$i]['org_code'],
                'dept_id' => $tempIndividual[$i]['dept_id'],
                'org_name_full_ch' => $tempIndividual[$i]['org_name_full_ch'],
                'org_name_ch' => $tempIndividual[$i]['org_name_ch'],
                'org_name_full_en' => $tempIndividual[$i]['org_name_full_en'],
                'org_name_en' => $tempIndividual[$i]['org_name_en'],
                'dept_name_ch' => $tempIndividual[$i]['dept_name_ch'],
                'dept_name_en' => $tempIndividual[$i]['dept_name_en'],
                'division_ch' => $tempIndividual[$i]['division_ch'],
                'division_en' => $tempIndividual[$i]['division_en'],
                'division_id' => $tempIndividual[$i]['division_id'],
                'event_ch' => $tempIndividual[$i]['event_ch'],
                'event_en' => $tempIndividual[$i]['event_en'],
                'event_code' => $tempIndividual[$i]['event_code'],
                'r4_ranking' => $tempIndividual[$i]['r4_ranking'],
                'count' => $tempIndividual[$i]['count'],
            ]);
        }
        for( $i = 0; $i < count($tempGroup); $i++ ) {
            array_push($dataArray, [
                'org_code' => $tempGroup[$i]['org_code'],
                'dept_id' => $tempGroup[$i]['dept_id'],
                'org_name_full_ch' => $tempGroup[$i]['org_name_full_ch'],
                'org_name_ch' => $tempGroup[$i]['org_name_ch'],
                'org_name_full_en' => $tempGroup[$i]['org_name_full_en'],
                'org_name_en' => $tempGroup[$i]['org_name_en'],
                'dept_name_ch' => $tempGroup[$i]['dept_name_ch'],
                'dept_name_en' => $tempGroup[$i]['dept_name_en'],
                'division_ch' => $tempGroup[$i]['division_ch'],
                'division_en' => $tempGroup[$i]['division_en'],
                'division_id' => $tempGroup[$i]['division_id'],
                'event_ch' => $tempGroup[$i]['event_ch'],
                'event_en' => $tempGroup[$i]['event_en'],
                'event_code' => $tempGroup[$i]['event_code'],
                'r4_ranking' => $tempGroup[$i]['r4_ranking'],
                'count' => $tempGroup[$i]['count'],
            ]);
        }
        $col1 = array_column($dataArray, 'org_code');
        $col2 = array_column($dataArray, 'dept_id');
        $col3 = array_column($dataArray, 'r4_ranking');
        array_multisort($dataArray, SORT_ASC, $col1, SORT_ASC, $col2, SORT_ASC, $col3, SORT_ASC);
        $tempOrg = '';
        $tempDept = '';
        $tempRank = 0;
        $index = -1;
        $userArray = array();
        print_r($dataArray);
        for ($i = 0; $i < count($dataArray); $i++) {
            if ($dataArray[$i]['org_code'] != $tempOrg && $dataArray[$i]['dept_id'] != $tempDept) {
                $index++;
                $tempOrg = $dataArray[$i]['org_code'];
                $tempDept = $dataArray[$i]['dept_id'];
                $tempRank = $dataArray[$i]['r4_ranking'];
                array_push($userArray, $dataArray[$i]);
            }
            $userArray[$index]['count'] += $dataArray[$i]['count'];
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
