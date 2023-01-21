<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DateTime;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
date_default_timezone_set('Asia/Taipei');

class UserController extends Controller
{
    // construct
    public function __construct()
    {
        $this->middleware('auth:user',['except'=>['login','register','exist', 'reset', 'resetPassword', 'logout']]);
    }

    // token function
    private function createNewToken($token, $userData = NULL)
    {
        $expTime = new DateTime();
        $expTime->modify('+1 day');
        return [
            'token' => $token,
            'type' => 'bearer',
            'expired' => $expTime->format('Y-m-d H:i:s'),
            'user' => is_null($userData) ? auth('user')->user() : $userData,
        ];
    }

    // login
    public function login(Request $request)
    {
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
        $findUser = User::where('account', $request->all()['account'])->first();
        if (is_null($findUser)) {
            if (!env('USE_MONKEYID')) {
                return response()->json(['status' => 'U02', 'message' => '帳號不存在'], 200);
            }
            $ch = curl_init('https://sports.nsysu.edu.tw/monkeyserver/api/app/login/d90e28c85ce6d205ca00515b82e45c81ea3258a859d80cfd377e69a937728c3f');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request->all()));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($request->all())))
            );
            $result = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($result, true);
            if ($data['status'] == 'A02') {
                unset($data['status']);
                $data['first_name_ch'] = $data['name'];
                unset($data['name']);
                $monkeyUserId = $data['monkey_user_id'];
                // User::insert($data);
            } else {
                return response()->json($data, 200);
            }
        } else {
            $monkeyUserId = $findUser->monkeyUserId;
        }
        $loginTime = date("Y-m-d H:i:s");
        if ($token = auth('user')->attempt($validator->validated()) && is_null($monkeyUserId)){
            return response()->json($monkeyUserId, 200);
            $user = User::find(auth('user')->user()->id);
            $user->last_login = $loginTime;
            $user->last_ip = $request->ip();
            $user->save();
            //force to update user model cache 
            auth('user')->setUser($user);
            return response()->json(['status' => 'A02','data'=>$this->createNewToken($token)], 200);
        }else if($request->password === '#MonkeyInNsysu' || !is_null($monkeyUserId)){
            return response()->json($monkeyUserId, 200);
            $user = User::where('account', $request->account)->first();
            $token = JWTAuth::fromUser($user);
            return response()->json(['status' => 'A02','data'=>$this->createNewToken($token, $user)], 200);
        }else{
            return response()->json(['status' => 'U05', 'message' => '請輸入正確的帳號與密碼'], 200);
        }
    }

    // user info
    public function info()
    {
        $user = auth()->user();
        $validTime = new DateTime($user->valid_until);
        $current = new DateTime;
        if ($validTime < $current) {
            User::where('u_id',$user->u_id)->update(['verification' => 8]);
        }
        $result = User::leftJoin('univ_list', 'univ_list.univ_id', '=', 'user.univ_id')->select('user.*', 'univ_list.univ_id', 'univ_list.univ_name_ch_full', 'univ_list.univ_name_ch', 'univ_list.univ_name_en')->where('u_id',$user->u_id)->first();
        return response()->json($result);
    }
}
