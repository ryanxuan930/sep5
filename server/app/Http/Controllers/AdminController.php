<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Admin;

class AdminController extends Controller
{
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
        $admin = auth('admin')->user();
        $query = Admin::leftJoin('admin_organizations', 'admin_organizations.admin_org_id', '=', 'admins.admin_org_id')->leftJoin('admin_departments', 'admin_departments.admin_dept_id', '=', 'admins.admin_dept_id')->select('admins.*', 'admin_organizations.*', 'admin_departments.*');
        if ($admin->admin_org_id == 1) {
            return response()->json($query->paginate(25));
        } else {
            return response()->json($query->where('admin_org_id', $admin->admin_org_id)->paginate(25));
        }
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
            'account' => 'required|unique:users,account',
            'password' => 'required',
            'name' => 'string|required',
            'permission' => 'integer|required',
            'admin_org_id' => 'integer|required',
            'admin_dept_id' => 'integer|required',
            'options' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $loginTime = date("Y-m-d H:i:s");
        $temp = $request->all();
        $temp['created_at'] = $loginTime;
        $temp['updated_at'] = $loginTime;
        $temp['last_ip'] = $request->ip();
        $temp['password'] = password_hash($request->all()['password'], PASSWORD_DEFAULT);
        Admin::insert($temp);
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
        return response()->json(Admin::leftJoin('admin_organizations', 'admin_organizations.admin_org_id', '=', 'admins.admin_org_id')->leftJoin('admin_departments', 'admin_departments.admin_dept_id', '=', 'admins.admin_dept_id')->select('admins.*', 'admin_organizations.*', 'admin_departments.*')->where('admin_id',$id)->first());
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
            'name' => 'string|required',
            'permission' => 'integer|required',
            'admin_org_id' => 'integer|required',
            'admin_dept_id' => 'integer|required',
            'options' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $loginTime = date("Y-m-d H:i:s");
        $temp = $request->all();
        $temp['updated_at'] = $loginTime;
        $temp['last_ip'] = $request->ip();
        Admin::where('admin_id', $id)->update($temp);
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
        Admin::where('admin_id', $id)->delete();
    }
}
