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
}
