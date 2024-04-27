<?php

namespace App\Providers;

use App\Services\interfaces\PRMStorageInterface;
use App\Services\interfaces\S3PRMStorage;
use App\Services\Remarks\RemarksDockerServer;
use App\Services\Remarks\RemarksRunDockerContainer;
use App\Services\Remarks\RemarksService;
use App\Services\RMapi;
use Illuminate\Support\ServiceProvider;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(RMapi::class, function () {
            return new RMapi();
        });

        $this->app->bind(RemarksService::class, fn() => match (config('app.env')) {
            'local' => new RemarksDockerServer(),
            'production' => new RemarksRunDockerContainer()
        });

        $this->app->bind(PRMStorageInterface::class, fn() => match (strtolower(config('app.storage_platform'))) {
            // TODO: Implement disk storage instead of stubbing
            'disk' => new class implements PRMStorageInterface {
                public function store(string $path, string $zipFileContents) {}
                public function getDownloadURL(string $path) {}
            },
            "aws" => new S3PRMStorage(),
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
