<?php

namespace App\Http\Controllers\Dump;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Random\RandomException;

class trashController extends Controller
{

    public function encrypt($string)
    {
        return Crypt::encryptString($string);
    }

    public function decrypt($string)
    {
        return Crypt::decryptString($string);
    }

}
