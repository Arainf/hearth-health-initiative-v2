<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('use-ai', function ($user) {
            return (bool) $user->ai_access;
        });

        Gate::define('isAdmin', fn ($user) => $user->is_admin);
        Gate::define('isDoctor', fn ($user) => $user->is_doctor);
    }

}
