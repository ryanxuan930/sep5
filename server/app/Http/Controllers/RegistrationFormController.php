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
        return response()->json(RF::leftJoin('organizations', 'registration_forms.org_id', '=', 'organizations.org_id')->leftJoin('departments', 'registration_forms.dept_id', '=', 'departments.dept_id')->leftJoin('users', 'registration_forms.u_id', '=', 'users.u_id')->select('registration_forms.*', 'users.first_name_ch', 'users.last_name_ch', 'users.first_name_en', 'users.last_name_en', 'users.org_code', 'users.dept_id', 'users.sex', 'users.num_in_dept', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en')->get());
    }
    public function indexByGame($gameId)
    {
        return response()->json(RF::leftJoin('organizations', 'registration_forms.org_id', '=', 'organizations.org_id')->leftJoin('departments', 'registration_forms.dept_id', '=', 'departments.dept_id')->leftJoin('users', 'registration_forms.u_id', '=', 'users.u_id')->select('registration_forms.*', 'users.first_name_ch', 'users.last_name_ch', 'users.first_name_en', 'users.last_name_en', 'users.org_code', 'users.dept_id', 'users.sex', 'users.num_in_dept', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en')->where('game_id', $gameId)->orderBy('org_id', 'asc')->orderBy('dept_id', 'asc')->orderBy('u_id', 'asc')->get());
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
        $query = RF::leftJoin('organizations', 'registration_forms.org_id', '=', 'organizations.org_id')->leftJoin('departments', 'registration_forms.dept_id', '=', 'departments.dept_id')->leftJoin('users', 'registration_forms.u_id', '=', 'users.u_id')->select('registration_forms.*', 'users.first_name_ch', 'users.last_name_ch', 'users.first_name_en', 'users.last_name_en', 'users.org_code', 'users.dept_id', 'users.sex', 'users.num_in_dept', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en')->where('game_id', $id)->where('org_id', $orgId);
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
