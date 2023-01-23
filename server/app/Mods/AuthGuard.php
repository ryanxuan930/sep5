<?php

namespace App\Mods;

class AuthGuard {
    public static function check() {
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
    }
}
?>