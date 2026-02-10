<?php
namespace App\Http\Controllers\Pages;
use App\Http\Controllers\Controller;

class UnitPageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if(!$user) return redirect('unauthorized');
        return view('pages.unit');
    }
}