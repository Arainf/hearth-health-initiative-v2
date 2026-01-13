<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [\App\Listeners\LogSuccessfulLogin::class],
        Logout::class => [\App\Listeners\LogLogout::class],
        Lockout::class => [\App\Listeners\LogLockout::class],
        Registered::class => [\App\Listeners\LogRegistered::class],
        PasswordReset::class => [\App\Listeners\LogPasswordReset::class],
    ];
}
