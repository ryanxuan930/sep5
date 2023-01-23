<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Validator;
use App\Models\Department;

class DepartmentController extends Controller
{
    // construct
    public function __construct()
    {
        if (is_null(auth('admin')->user()) && is_null(auth('user')->user())) {
            return redirect('/login');
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Department::leftJoin('organizations', 'departments.related_org_id', '=', 'organizations.org_id')->orderBy('related_org_id', 'asc')->orderBy('sort_order', 'asc')->get());
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
            'related_org_id' => 'required|integer|exist:organizations,org_id',
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
     * Display the specified organization resource.
     *
     * @param  int  $id -> org_id
     * @return \Illuminate\Http\Response
     */
    public function showByOrg($id)
    {
        return response()->json(Department::leftJoin('organizations', 'departments.related_org_id', '=', 'organizations.org_id')->where('related_org_id', $id)->orderBy('sort_order', 'asc')->get());
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
            'related_org_id' => 'required|integer|exist:organizations,org_id',
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
        Bulletin::where('dept_id', $id)->delete();
        return response()->json(['status'=>'A01']);
    }
}
