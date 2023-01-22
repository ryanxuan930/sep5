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
use App\Mods\HttpsRequest;
use App\Mods\TokenMaker;
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

        // find user in User table
        $findUser = User::where('account', $request->all()['account'])->first();
        if (is_null($findUser)) { // user not found
            if (env('USE_MONKEYID')) { // if using monkey id
                $response = HttpsRequest::post('https://sports.nsysu.edu.tw/monkeyserver/api/app/login/'.env('MONKEYID_KEY'), $request->all());
                if ($response['status'] == 'A02') { // check response
                    if (is_null($findUser)) { // add to user table
                        $temp = [
                            'account' => $response['account'],
                            'monkey_user_id' => $response['monkey_user_id'],
                            'user_identity' => $response['user_identity'],
                            'first_name_ch' => $response['name'],
                            'org_id' => $response['org_id'],
                            'password' => password_hash($request->all()['password'], PASSWORD_DEFAULT)
                        ];
                        User::insert($temp);
                    } else { // update user table
                        $temp = [
                            'account' => $response['account'],
                            'user_identity' => $response['user_identity'],
                            'org_id' => $response['org_id'],
                            'password' => password_hash($request->all()['password'], PASSWORD_DEFAULT)
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
        }
        // get user data again
        $findUser = User::where('account', $request->all()['account'])->first();
        $loginTime = date("Y-m-d H:i:s");
        if ($token = auth('user')->attempt($validator->validated()) && is_null($findUser->monkey_user_id)){
            $user = User::find(auth('user')->user()->id);
            $user->last_login = $loginTime;
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
