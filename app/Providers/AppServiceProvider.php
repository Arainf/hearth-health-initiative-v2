<?php

namespace App\Providers;

use App\Http\Controllers\Dump\trashController;
use App\Services\DropdownService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::share('encryption', app(trashController::class));
        View::share('dropdown', app(DropdownService::class));
        
        if (app()->environment('local')) {
            DB::listen(function ($query) {
                logger("SQL: " . $query->sql);
                logger("Time: " . $query->time . " ms");
            });
        }

    }
}
