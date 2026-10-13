<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Use Tailwind pagination views
        Paginator::useTailwind();

        // Force HTTPS in production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Max 5 failed login attempts per minute per IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Max 3 failed verify code attempts per minute per IP
        RateLimiter::for('verify-code', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // Max 3 resend attempts per minute per IP
        RateLimiter::for('resend-code', function (Request $request) {
            return Limit::perMinute(1)->by($request->ip());
        });
    }
}