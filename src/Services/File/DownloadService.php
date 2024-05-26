<?php

namespace Aasimsajjad22\ImageDownloader\Services\File;

use GuzzleHttp\Client;

class DownloadService
{
    public $failed;
    protected $url;
    protected $contentType;
    protected $file;
    protected $exception;
    protected $userAgentHeader;


    public function __construct(string $fileUrl)
    {
        $this->url = $fileUrl;

        $this->userAgentHeader = ["user-agent" => $_SERVER['HTTP_USER_AGENT']];

        $this->downloadByGuzzle();

        if (!$this->file) {
            $this->failed = true;
        }

        return $this;
    }

    public function get()
    {
        return $this->file;
    }

    public function downloadByGuzzle(bool $verify = true)
    {
        $client = new Client([
            'verify' => $verify,
            'timeout' => 10,
            'headers' => $this->parseHeadersForGuzzle($this->userAgentHeader),
        ]);

        try {
            $file = $client->get($this->url)->getBody()->getContents();
            if ($file) {
                $this->failed = false;
                $this->file = $file;
            } else {
                $this->failed = true;
            }
        } catch (\Exception $e) {
            if (true === $verify) {
                $this->downloadByGuzzle(false);
            }

            $this->failed = true;
        }
    }

    protected function parseHeadersForGuzzle($headersString = null)
    {
        if (!$headersString || !\is_string($headersString)) {
            return [];
        }

        if (false !== strpos($headersString, ':')) {
            $headerArray = array_map('trim', explode(':', $headersString));
            if (2 === \count($headerArray)) {
                return [$headerArray[0] => $headerArray[1]];
            }
        }

        return [];
    }
}