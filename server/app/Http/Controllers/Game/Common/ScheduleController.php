<?php

namespace App\Http\Controllers\Game\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\SportList;
use App\Mods\GameFunctions;

class ScheduleController extends Controller
{
    private $tableName = 'schedule';
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
        return response()->json(DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->leftJoin($sportCode.'_'.$gameId.'_divisions', $sportCode.'_'.$gameId.'_divisions.division_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id')->leftJoin('events', 'events.event_code', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code')->select($sportCode.'_'.$gameId.'_'.$this->tableName.'.*', $sportCode.'_'.$gameId.'_divisions.*', 'events.*')->get());
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
        // get sport module
        $sportData = SportList::where('sport_code', $sportCode)->first();
        if ($sportData->module == 'ln') {
            // validation
            $validator = Validator::make($request->all(),[
                '*.division_id' => 'required|integer',
                '*.event_code' => 'required',
                '*.timestamp' => 'required',
                '*.round' => 'required|integer',
                '*.status' => 'required|integer',
                '*.options' => 'nullable',
            ]);
        } else if ($sportData->module == 'rd') {
            // validation
            $validator = Validator::make($request->all(),[
                '*.division_id' => 'required|integer',
                '*.event_code' => 'required',
                '*.timestamp' => 'required',
                '*.status' => 'required|integer',
                '*.options' => 'nullable',
            ]);
        } else {
            $validator = Validator::make($request->all(),[
                '*.division_id' => 'required|integer',
                '*.event_code' => 'required',
                '*.timestamp' => 'required',
                '*.round' => 'required|integer',
                '*.session' => 'required|integer',
                '*.status' => 'required|integer',
                '*.options' => 'nullable',
            ]);
        }
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
    public function patcher(Request $request, $sportCode, $gameId, $id) 
    {
        $validator = Validator::make($request->all(),[
            'status' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->where('schedule_id', $id)->update($temp);
        return response()->json(['status'=>'A01']);
    }
}
