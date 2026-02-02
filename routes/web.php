<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DeleteController;
use App\Http\Controllers\Doctor\DoctorPage;
use App\Http\Controllers\Dump\trashController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\Pdf\PdfController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\Table\TableController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
        return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/table/records', [TableController::class, 'records']);
    Route::get('/table/patients', [TableController::class, 'patients']);
    Route::get('/table/patientsNav', [TableController::class, 'patientsNav']);
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

    Route::get('/patients', [PatientController::class, 'index'])->name('patient');;
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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/compare', function () {
        return view('compare');
    })->name('compare');

    Route::get('/editGenerate', function () {
        return view('edit');
    })->name('edit');

    Route::get('/createForm', function () {
        return view('form');
    })->name('form');


});

Route::middleware(['auth', 'can:isAdmin'])->group(function () {
    Route::get('/table/accounts', [TableController::class, 'accounts']);
    Route::get('/table/archive-records', [TableController::class, 'archiveRecords']);
    Route::get('/api/getArchiveYears', [ApiController::class, 'getArchiveYears']);
    Route::get('/archive', function(){
        return view('archive');
    })->name('archive');
    Route::delete('/api/accounts/delete/{id}', [ApiController::class, 'deleteUser']);
    Route::put('/api/accounts/{id}/ai-access', [ApiController::class, 'aiAccess']);
    Route::put('/api/accounts/{id}/admin', [ApiController::class, 'adminAccess']);
    Route::put('/api/accounts/{id}/doctor', [ApiController::class, 'doctorAccess']);
    Route::get('/accounts/create', [RegisteredUserController::class, 'create'])->name('accounts.create');
    Route::get('/account', function () {
        return view('account');
    })->name('account');
    Route::post('/accounts', [RegisteredUserController::class, 'store'])
        ->name('accounts.store');
});


Route::middleware(['auth', 'verified'])->group(function () {
    $doctorSlug = trashController::encrypt('doctor');
    Route::get("/{$doctorSlug}", [DoctorPage::class, 'index'])->name('doctor');
});


require __DIR__.'/auth.php';
