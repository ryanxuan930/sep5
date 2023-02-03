<?php

namespace App\Http\Controllers\Game\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

class ParamsController extends Controller
{
    private $tableName = 'params';
    // construct
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['getters']]);
    }
    /**
     * Display a listing of the resource.
     * 
     * @param  string $sportCode
     * @param  string $orgId
     * @return \Illuminate\Http\Response
     */
    public function getters($sportCode, $gameId)
    {
        return response()->json(DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->get());
    }
    /**
     * Set a series of resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $sportCode
     * @param  string $orgId
     * @return \Illuminate\Http\Response
     */
    public function setters(Request $request, $sportCode, $gameId)
    {
        // validation
        $validator = Validator::make($request->all(),[
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
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->truncate();
        DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->insert($temp);
        return response()->json(['status'=>'A01']);
    }
}
