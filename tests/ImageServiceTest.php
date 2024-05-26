<?php

namespace Aasimsajjad22\tests;

use Aasimsajjad22\ImageDownloader\Services\ImageService;
use PHPUnit\Framework\TestCase;

class ImageServiceTest extends TestCase
{

    public function testDownloadImageWithValidUrl()
    {
        $imageUrl = 'https://cdn.pixabay.com/photo/2013/07/21/13/00/rose-165819_640.jpg';

        $imageService = new ImageService();

        $image = $imageService->downloadImage($imageUrl);
        $this->assertNotNull($image);
        $this->assertNull($imageService->failed);
    }

    public function testResize()
    {
        $imageUrl = 'https://cdn.pixabay.com/photo/2013/07/21/13/00/rose-165819_640.jpg';

        $imageService = new ImageService();
        $imageService->downloadImage($imageUrl);

        // Resize the image
        $imageService->resize(800, 600);

        // Assert that the image has been resized
        $this->assertTrue($imageService->isResized());
    }
}