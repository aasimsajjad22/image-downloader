<?php

namespace Aasimsajjad22\ImageDownloader\Services;

use Aasimsajjad22\ImageDownloader\Services\File\DownloadService;
use Exception;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManagerStatic;

class ImageService
{
    public $failed;

    protected $image;
    protected $imageName;
    protected $mime;
    protected $extension;
    protected $isResized = false;
    protected $originalFilename;

    public function __construct()
    {
        ImageManagerStatic::configure(['driver' => 'gd']);
    }

    public function downloadImage($image, $resize = true)
    {
        try {
            $downloadedImage = Image::make($image);
        } catch (Exception $ex) {

            $downloadedImage = new DownloadService($image);

            if ($downloadedImage->failed) {
                $this->failed = true;
            } else {
                $downloadedImage = $downloadedImage->get();
                try {
                    $downloadedImage = ImageManagerStatic::make($downloadedImage);
                } catch (Exception $e) {
                    $this->failed = true;
                }
            }
        }

        if ($downloadedImage && !$this->failed) {
            $this->image = $downloadedImage;
            $this->image->backup();
            $this->originalFilename = pathinfo($image)['basename'];
            if ($resize) {
                $this->resize();
            }
            $this->mime = $this->image->mime();
            $this->setExtension();
        } else {
            $this->failed = true;
            return false;
        }

        return $this->image;
    }

    public function resize(?int $width = 1200, ?int $height = null, $aspectRatio = true, $upsizeProtection = true)
    {
        $this->isResized = true;
        $this->image = $this->image->resize(
            $width,
            $height,
            function ($constraint) use ($aspectRatio, $upsizeProtection) {
                if ($aspectRatio) {
                    $constraint->aspectRatio();
                }
                if ($upsizeProtection) {
                    $constraint->upsize();
                }
            }
        );
    }

    public function get($asStream = true)
    {
        return ($asStream) ? $this->stream() : $this->image;
    }

    public function stream()
    {
        return $this->image->stream();
    }

    public function setName(?string $prefix = null, int $charLength = 16): string
    {
        $imageName = ($prefix) ? $prefix . '-' : '';
        $imageName .= Str::random($charLength);
        $this->imageName = $imageName;

        return $this->imageName;
    }

    public function getName(bool $include_extension = true): string
    {
        if (!$this->imageName) {
            $this->setName();
        }

        return ($include_extension)
            ? $this->imageName . '.' . $this->getExtension()
            : $this->imageName;
    }

    public function setContentType()
    {
        if (!$this->mime) {
            $this->mime = $this->image->mime();
        }

        return $this;
    }

    public function getContentType()
    {
        if (!$this->mime) {
            $this->setContentType();
        }

        return $this->mime;
    }

    public function setExtension()
    {
        if (!$this->mime) {
            $this->setContentType();
        }
        $this->extension = explode('/', $this->mime)[1] ?? 'png';

        return $this;
    }

    public function getExtension(): string
    {
        if (!$this->mime) {
            $this->setExtension();
        }

        return $this->extension;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function isResized()
    {
        return $this->isResized;
    }
}
