<?php
namespace Aasimsajjad22\ImageDownloader;

use Illuminate\Support\Facades\Facade;

class ImageDownloaderFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Services\ImageService::class;
    }
}