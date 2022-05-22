<?php

namespace App\Helpers;

use Eloquent\Pathogen\AbsolutePath;
use Eloquent\Pathogen\Exception\EmptyPathException;
use Eloquent\Pathogen\Exception\InvalidPathStateException;
use Eloquent\Pathogen\Path;
use Eloquent\Pathogen\PathInterface;
use Eloquent\Pathogen\RelativePath;
use Eloquent\Pathogen\RelativePathInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use RuntimeException;
use ZipArchive;

class FileManipulations {
    /**
     * @param Filesystem $storage
     * @param RelativePathInterface $filepath A path including a file at the end.
     * @return PathInterface Path relative to storage root (includes user-dir),
     *              excludes filename
     * @throws InvalidPathStateException
     */
    public static function ensureDirectoryTreeExists(Filesystem $storage, RelativePathInterface $filepath): PathInterface {
        $atoms = $filepath->atoms();

        // last atom is file
        $tree = Path::fromString("");
        foreach ($atoms as $directory_name) {
            $tree = $tree->joinAtoms($directory_name);
            $dir_path = $tree->toAbsolute();

            if (!$storage->exists($dir_path)) {
                $storage->makeDirectory($dir_path);
            }
        }
        return $tree;
    }

    /**
     * @param Filesystem $storage
     * @param RelativePathInterface $from
     * @param RelativePathInterface $to
     * @return void
     */
    public static function extractZip(Filesystem $storage, RelativePathInterface $from, RelativePathInterface $to): void {
        $zip = new ZipArchive();
        $result = $zip->open($storage->path($from));
        if ($result === true) {
            $extract_result = $zip->extractTo($storage->path($to));
            if ($extract_result !== true) {
                $zip->close();
                throw new RuntimeException("Unable to extract zip");
            }
        } else {
            throw new RuntimeException("Unable to open zip");
        }
    }

}
