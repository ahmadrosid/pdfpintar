<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Opcodes\LogViewer\Facades\LogViewer;

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
        Gate::define('viewPulse', function (User $user) {
            return $user->email == 'hey@ahmadrosid.com';
        });
        LogViewer::auth(function ($request) {
            if (!$request->user()) return false;
            return $request->user()->email == 'hey@ahmadrosid.com';
        });
    }
}
