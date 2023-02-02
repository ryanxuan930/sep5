<?php

namespace App\Http\Controllers;

use App\Models\SchoolTeam;
use Illuminate\Http\Request;
use App\Models\AdminOrganization;

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
            return response()->json(SchoolTeam::where('org_id', $org_id)->get());
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
        $orgData = AdminOrganization::where('admin_org_id', $admin->admin_org_id)->first();
        $temp['created_at'] = date("Y-m-d H:i:s");
        $temp['updated_at'] = date("Y-m-d H:i:s");
        $temp['org_id'] = $orgData->related_user_org_id;
        SchoolTeam::insert($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Display the specified resource.
     *
     * @param  $org_id
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($org_id, $id)
    {
        return response()->json(SchoolTeam::where('school_team_id', $id)->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $org_id
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $org_id, $id)
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
        $temp['updated_at'] = date("Y-m-d H:i:s");
        SchoolTeam::where('school_team_id', $id)->update($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $org_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($org_id, $id)
    {
        SchoolTeam::where('school_team_id', $id)->delete();
        return response()->json(['status'=>'A01']);
    }
}
