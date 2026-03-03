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
use App\Http\Controllers\Pages\LoginPageController;
use App\Http\Controllers\Pages\PageController;
use App\Http\Controllers\Pages\PatientPageController;
use App\Http\Controllers\Pages\RecordPageController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\Pdf\PdfController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\Table\TableController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pages\UnitPageController;
use App\Http\Controllers\Table\CreateUnitController;


$encryption = new trashController();


Route::get('/', [LoginPageController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/page/{token}', [PageController::class, 'page'])->name('page');
    Route::get('/table/{token}', [PageController::class, 'table'])->name('page');
    Route::put('/update/{token}', [PageController::class, 'update'])->name('update');
    Route::post('/store/{token}', [PageController::class, 'store'])->name('store');
    Route::delete('/delete/{token}', [PageController::class, 'destroy'])->name('delete');
});

Route::get('/unauthorized', function () { return view('pages.unauthorize'); })->name('unauthorized');

require __DIR__.'/auth.php';
