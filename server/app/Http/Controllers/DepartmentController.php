<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Validator;
use App\Models\Department;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        return response()->json(Department::leftJoin('organizations', 'departments.related_org_id', '=', 'organizations.org_id')->orderBy('related_org_id', 'asc')->orderBy('sort_order', 'asc')->get());
    }

    public function indexByOrg($org_id)
    {
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        return response()->json(Department::leftJoin('organizations', 'departments.related_org_id', '=', 'organizations.org_id')->where('related_org_id', $org_id)->orderBy('sort_order', 'asc')->get());
    }

    public function indexByOrgCode($org_code)
    {
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        return response()->json(Department::leftJoin('organizations', 'departments.related_org_id', '=', 'organizations.org_id')->where('organizations.org_code', $org_code)->orderBy('sort_order', 'asc')->get());
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
            'related_org_id' => 'required|integer|exists:organizations,org_id',
            'dept_name_ch' => 'required',
            'dept_name_en' => 'nullable',
            'sort_order' => 'integer',
            'has_grade' => 'required|boolean',
            'grade' => 'required|integer',
            'options' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        Department::insert($temp);
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
        return response()->json(Department::leftJoin('organizations', 'departments.related_org_id', '=', 'organizations.org_id')->where('dept_id', $id)->first());
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
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $validator = Validator::make($request->all(),[
            'related_org_id' => 'required|integer|exists:organizations,org_id',
            'dept_name_ch' => 'required',
            'dept_name_en' => 'nullable',
            'sort_order' => 'integer',
            'has_grade' => 'required|boolean',
            'grade' => 'required|integer',
            'options' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        Department::where('dept_id', $id)->update($temp);
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
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        Department::where('dept_id', $id)->delete();
        return response()->json(['status'=>'A01']);
    }
}
