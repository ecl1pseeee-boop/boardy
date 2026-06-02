<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensExpireIn(now()->addMinute());
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::authorizationView('oauth.authorize');
        User::observe(UserObserver::class);


        if(app()->environment('local')) {
            Http::globalOptions([
                'verify' => false,
            ]);
        }
    }
}
