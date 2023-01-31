<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Organization;

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
        $temp['created_at'] = date("Y-m-d H:i:s");
        $temp['updated_at'] = date("Y-m-d H:i:s");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(SportList::where('org_id', $id)->first());
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
