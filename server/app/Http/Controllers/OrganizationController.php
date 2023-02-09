<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Organization;
use App\Models\Department;
use App\Models\OrganizationTemp;
use App\Models\User;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Organization::all());
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
            'org_type' => 'required|integer',
            'org_name_full_ch' => 'required',
            'org_name_ch' => 'required',
            'org_name_full_en' => 'nullable',
            'org_name_en' => 'nullable',
            'logo_imge' => 'nullable',
            'union_id' => 'nullable',
            'country_code' => 'required|size:2',
            'zipcode' => 'nullable',
            'city_code' => 'nullable',
            'address' => 'nullable',
            'telephone' => 'nullable',
            'telephone_ex' => 'nullable',
            'fax' => 'nullable',
            'contact_name' => 'nullable',
            'contact_email' => 'nullable',
            'homepage' => 'nullable',
            'links' => 'nullable',
            'chairman_ch' => 'nullable',
            'chairman_en' => 'nullable',
            'chairman_image' => 'nullable',
            'leader_ch' => 'nullable',
            'leader_en' => 'nullable',
            'leader_image' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $orgTemp = OrganizationTemp::where('org_type', $temp['org_type'])->first();
        $orgCount = $orgTemp->count + 1;
        $temp['org_code'] = $orgTemp->org_prefix.str_pad($orgCount, 4, '0', STR_PAD_LEFT);
        OrganizationTemp::where('org_type', $temp['org_type'])->update(['count' => $orgCount]);
        $temp['created_at'] = date("Y-m-d H:i:s");
        $temp['updated_at'] = date("Y-m-d H:i:s");
        Organization::insert($temp);
        return response()->json(['status'=>'A01']);
    }
    public function create(Request $request)
    {
        if (is_null(auth('user')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $validator = Validator::make($request->all(),[
            'org_type' => 'required|integer',
            'org_name_full_ch' => 'required',
            'org_name_ch' => 'required',
            'org_name_full_en' => 'nullable',
            'org_name_en' => 'nullable',
            'logo_imge' => 'nullable',
            'union_id' => 'nullable',
            'country_code' => 'required|size:2',
            'zipcode' => 'nullable',
            'city_code' => 'nullable',
            'address' => 'nullable',
            'telephone' => 'nullable',
            'telephone_ex' => 'nullable',
            'fax' => 'nullable',
            'contact_name' => 'nullable',
            'contact_email' => 'nullable',
            'homepage' => 'nullable',
            'links' => 'nullable',
            'chairman_ch' => 'nullable',
            'chairman_en' => 'nullable',
            'chairman_image' => 'nullable',
            'leader_ch' => 'nullable',
            'leader_en' => 'nullable',
            'leader_image' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $orgTemp = OrganizationTemp::where('org_type', $temp['org_type'])->first();
        $orgCount = $orgTemp->count + 1;
        $temp['org_code'] = $orgTemp->org_prefix.str_pad($orgCount, 4, '0', STR_PAD_LEFT);
        OrganizationTemp::where('org_type', $temp['org_type'])->update(['count' => $orgCount]);
        $temp['created_at'] = date("Y-m-d H:i:s");
        $temp['updated_at'] = date("Y-m-d H:i:s");
        Organization::insert($temp);
        $orgData = Organization::where('org_code', $temp['org_code'])->select('org_id', 'org_code')->first();
        Department::insert([
            'related_org_id' => $orgData->org_id,
            'dept_name_ch' => $temp['org_name_full_ch'],
            'dept_name_en' => $temp['org_name_full_en'],
            'sort_order' => 0,
            'has_grade' => 0,
            'grade' => 0,
            'options' => null,
            'mutable' => 0,
        ]);
        $deptData = Department::where('related_org_id', $orgData->org_id)->select('dept_id', 'related_org_id')->first();
        $user = User::find(auth('user')->user()->u_id);
        $user->org_code = $temp['org_code'];
        $user->dept_id = $deptData->dept_id;
        $user->permission = 2;
        $user->save();
        return response()->json(['status'=>'A01']);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Organization::where('org_code', $id)->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id (org_code)
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $user = auth('user')->user();
        if (!is_null($user)) {
            if ($user->org_code != $id) {
                return response()->json(['status'=>'E01', 'message'=>'錯誤']);
            }
        }
        $validator = Validator::make($request->all(),[
            'org_type' => 'required|integer',
            'org_name_full_ch' => 'required',
            'org_name_ch' => 'required',
            'org_name_full_en' => 'nullable',
            'org_name_en' => 'nullable',
            'logo_imge' => 'nullable',
            'union_id' => 'nullable',
            'country_code' => 'required|size:2',
            'zipcode' => 'nullable',
            'city_code' => 'nullable',
            'address' => 'nullable',
            'telephone' => 'nullable',
            'telephone_ex' => 'nullable',
            'fax' => 'nullable',
            'contact_name' => 'nullable',
            'contact_email' => 'nullable',
            'homepage' => 'nullable',
            'links' => 'nullable',
            'chairman_ch' => 'nullable',
            'chairman_en' => 'nullable',
            'chairman_image' => 'nullable',
            'leader_ch' => 'nullable',
            'leader_en' => 'nullable',
            'leader_image' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        Organization::where('org_code', $id)->update($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        Organization::where('org_code', $id)->delete();
        return response()->json(['status'=>'A01']);
    }
}
