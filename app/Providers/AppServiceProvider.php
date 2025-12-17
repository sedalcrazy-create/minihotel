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
        // Only force HTTPS when accessed via domain (CDN)
        if (request()->getHost() !== '37.152.174.87') {
            URL::forceScheme('https');
        }
    }
}
