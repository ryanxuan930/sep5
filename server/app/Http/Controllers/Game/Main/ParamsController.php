<?php

namespace App\Http\Controllers\Game\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\SportList;
use App\Mods\GameFunctions;

class ParamsController extends Controller
{
    private $tableName = 'params';
    // construct
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['getters', 'gettersFull']]);
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
    public function gettersFull($sportCode, $gameId)
    {
        return response()->json(DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->leftJoin($sportCode.'_'.$gameId.'_divisions', $sportCode.'_'.$gameId.'_divisions.division_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id')->leftJoin('events', 'events.event_code', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code')->select($sportCode.'_'.$gameId.'_divisions.*', 'events.*')->get());
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
            '*.event_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->truncate();
        DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->insert($temp);
        return response()->json(['status'=>'A01']);
    }
    /**
     * update a series of resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $sportCode
     * @param  string $orgId
     * @return \Illuminate\Http\Response
     */
    public function patcher(Request $request, $sportCode, $gameId)
    {
        // get sport module
        $sportData = SportList::where('sport_code', $sportCode)->first();
        if ($sportData->module == 'ln') {
            // validation
            $validator = Validator::make($request->all(),[
                '*.division_id' => 'required|integer',
                '*.event_code' => 'required',
                '*.r1' => 'required|boolean',
                '*.r1_aq' => 'required|integer',
                '*.r1_sq' => 'required|integer',
                '*.r1_split' => 'required|integer',
                '*.r2' => 'required|boolean',
                '*.r2_aq' => 'required|integer',
                '*.r2_sq' => 'required|integer',
                '*.r2_split' => 'required|integer',
                '*.r3' => 'required|boolean',
                '*.r3_aq' => 'required|integer',
                '*.r3_sq' => 'required|integer',
                '*.r3_split' => 'required|integer',
                '*.r4' => 'required|boolean',
                '*.r4_aq' => 'required|integer',
                '*.r4_sq' => 'required|integer',
                '*.r4_split' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            $temp = $request->all();
            foreach ($temp as $value) {
                $data = $value;
                unset($data['division_id']);
                unset($data['event_code']);
                DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->where('dividion_id', $value->division_id)->where('event_code', $value->event_code)->update($data);
            }
        }
    }
}
