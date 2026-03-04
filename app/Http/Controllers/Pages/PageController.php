<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;
use Illuminate\Http\Request;

class PageController extends Controller
{
    //  METHOD: GET
    public function page(Request $request, $token)
    {
        $enc = app(trashController::class);
        $page = $enc->decrypt($token);

        return match ($page) {
            'doctor' => app(DoctorPageController::class)->index($request,$token),
            'dashboard' => app(DashboardPageController::class)->index($request, $token),
            'patient' => app(PatientPageController::class)->index($request, $token),
            'account' => app(AccountPageController::class)->index($request, $token),
            'archive' => app(ArchivePageController::class)->index($request, $token),
            'unit' => app(UnitPageController::class)->index($request, $token),
            'record' => app(RecordPageController::class)->index($request,$token),
            'compare' => app(ComparePageController::class)->index($token),
            'generated' => app(GeneratedPageController::class)->menu($request),
            default => abort(404),
        };
    }

    //  METHOD: GET
    public function table(Request $request,$token)
    {
        $enc = app(trashController::class);
        $page = $enc->decrypt($token);

        return match ($page) {
            'doctor' => app(DoctorPageController::class)->table($request),
            'dashboard' => app(DashboardPageController::class)->table($request),
            'patient' => app(PatientPageController::class)->table($request),
            'account' => app(AccountPageController::class)->table($request),
            'archive' => app(ArchivePageController::class)->table($request, $token),
            'unit' => app(UnitPageController::class)->table($request),
            'record' => app(RecordPageController::class)->table($request),
            'compare' => app(ComparePageController::class)->index($request),

            default => abort(404),
        };
    }

    // PUT
    public function update(Request $request, $token)
    {
        $enc = app(trashController::class);
        $page = $enc->decrypt($token);

        return match ($page) {
            'doctor' => app(DoctorPageController::class)->index($request,$token),
            'dashboard' => app(DashboardPageController::class)->index($request, $token),
            'patient' => app(PatientPageController::class)->index($request, $token),
            'account' => app(AccountPageController::class)->index($request, $token),
            'archive' => app(ArchivePageController::class)->index($request, $token),
            'unit' => app(UnitPageController::class)->index($request, $token),
            default => abort(404),
        };
    }

    public function store(Request $request, $token)
    {
        $enc = app(trashController::class);
        $page = $enc->decrypt($token);

        return match ($page) {
            'record' => app(RecordPageController::class)->index($request,$token),
            'doctor' => app(DoctorPageController::class)->index($request,$token),
            'dashboard' => app(DashboardPageController::class)->index($request,$token),
            'patient' => app(PatientPageController::class)->index($request,$token),
            'account' => app(AccountPageController::class)->index($request,$token),
            'generated' => app(GeneratedPageController::class)->menu($request,$token),
            'unit' => app(UnitPageController::class)->index($request, $token),
            default => abort(404),
        };
    }

    public function destroy(Request $request, $token)
    {
        $enc = app(trashController::class);
        $page = $enc->decrypt($token);

        return match ($page) {
            'patient' => app(PatientPageController::class)->index($request, $token),
            'unit' => app(UnitPageController::class)->index($request, $token),
            default => abort(404),
        };
    }

}
