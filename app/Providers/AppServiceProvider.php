<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Only force HTTPS when accessed via domain (CDN), not localhost
        $host = request()->getHost();
        if ($host !== '37.152.174.87' && $host !== 'localhost' && !str_contains($host, '127.0.0.1')) {
            URL::forceScheme('https');
        }
    }
}
