<?php

namespace App\Services\interfaces;

use App\DataClasses\SyncContext;

/**
 * PRM stands for [P]rocessed [R]e[M]arkable file
 */
interface PRMStorageInterface
{
    public function store(string $path, string $zipFileContents);
    public function getDownloadURL(string $path);
}
