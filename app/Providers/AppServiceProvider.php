<?php

namespace App\Providers;

use App\Services\RMapi;
use Illuminate\Support\ServiceProvider;
use URL;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton(RMapi::class, function () {
            return new RMapi();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
