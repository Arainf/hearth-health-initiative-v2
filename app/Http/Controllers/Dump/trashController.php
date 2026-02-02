<?php

namespace App\Http\Controllers\Dump;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class trashController extends Controller
{
    public static function encrypt(string $trash)
    {
        $encrypted = $trash;
        if(env('APP_DEBUG') == false){
            $encrypted = base64_encode(hash_hmac('sha256', $trash, config('app.key')));
        }

        $encrypted = base64_encode(hash_hmac('sha256', $trash, config('app.key')));

        return ($encrypted);
    }

    public static function decrypt(string $trash)
    {
        $decrypted = Crypt::decryptString($trash);
        return urldecode($decrypted);
    }
}
