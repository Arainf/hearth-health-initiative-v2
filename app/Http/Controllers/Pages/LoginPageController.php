<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;

class LoginPageController extends Controller
{
    public function index()
    {
        $encryption = new trashController();
        return view('auth.login', compact('encryption'));
    }
}
