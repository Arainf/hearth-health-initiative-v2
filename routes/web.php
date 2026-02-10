<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DeleteController;
use App\Http\Controllers\Pages\AccountPageController;
use App\Http\Controllers\Pages\ArchivePageController;
use App\Http\Controllers\Pages\ComparePageController;
use App\Http\Controllers\Pages\DashboardPageController;
use App\Http\Controllers\Pages\DoctorPageController;
use App\Http\Controllers\Dump\trashController;
use App\Http\Controllers\Pages\PatientPageController;
use App\Http\Controllers\Pages\RecordPageController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\Pdf\PdfController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\Table\TableController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pages\UnitPageController;

Route::get('/', function () {
        return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/table/patients/{id}/records', [TableController::class, 'patientsOwnRecord']);
    Route::get('/api/statuses', [ApiController::class, 'getStatuses']);
    Route::put('/api/statusUpdate', [ApiController::class, 'update']);
    Route::get('/api/record/{id}', [ApiController::class, 'getSingleRecord']);
    //Get Status Count
    Route::get('/api/getStatusCount', [ApiController::class, 'countStatus']);
    Route::get('/api/getRecordYears', [ApiController::class, 'getRecordYears']);
    Route::get('/api/getPatientYears', [ApiController::class, 'getPatientYears']);
    Route::get('/api/getPatient/search', [ApiController::class, 'searchPatient']);
    Route::get('/api/getGeneratedContent/{id}', [ApiController::class, 'getGeneratedContent']);
    Route::get('/export/pdf/{id}', [PdfController::class, 'export']);

    Route::get('/patients/{id}', [PatientController::class, 'show'])->name('patientFiles');
    Route::get('/patients/{id}/edit', [PatientController::class, 'edit'])->name('patientEdit');
    Route::put('/patients/{id}', [PatientController::class, 'update']);


    Route::get('/api/patients/{id}/records/{action}', [ApiController::class, 'recordsWithPatient']);

    //Post
    Route::post('/api/evaluate/{id}', [AIController::class, 'evaluateRecord']);
    Route::post('/api/saveRecord/{id}', [AIController::class, 'editChangesSave']);
    Route::put('/api/records/{id}', [RecordController::class, 'update']);
    Route::post('/records/store', [RecordController::class, 'store'])->name('records.store');

    Route::delete('/api/patient/delete/{id}', [DeleteController::class, 'deletePatient']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'can:isAdmin'])->group(function () {
    Route::get('/table/archive-records', [TableController::class, 'archiveRecords']);
    Route::get('/api/getArchiveYears', [ApiController::class, 'getArchiveYears']);
    Route::delete('/api/accounts/delete/{id}', [ApiController::class, 'deleteUser']);
    Route::put('/api/accounts/{id}/ai-access', [ApiController::class, 'aiAccess']);
    Route::put('/api/accounts/{id}/admin', [ApiController::class, 'adminAccess']);
    Route::put('/api/accounts/{id}/doctor', [ApiController::class, 'doctorAccess']);

    Route::post('/accounts', [RegisteredUserController::class, 'store'])
        ->name('accounts.store');
});

/** NEW ROUTES WITH ENCRYPTED ROUTE */

Route::middleware('auth')->group(function () {
    $core = trashController::encrypt('doctor');
    Route::get("/{$core}", [DoctorPageController::class, 'index'])->name('doctor');
    Route::get("/table/{$core}", [DoctorPageController::class, 'table']);
});

Route::middleware('auth')->group(function () {
    $core = trashController::encrypt('dashboard');
    Route::get("/{$core}", [DashboardPageController::class, 'index'])->name('dashboard');
    Route::get("/table/{$core}", [DashboardPageController::class, 'table']);
});

Route::middleware('auth')->group(function () {
    $core = trashController::encrypt('account');
    Route::get("/{$core}", [AccountPageController::class, 'index'])->name('account');
    Route::get("/table/{$core}", [AccountPageController::class, 'table']);

    Route::get("/create/{$core}", [RegisteredUserController::class, 'create'])->name('account.create');
});

Route::middleware('auth')->group(function () {
   $core = trashController::encrypt('archive');
   Route::get("/{$core}", [ArchivePageController::class, 'index'])->name('archive');
   Route::get("/table/{$core}", [AccountPageController::class, 'table']);
});

Route::middleware('auth')->group(function () {
    $core = trashController::encrypt('patient');
    Route::get("/{$core}", [PatientPageController::class, 'index'])->name('patient');
    Route::get("/table/{$core}", [PatientPageController::class, 'table']);
});

Route::middleware('auth')->group(function () {
   $core = trashController::encrypt('compare');
   Route::get("/{$core}", [ComparePageController::class, 'index'])->name('compare');

});

Route::middleware('auth')->group(function () {
    $core = trashController::encrypt('record');
    Route::get("/{$core}", [RecordPageController::class, 'index'])->name('record');
    Route::get("/table/{$core}", [RecordPageController::class, 'table']);

});

Route::middleware('auth')->group(function(){
    $core = trashController::encrypt('unit');
    Route::get("/{$core}", [UnitPageController::class, 'index'])->name('unit');
});


Route::get('/unauthorized', function () { return view('pages.unauthorize'); })->name('unauthorized');

require __DIR__.'/auth.php';
