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
     * Creates all directories under $USERDIR/files/$filepath
     * Returns path relative to storage (to use in $torage->path($returnValue)
     *
     * @param PathInterface $filepath A path including a file at the end.
     * @param Filesystem $storage
     * @return PathInterface Path relative to storage root (includes user-dir),
     *              excludes filename
     * @throws InvalidPathStateException
     */
    public static function ensureDirectoryTreeExists(PathInterface $filepath, Filesystem $storage):
    PathInterface {
        /**
         * The "files" directory prevents a potential problem
         * Consider these files existing on your remarkable
         * ./notes
         * ./work/notes
         * rmapi downloads all files to the XDG_CACHE_DIR, so the original
         * "notes" would be overwritten if ./work/notes got downloaded later
         * than the ./notes file.
         * For that reason, I create the "files" dir where the rm directory
         * structure gets mirrored as files get downloaded
         */
        $atoms = $filepath->atoms();

        // last atom is file
        unset($atoms[count($atoms) - 1]);
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
     * @param RelativePathInterface $to
     * @return void
     * @throws InvalidPathStateException
     */
    public function extractDownloadedZip(Filesystem $storage, RelativePathInterface $to): void {
        $zip = new ZipArchive();
        $result = $zip->open($storage->path($to));
        if ($result === true) {
            $extract_result = $zip->extractTo($storage->path(Path::fromString($to)->parent()->normalize()));
            if ($extract_result !== true) {
                $zip->close();
                throw new RuntimeException("Unable to extract zip");
            }
        } else {
            throw new RuntimeException("Unable to open zip");
        }
    }

}
