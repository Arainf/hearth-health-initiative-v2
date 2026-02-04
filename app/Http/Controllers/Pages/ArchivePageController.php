<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;
use App\Services\DropdownService;

class ArchivePageController extends Controller
{
    public function index()
    {

        $user = auth()->user();
        $currentYear = now()->year;
        $years = DropdownService::years();
        $status = DropdownService::status($currentYear, false);


        if(!$user->is_Admin()) return redirect('unauthorized');


        return view('pages.archive',
        [
            'years' => $years,
            'currentYear' => $currentYear,
            'status' => $status,
            'table' => trashController::encrypt('archive')
        ]);

    }
}
