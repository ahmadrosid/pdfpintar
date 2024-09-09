<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Opcodes\LogViewer\Facades\LogViewer;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

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
        Gate::define('viewHorizon', function (User $user) {
            return $user->email == 'hey@ahmadrosid.com';
        });
        LogViewer::auth(function ($request) {
            if (!$request->user()) return false;
            return $request->user()->email == 'hey@ahmadrosid.com';
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject(__('Please Verify Your Email'))
                ->line(__('Thank you for joining pdfpintar! We\'re excited to have you on board.'))
                ->line(__('To ensure the security of your account and get started, please verify your email address.'))
                ->action(__('Click to verify'), $url)
                ->line(__('This link will expire in 24 hours for your security.'))
                ->line(__('If you didn\'t create an account with pdfpintar, please ignore this email.'))
                ->line(__('If you have any questions, feel free to contact our support team.'));
        });
    }
}
