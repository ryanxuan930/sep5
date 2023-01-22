<?php

namespace App\Mods;

use DateTime;

class TokenMaker {
    public static function forge(mixed $token, mixed $userData) {
        $expTime = new DateTime();
        $expTime->modify('+1 day');
        return [
            'token' => $token,
            'type' => 'bearer',
            'expired' => $expTime->format('Y-m-d H:i:s'),
            'user' => $userData,
        ];
    }
}
?>