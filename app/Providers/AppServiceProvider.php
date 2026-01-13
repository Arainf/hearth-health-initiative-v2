<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Patient;
use App\Models\Records;
use App\Models\User;
use App\Observers\PatientObserver;
use App\Observers\RecordsObserver;
use App\Observers\UserObserver;

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
        Patient::observe(PatientObserver::class);
        Records::observe(RecordsObserver::class);
        User::observe(UserObserver::class);
    }
}
