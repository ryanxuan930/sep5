<?php

namespace App\Http\Controllers;

use App\Models\ConsentForm;
use Illuminate\Http\Request;
use App\Models\ConsentForm as CF;
use Validator;

class ConsentFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(CF::leftJoin('users', 'users.u_id', '=', 'consent_forms.u_id')
        ->leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')
        ->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->select('consent_forms.*', 'users.u_id', 'users.first_name_ch', 'users.last_name_ch', 'users.first_name_en', 'users.last_name_en', 'users.org_code', 'users.dept_id', 'users.sex', 'users.num_in_dept', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en')->get());
    }

    public function indexByGame($gameId)
    {
        return response()->json(CF::leftJoin('users', 'users.u_id', '=', 'consent_forms.u_id')
        ->leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')
        ->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->select('consent_forms.*', 'users.u_id', 'users.first_name_ch', 'users.last_name_ch', 'users.first_name_en', 'users.last_name_en', 'users.org_code', 'users.dept_id', 'users.sex', 'users.num_in_dept', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en')->where('game_id', $gameId)->orderBy('u_id', 'asc')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $validator = Validator::make($request->all(),[
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
        CF::insert($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ConsentForm  $consentForm
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(CF::leftJoin('users', 'users.u_id', '=', 'consent_forms.u_id')
        ->leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')
        ->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->select('consent_forms.*', 'users.u_id', 'users.first_name_ch', 'users.last_name_ch', 'users.first_name_en', 'users.last_name_en', 'users.org_code', 'users.dept_id', 'users.sex', 'users.num_in_dept', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en')->where('consent_form_id', $id)->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ConsentForm  $consentForm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $validator = Validator::make($request->all(),[
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
        CF::where('consent_form_id', $id)->update($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ConsentForm  $consentForm
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        CF::where('consent_form_id', $id)->delete();
        return response()->json(['status'=>'A01']);
    }
}
