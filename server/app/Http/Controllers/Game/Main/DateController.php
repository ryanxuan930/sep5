<?php

namespace App\Http\Controllers\Game\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DateController extends Controller
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
    public function getters()
    {
        return response()->json(DB::table($sportCode.'_'.$orgId.'_dates')->all());
    }
    /**
     * Set a series of resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $sportCode
     * @param  string $orgId
     * @return \Illuminate\Http\Response
     */
    public function setters(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(),[
            '*.date' => 'required|date',
            '*.day_ch' => 'nullable',
            '*.day_en' => 'nullable'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        DB::table($sportCode.'_'.$orgId.'_dates')->truncate();
        DB::table($sportCode.'_'.$orgId.'_dates')->insert;
        return response()->json(['status'=>'A01']);
    }
}
