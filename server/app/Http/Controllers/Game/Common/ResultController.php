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
        $tempArray = array_merge(json_decode(json_encode($tempIndividual), true), json_decode(json_encode($tempGroup), true));
        return response()->json($userArray);
    }
}
