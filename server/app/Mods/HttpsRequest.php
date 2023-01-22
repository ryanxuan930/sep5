<?php

namespace App\Mods;

class HttpsRequest {
    public static function post($url, $payload) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($payload)))
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }
}
?>