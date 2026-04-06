<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
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
        ResetPassword::createUrlUsing(function (object $notifiable, string $token): string {
            $frontendUrl = rtrim(config('app.frontend_reset_password_url', config('app.url').'/reset-password'), '/');

            return $frontendUrl.'?token='.$token.'&email='.urlencode($notifiable->getEmailForPasswordReset());
        });
    }
}
