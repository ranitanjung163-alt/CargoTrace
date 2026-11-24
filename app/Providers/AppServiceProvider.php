<?php

namespace App\Providers;

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
        if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || config('app.env') === 'production' || str_contains(request()->getHost(), 'vercel.app')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
