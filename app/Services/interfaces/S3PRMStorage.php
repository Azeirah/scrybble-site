<?php

namespace App\Services\interfaces;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

class S3PRMStorage implements PRMStorageInterface
{
    /**
     * @param string $path
     * @param string $prmFileContents
     * @return void
     */
    public function store(string $path, string $prmFileContents): void
    {
        $success = Storage::disk('s3')->put($path, $prmFileContents);
        if (!$success) {
            throw new RuntimeException('Failed to upload zip to S3');
        }
    }
}
