<?php

namespace App\Helpers;

use Eloquent\Pathogen\Exception\InvalidPathStateException;
use Eloquent\Pathogen\Path;
use Eloquent\Pathogen\PathInterface;
use Eloquent\Pathogen\RelativePathInterface;
use FilesystemIterator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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

    public static function zipDirectory(Filesystem $storage, RelativePathInterface $from, RelativePathInterface $to): void {
        $zip = new ZipArchive();
        $zipLocation = $storage->path($to->string());
        if ($zip->open($zipLocation, flags: ZipArchive::CREATE) !== true) {
            throw new RuntimeException("Was unable to open zip at $zipLocation");
        }

        $root = $storage->path($from);
        $dirIter = new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS);
        $iter = new RecursiveIteratorIterator($dirIter);

        $removeRoot = Path::fromString($storage->path($from))->normalize()->string();
        foreach ($iter as $info) {
            $path = $info->getPathname();
            // name inside zip, otherwise includes whole path like /var/www/html/.....
            $entry = Str::replace(search: $removeRoot, replace: '', subject: $path);
            echo $path . PHP_EOL;

            if (is_dir($path)) {
                $zip->addEmptyDir($path, $entry);
            } else if (is_file($path)) {
                $zip->addFile($path, $entry);
            }
        }

        if (!$zip->close()) {
            throw new RuntimeException("Was unable to close zip after creation");
        }
    }

}
