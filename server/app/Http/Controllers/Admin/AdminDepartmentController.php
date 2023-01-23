<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Validator;
use App\Models\AdminDepartment as AD;

class AdminDepartmentController extends Controller
{
    // construct
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(AD::all());
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
            'admin_dept_name_ch' => 'required',
            'admin_dept_name_en' => 'required',
            'sport_management_list' => 'nullable',
            'remarks' => 'nullable',
            'admin_org_id' => 'integer',
        ]);
        if ($validator->fails()) {
            /*
            $failedRules = $validator->failed();
            if (isset($failedRules['account']['Required'])) {
                return response()->json(['status' => 'U07', 'message' => '請輸入App名稱'], 200);
            } else if (isset($failedRules['password']['Required'])) {
                return response()->json(['status' => 'U07', 'message' => '請輸入網域'], 200);
            }*/
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['created_at'] = date("Y-m-d H:i:s");
        $temp['updated_at'] = date("Y-m-d H:i:s");
        AD::insert($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(AD::leftJoin('admin_organizations', 'admin_departments.admin_org_id', '=', 'admin_organizations.admin_org_id')->where('admin_dept_id', $id)->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'admin_dept_name_ch' => 'required',
            'admin_dept_name_en' => 'required',
            'sport_management_list' => 'nullable',
            'remarks' => 'nullable',
            'admin_org_id' => 'interger',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['updated_at'] = date("Y-m-d H:i:s");
        AD::where('admin_dept_id', $id)->update($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AD::where('admin_dept_id', $id)->delete();
        return response()->json(['status'=>'A01']);
    }
}
