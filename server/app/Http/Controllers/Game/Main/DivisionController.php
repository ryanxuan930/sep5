<?php

namespace App\Http\Controllers\Game\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
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
        return response()->json(DB::table($sportCode.'_'.$gameId.'_division')->get());
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
            '*.sex' => 'required|integer',
            '*.division_ch' => 'required',
            '*.division_en' => 'nullable'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        DB::table($sportCode.'_'.$gameId.'_division')->truncate();
        DB::table($sportCode.'_'.$gameId.'_division')->insert($temp);
        return response()->json(['status'=>'A01']);
    }
}
