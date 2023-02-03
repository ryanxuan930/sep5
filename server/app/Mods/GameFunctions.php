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
}

?>