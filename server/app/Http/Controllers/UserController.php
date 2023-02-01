<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (is_null($user = auth('user')->user()) && is_null($admin = auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        } else if (is_null($admin)) {
            $org_code = $user->org_code;
            return response()->json(User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->leftJoin('countries', 'users.nationality', '=', 'countries.country_code')->leftJoin('tribes', 'users.indigenous_tribe_id', '=', 'tribes.tribe_id')->leftJoin('sport_lists', 'users.gifited_sport_id', '=', 'sport_lists.sport_id')->leftJoin('cities', 'users.household_city_code', '=', 'cities.city_code')->select('users.*', 'organizations.*', 'departments.*', 'countries.*', 'tribes.*', 'sport_lists.*', 'cities.*')->where('org_code', $org_code)->paginate(25));
        } else {
            return response()->json(User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->leftJoin('countries', 'users.nationality', '=', 'countries.country_code')->leftJoin('tribes', 'users.indigenous_tribe_id', '=', 'tribes.tribe_id')->leftJoin('sport_lists', 'users.gifited_sport_id', '=', 'sport_lists.sport_id')->leftJoin('cities', 'users.household_city_code', '=', 'cities.city_code')->select('users.*', 'organizations.*', 'departments.*', 'countries.*', 'tribes.*', 'sport_lists.*', 'cities.*')->paginate(25));
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
            'name' => 'required',
            'password' => 'required',
            'first_name_ch' => 'string|required',
            'last_name_ch' => 'string|required',
            'first_name_en' => 'string|nullable',
            'last_name_en' => 'string|nullable',
            'org_code' => 'required|size:5',
            'is_student' => 'boolean',
            'student_id' => 'string|nullable',
            'dept_id' => 'integer',
            'grade' => 'integer',
            'unified_id' => 'nullable',
            'birthday' => 'nullable',
            'nationality' => 'string|size:2',
            'is_indigenous' => 'boolean',
            'indigenous_tribe_id' => 'integer',
            'is_sport_gifited' => 'boolean',
            'gifited_sport_id' => 'integer',
            'is_school_team' => 'school_team_id_list',
            'sex' => 'integer',
            'height' => 'integer',
            'weight' => 'integer',
            'blood_type' => 'string|nullable',
            'cellphone' => 'string|nullable',
            'telephone' => 'string|nullable',
            'household_city_code' => 'string|size:2',
            'address' => 'string|nullable',
            'emergency_contact' => 'string|nullable',
            'emergency_phone' => 'string|nullable',
            'options' => 'nullable',
            'avatar' => 'string|nullable',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['last_ip'] = $request->ip();
        $temp['password'] = password_hash($request->all()['password'], PASSWORD_DEFAULT);
        $temp['athlete_id'] = strtoupper(str_pad(base_convert(floor(microtime(true)*100), 10, 36), 8, '0', STR_PAD_LEFT));
        User::insert($temp);
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
        if (is_null($user = auth('user')->user()) && is_null($admin = auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        return response()->json(User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->leftJoin('countries', 'users.nationality', '=', 'countries.country_code')->leftJoin('tribes', 'users.indigenous_tribe_id', '=', 'tribes.tribe_id')->leftJoin('sport_lists', 'users.gifited_sport_id', '=', 'sport_lists.sport_id')->leftJoin('cities', 'users.household_city_code', '=', 'cities.city_code')->select('users.*', 'organizations.*', 'departments.*', 'countries.*', 'tribes.*', 'sport_lists.*', 'cities.*')->where('u_id', $id)->first());
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
        if (is_null($user = auth('user')->user()) && is_null($admin = auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $validator = Validator::make($request->all(),[
            'account' => 'required|unique:users,account',
            'name' => 'required',
            'first_name_ch' => 'string|required',
            'last_name_ch' => 'string|required',
            'first_name_en' => 'string|nullable',
            'last_name_en' => 'string|nullable',
            'org_code' => 'required|size:5',
            'is_student' => 'boolean',
            'student_id' => 'string|nullable',
            'dept_id' => 'integer',
            'grade' => 'integer',
            'unified_id' => 'nullable',
            'birthday' => 'nullable',
            'nationality' => 'string|size:2',
            'is_indigenous' => 'boolean',
            'indigenous_tribe_id' => 'integer',
            'is_sport_gifited' => 'boolean',
            'gifited_sport_id' => 'integer',
            'is_school_team' => 'school_team_id_list',
            'sex' => 'integer',
            'height' => 'integer',
            'weight' => 'integer',
            'blood_type' => 'string|nullable',
            'cellphone' => 'string|nullable',
            'telephone' => 'string|nullable',
            'household_city_code' => 'string|size:2',
            'address' => 'string|nullable',
            'emergency_contact' => 'string|nullable',
            'emergency_phone' => 'string|nullable',
            'options' => 'nullable',
            'avatar' => 'string|nullable',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['last_ip'] = $request->ip();
        User::where('u_id', $id)->update($temp);
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
        if (is_null($user = auth('user')->user()) && is_null($admin = auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        User::where('u_id', $id)->delete();
        return response()->json(['status'=>'A01']);
    }
}