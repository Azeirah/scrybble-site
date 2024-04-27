<?php

namespace App\Providers;

use App\Services\Remarks\RemarksDockerServer;
use App\Services\Remarks\RemarksRunDockerContainer;
use App\Services\Remarks\RemarksService;
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

        $this->app->bind(RemarksService::class, fn() => match (config('app.env')) {
            'local' => new RemarksDockerServer(),
            'production' => new RemarksRunDockerContainer()
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
