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
    public function gettersFull($sportCode, $gameId)
    {
        return response()->json(DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->leftJoin($sportCode.'_'.$gameId.'_divisions', $sportCode.'_'.$gameId.'_divisions.dividion_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id')->leftJoin('events', 'events.event_code', '-', $sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code')->get());
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

    }
}
