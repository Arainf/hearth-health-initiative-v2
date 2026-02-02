<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\DropdownService;

class DashboardPageController extends Controller
{
    public function index()
    {
        $currentYear = now()->year;
        $years = DropdownService::years();
        $status = DropdownService::status($currentYear, false);

        $user = auth()->user();

        if($user->is_Doctor() || $user->is_Admin() || $user){
            return view('pages.dashboard',
                [
                    'years' => $years,
                    'currentYear' => $currentYear,
                    'status' => $status,
                ]);
        } else {
            return view('pages.unauthorize');
        }



    }
}
