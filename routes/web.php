<?php

use App\Http\Controllers\Pages\LoginPageController;
use App\Http\Controllers\Pages\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginPageController::class, 'index']);

Route::middleware(['auth' , 'decrypt.page'])->group(function () {
    Route::get('/page/{token}',         [PageController::class, 'page'])->name('page');
    Route::get('/table/{token}',        [PageController::class, 'table'])->name('page');
    Route::put('/update/{token}',       [PageController::class, 'update'])->name('update');
    Route::patch('/profile/{token}',    [PageController::class, 'patch'])->name('profile');
    Route::post('/store/{token}',       [PageController::class, 'store'])->name('store');
    Route::delete('/delete/{token}',    [PageController::class, 'destroy'])->name('delete');
});

Route::get('/unauthorized', function () { return view('pages.unauthorize'); })->name('unauthorized');

require __DIR__.'/auth.php';
