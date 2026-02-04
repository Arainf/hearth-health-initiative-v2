<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class ComparePageController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user) return redirect('unauthorized');

        return view("pages.compare");
    }
}
