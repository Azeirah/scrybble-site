<?php

namespace App\Services\interfaces;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class S3PRMStorage implements PRMStorageInterface
{
    private FilesystemAdapter|Filesystem $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('s3');
    }

    /**
     * @param string $path
     * @param string $prmFileContents
     * @return void
     */
    public function store(string $path, string $prmFileContents): void
    {
        $success = $this->disk->put($path, $prmFileContents);
        if (!$success) {
            throw new RuntimeException('Failed to upload zip to S3');
        }
    }

    public function getDownloadURL(string $path)
    {
        return $this->disk->temporaryUrl($path, now()->addMinutes(5));
    }
}
