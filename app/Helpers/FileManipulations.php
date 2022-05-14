<?php

namespace App\Helpers;

use Eloquent\Pathogen\Exception\EmptyPathException;
use Eloquent\Pathogen\Exception\InvalidPathStateException;
use Eloquent\Pathogen\Path;
use Eloquent\Pathogen\PathInterface;
use Illuminate\Contracts\Filesystem\Filesystem;

class FileManipulations {
    /**
     * Creates all directories under $USERDIR/files/$filepath
     * Returns path relative to storage (to use in $torage->path($returnValue)
     *
     * @param string $filepath
     * @param string $start
     * @param Filesystem $storage
     * @return PathInterface Path relative to storage root (includes user-dir),
     *              excludes filename
     * @throws EmptyPathException
     * @throws InvalidPathStateException
     */
    public static function ensureDirectoryTreeExists(string $filepath, string $start, Filesystem $storage): PathInterface {
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
        $atoms = Path::fromString($start)
                     ->joinAtoms("files")
                     ->join(Path::fromString($filepath)->toRelative())
                     ->atoms();

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

}
