<?php

namespace App\Http\Controllers;

use App\Models\RegistrationForm as RF;
use Validator;
use Illuminate\Http\Request;

class RegistrationFormController extends Controller
{
    // construct
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['index', 'indexByGame', 'show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(RF::all());
    }
    public function indexByGame($gameId)
    {
        return response()->json(RF::where('game_id', $gameId)->orderBy('org_id', 'asc')->orderBy('dept_id', 'asc')->orderBy('u_id', 'asc')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'org_id' => 'required|integer',
            'dept_id' => 'required|integer',
            'u_id' => 'required|integer',
            'game_id' => 'required|integer',
            'status' => 'required|integer',
            'remarks' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['created_at'] = date("Y-m-d H:i:s");
        $temp['updated_at'] = date("Y-m-d H:i:s");
        RF::insert($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(RF::where('reg_form_id', $id)->get());
    }
    public function showByUnit($gameId, $orgId, $deptId = null, $uId = null)
    {
        $query = RF::where('game_id', $id)->where('org_id', $orgId);
        if (!is_null($uId)) {
            $result = $query->where('dept_id', $deptId)->get();
        } else if (!is_null($deptId)) {
            $result = $query->where('org_id', $orgId)->get();
        } else {
            $result = $query->where('u_id', $uId)->get();
        }
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'org_id' => 'required|integer',
            'dept_id' => 'required|integer',
            'u_id' => 'required|integer',
            'game_id' => 'required|integer',
            'status' => 'required|integer',
            'remarks' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['updated_at'] = date("Y-m-d H:i:s");
        RF::where('reg_form_id', $id)->update($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        RF::where('reg_form_id', $id)->delete();
        return response()->json(['status'=>'A01']);
    }
}
