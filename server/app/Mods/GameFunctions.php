<?php

namespace App\Mods;

class GameFunctions {

    public static function gameParamsValidation(string $module){
        switch($module) {
            case 'ln':
                return [
                    '*.division_id' => 'required|integer',
                    '*.event_code' => 'required|string',
                    '*.r1' => 'required|boolean',
                    '*.r1_aq' => 'required|integer',
                    '*.r1_sq' => 'required|integer',
                    '*.r1_split' => 'required|boolean',
                    '*.r2' => 'required|boolean',
                    '*.r2_aq' => 'required|integer',
                    '*.r2_sq' => 'required|integer',
                    '*.r2_split' => 'required|boolean',
                    '*.r3' => 'required|boolean',
                    '*.r3_aq' => 'required|integer',
                    '*.r3_sq' => 'required|integer',
                    '*.r3_split' => 'required|boolean',
                    '*.r4' => 'required|boolean',
                    '*.r4_aq' => 'required|integer',
                    '*.r4_sq' => 'required|integer',
                    '*.r4_split' => 'required|boolean',
                ];
            break;
            case 'ge':
                return [
                    '*.division_id' => 'required|integer',
                    '*.event_code' => 'required|string',
                    '*.locked_ip' => 'nullable',
                    '*.locked_admin' => 'nullable',
                    '*.data' => 'nullable',
                ];
                break;
                case 'rd':
                    return [
                        '*.division_id' => 'required|integer',
                        '*.event_code' => 'required|string',
                        '*.qualified' => 'integer',
                        '*.options' => 'nullable',
                    ];
                    break;
        }
    }

    public static function gameIndividualValidation(string $module){
        switch($module) {
            case 'ln':
                return [
                    '*.division_id' => 'required|integer',
                    '*.event_code' => 'required|string',
                    '*.ref_result' => 'required|string',
                    '*.r1_heat' => 'required|integer',
                    '*.r1_lane' => 'required|integer',
                    '*.r1_result' => 'required',
                    '*.r1_ranking' => 'required|boolean',
                    '*.r2_heat' => 'required|integer',
                    '*.r2_lane' => 'required|integer',
                    '*.r2_result' => 'required',
                    '*.r2_ranking' => 'required|integer',
                    '*.r3_heat' => 'required|integer',
                    '*.r3_lane' => 'required|integer',
                    '*.r3_result' => 'required',
                    '*.r3_ranking' => 'required|integer',
                    '*.r4_heat' => 'required|integer',
                    '*.r4_lane' => 'required|integer',
                    '*.r4_result' => 'required',
                    '*.r4_ranking' => 'required|integer',
                ];
            break;
            case 'ge':
                return [
                    '*.division_id' => 'required|integer',
                    '*.event_code' => 'required|string',
                    '*.ini_group' => 'required|integer',
                    '*.ini_position' => 'required|integer',
                    '*.options' => 'nullable',
                ];
                break;
                case 'rd':
                    return [
                        '*.division_id' => 'required|integer',
                        '*.event_code' => 'required|string',
                        '*.bib' => 'required|string',
                        '*.ref_result' => 'required|string',
                        '*.result' => 'required',
                        '*.ranking' => 'required|integer',
                        '*.options' => 'nullable',
                    ];
                    break;
        }
    }
}

?>