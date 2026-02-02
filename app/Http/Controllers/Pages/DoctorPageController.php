<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\DropdownService;

class DoctorPageController extends Controller
{
    public function index()
    {

        $currentYear = now()->year;
        $years = DropdownService::years();
        $status = DropdownService::status($currentYear, false);

        $user = auth()->user();

        if($user->is_Doctor()){
            return view('pages.doctor',
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
