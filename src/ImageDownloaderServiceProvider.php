<?php
namespace Aasimsajjad22\ImageDownloader;

use Illuminate\Support\ServiceProvider;

class ImageDownloaderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Services\ImageService::class, function ($app) {
            return new Services\ImageService();
        });
    }

    public function boot()
    {
        // Boot logic if any
    }
}