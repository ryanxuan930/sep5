<?php

namespace App\Http\Controllers;

use App\Models\SchoolTeam;
use Illuminate\Http\Request;

class SchoolTeamController extends Controller
{
    // construct
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['index', 'show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($org_id)
    {
        if ($org_id == 1) {
            return response()->json(SchoolTeam::all());
        } else {
            return response()->json(SchoolTeam::where('admin_org_id', $org_id)->get());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $org_id)
    {
        $validator = Validator::make($request->all(),[
            'team_name_ch' => 'required',
            'team_name_en' => 'required',
            'sport_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['created_at'] = date("Y-m-d H:i:s");
        $temp['updated_at'] = date("Y-m-d H:i:s");
        SchoolTeam::insert($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SchoolTeam  $schoolTeam
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolTeam $schoolTeam)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SchoolTeam  $schoolTeam
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SchoolTeam $schoolTeam)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SchoolTeam  $schoolTeam
     * @return \Illuminate\Http\Response
     */
    public function destroy(SchoolTeam $schoolTeam)
    {
        //
    }
}
