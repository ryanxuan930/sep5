<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\AdminOrganization;
use Illuminate\Support\Facades\Storage;
set_time_limit(0);

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->leftJoin('countries', 'users.nationality', '=', 'countries.country_code')->leftJoin('tribes', 'users.indigenous_tribe_id', '=', 'tribes.tribe_id')->leftJoin('sport_lists', 'users.gifited_sport_id', '=', 'sport_lists.sport_id')->leftJoin('cities', 'users.household_city_code', '=', 'cities.city_code')->select('users.*', 'organizations.org_id', 'organizations.org_code', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_id', 'departments.dept_name_ch', 'departments.dept_name_en', 'countries.*', 'tribes.*', 'sport_lists.*', 'cities.*')->orderBy('users.u_id', 'asc');
        if (is_null($user = auth('user')->user()) && is_null($admin = auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        } else if (is_null($admin)) {
            $org_code = $user->org_code;
            return response()->json($query->where('organizations.org_code', $org_code)->paginate(25));
        } else {
            if ($admin->admin_org_id == 1) {
                return response()->json($query->paginate(25));
            } else {
                $orgData = AdminOrganization::leftJoin('organizations', 'organizations.org_id', '=', 'admin_organizations.related_user_org_id')->where('admin_org_id', $admin->admin_org_id)->first();
                return response()->json($query->where('organizations.org_code', $orgData->org_code)->paginate(25));
            }
        }
    }
    public function indexByUser()
    {
        if (is_null($user = auth('user')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $query = User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->select('users.*', 'organizations.org_id', 'organizations.org_code', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_id', 'departments.dept_name_ch', 'departments.dept_name_en')->orderBy('users.u_id', 'asc');
        $permission = $user->permission;
        if ($permission == 0){
            return response()->json($query->where('u_id', $user->u_id)->get());
        } else if ($permission == 1) {
            return response()->json($query->where('users.dept_id', $user->dept_id)->get());
        } else if ($permission == 2) {
            return response()->json($query->where('users.org_code', $user->org_code)->get());
        } else {
            return response()->json([]);
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
        if (is_null($user = auth('user')->user()) && is_null($admin = auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $validator = Validator::make($request->all(),[
            'account' => 'required|unique:users,account',
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
            'is_school_team' => 'boolean',
            'school_team_id_list' => 'nullable',
            'sex' => 'integer',
            'height' => 'integer',
            'weight' => 'integer',
            'blood_type' => 'string|nullable',
            'cellphone' => 'string|nullable',
            'telephone' => 'string|nullable',
            'household_city_code' => 'string|nullable',
            'address' => 'string|nullable',
            'emergency_contact' => 'string|nullable',
            'emergency_phone' => 'string|nullable',
            'options' => 'nullable',
            'avatar' => 'nullable',
            'permission' => 'integer',
            'num_in_dept' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // constant
        $loginTime = date("Y-m-d H:i:s");
        $temp = $request->all();
        $temp['created_at'] = $loginTime;
        $temp['updated_at'] = $loginTime;
        $temp['last_ip'] = $request->ip();
        $temp['password'] = password_hash($request->all()['password'], PASSWORD_DEFAULT);
        $temp['athlete_id'] = strtoupper(str_pad(base_convert(floor(microtime(true)*100), 10, 36), 8, '0', STR_PAD_LEFT));
        User::insert($temp);
        return response()->json(['status'=>'A01']);
    }

    public function storeByBatch(Request $request)
    {
        if (is_null($user = auth('user')->user()) && is_null($admin = auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $validator = Validator::make($request->all(),[
            '*.account' => 'required|unique:users,account',
            '*.password' => 'required',
            '*.first_name_ch' => 'string|required',
            '*.last_name_ch' => 'string|required',
            '*.first_name_en' => 'string|nullable',
            '*.last_name_en' => 'string|nullable',
            '*.org_code' => 'required|size:5',
            '*.is_student' => 'boolean|nullable',
            '*.student_id' => 'string|nullable',
            '*.dept_id' => 'integer|nullable',
            '*.grade' => 'integer|nullable',
            '*.unified_id' => 'nullable',
            '*.birthday' => 'nullable',
            '*.nationality' => 'string|size:2',
            '*.sex' => 'integer',
            '*.height' => 'integer',
            '*.weight' => 'integer',
            '*.blood_type' => 'string|nullable',
            '*.cellphone' => 'string|nullable',
            '*.telephone' => 'string|nullable',
            '*.household_city_code' => 'string|nullable',
            '*.address' => 'string|nullable',
            '*.emergency_contact' => 'string|nullable',
            '*.emergency_phone' => 'string|nullable',
            '*.options' => 'nullable',
            '*.avatar' => 'nullable',
            '*.permission' => 'integer',
            '*.num_in_dept' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>'E01', 'message' => $validator->errors()]);
        }
        // constant
        $loginTime = date("Y-m-d H:i:s");
        $temp = $request->all();
        for ($i = 0; $i < count($temp); $i++) {
            $temp[$i]['created_at'] = $loginTime;
            $temp[$i]['updated_at'] = $loginTime;
            $temp[$i]['last_ip'] = $request->ip();
            $temp[$i]['password'] = password_hash($temp[$i]['password'], PASSWORD_DEFAULT);
            $temp[$i]['athlete_id'] = strtoupper(str_pad(base_convert(floor(microtime(true)*100), 10, 36), 8, '0', STR_PAD_LEFT));
        }
        User::insert($temp);
        return response()->json(['status'=>'A01']);
    }

    public function search(Request $request)
    {
        $temp = $request->all();
        $query = User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->leftJoin('countries', 'users.nationality', '=', 'countries.country_code')->leftJoin('tribes', 'users.indigenous_tribe_id', '=', 'tribes.tribe_id')->leftJoin('sport_lists', 'users.gifited_sport_id', '=', 'sport_lists.sport_id')->leftJoin('cities', 'users.household_city_code', '=', 'cities.city_code')->select('users.*', 'organizations.org_id', 'organizations.org_code', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_id', 'departments.dept_name_ch', 'departments.dept_name_en', 'countries.*', 'tribes.*', 'sport_lists.*', 'cities.*')->where('users.account', 'like', '%'.$temp['account'].'%');
        if (is_null($user = auth('user')->user()) && is_null($admin = auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        } else if (is_null($admin)) {
            $org_code = $user->org_code;
            return response()->json($query->where('organizations.org_code', $org_code)->limit(20)->get());
        } else {
            if ($admin->admin_org_id == 1) {
                return response()->json($query->limit(20)->get());
            } else {
                $orgData = AdminOrganization::leftJoin('organizations', 'organizations.org_id', '=', 'admin_organizations.related_user_org_id')->where('admin_org_id', $admin->admin_org_id)->first();
                return response()->json($query->where('organizations.org_code', $orgData->org_code)->limit(20)->get());
            }
        }
    }
    public function searchByUnit(Request $request)
    {
        $temp = $request->all();
        if (is_null($admin = auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        } 
        $validator = Validator::make($request->all(),[
            'org_id' => 'integer|required',
            'dept_id' => 'integer|required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $query = User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->leftJoin('countries', 'users.nationality', '=', 'countries.country_code')->leftJoin('tribes', 'users.indigenous_tribe_id', '=', 'tribes.tribe_id')->leftJoin('sport_lists', 'users.gifited_sport_id', '=', 'sport_lists.sport_id')->leftJoin('cities', 'users.household_city_code', '=', 'cities.city_code')->select('users.*', 'organizations.org_id', 'organizations.org_code', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_id', 'departments.dept_name_ch', 'departments.dept_name_en', 'countries.country_name_ch', 'countries.country_name_en', 'tribes.tribe_name_ch', 'tribes.tribe_name', 'sport_lists.sport_name_ch', 'sport_lists.sport_name_en', 'sport_lists.sport_code', 'cities.city_name_ch', 'cities.city_name_en')->where('organizations.org_id', $temp['org_id']);
        if ($temp['dept_id'] > 0) {
            $query->where('users.dept_id', $temp['dept_id']);
        }
        return response()->json($query->get());
    }
    public function getUserFromList(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'data' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $array = json_decode($request->all()['data'], true);
        return response()->json(User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->select('users.u_id', 'users.athlete_id', 'users.first_name_ch', 'users.last_name_ch', 'users.first_name_en', 'users.last_name_en', 'users.org_code', 'users.dept_id', 'users.sex', 'users.num_in_dept', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en')->whereIn('u_id', $array)->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->leftJoin('countries', 'users.nationality', '=', 'countries.country_code')->leftJoin('tribes', 'users.indigenous_tribe_id', '=', 'tribes.tribe_id')->leftJoin('sport_lists', 'users.gifited_sport_id', '=', 'sport_lists.sport_id')->leftJoin('cities', 'users.household_city_code', '=', 'cities.city_code')->select('users.*', 'organizations.*', 'departments.*', 'countries.*', 'tribes.*', 'sport_lists.*', 'cities.*')->where('u_id', $id)->first());
    }
    public function showByAthleteId($id)
    {
        if (is_null($user = auth('user')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $result = User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->select('users.*', 'organizations.org_id', 'organizations.org_code', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_id', 'departments.dept_name_ch', 'departments.dept_name_en')->where('athlete_id', $id)->first();
        if (is_null($result)) {
            $result = User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->select('users.*', 'organizations.org_id', 'organizations.org_code', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_id', 'departments.dept_name_ch', 'departments.dept_name_en')->where('student_id', $id)->first();
        }
        return response()->json($result);
    }
    public function showByName($firstName, $lastName)
    {
        return response()->json(User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->leftJoin('countries', 'users.nationality', '=', 'countries.country_code')->leftJoin('tribes', 'users.indigenous_tribe_id', '=', 'tribes.tribe_id')->leftJoin('sport_lists', 'users.gifited_sport_id', '=', 'sport_lists.sport_id')->leftJoin('cities', 'users.household_city_code', '=', 'cities.city_code')->select('users.*', 'organizations.*', 'departments.*', 'countries.*', 'tribes.*', 'sport_lists.*', 'cities.*')->where('first_name_ch', $firstName)->where('last_name_ch', $lastName)->first());
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
            'account' => 'required',
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
            'is_school_team' => 'boolean',
            'school_team_id_list' => 'nullable',
            'sex' => 'integer',
            'height' => 'integer',
            'weight' => 'integer',
            'blood_type' => 'string|nullable',
            'cellphone' => 'string|nullable',
            'telephone' => 'string|nullable',
            'household_city_code' => 'string|nullable',
            'address' => 'string|nullable',
            'emergency_contact' => 'string|nullable',
            'emergency_phone' => 'string|nullable',
            'options' => 'nullable',
            'avatar' => 'nullable',
            'permission' => 'integer',
            'num_in_dept' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // constant
        $loginTime = date("Y-m-d H:i:s");
        $temp = $request->all();
        $temp['updated_at'] = $loginTime;
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
