<?php

namespace App\Services;

use App\Events\ReMarkableAuthenticatedEvent;
use Eloquent\Pathogen\Exception\EmptyPathException;
use Eloquent\Pathogen\Exception\InvalidPathStateException;
use Eloquent\Pathogen\Path;
use Eloquent\Pathogen\PathInterface;
use Eloquent\Pathogen\RelativePathInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use ZipArchive;

class RMapi {
    private Filesystem $storage;

    public function __construct() {
        $efs = Storage::disk('efs');
        $userDir = "user-" . Auth::user()->id . "/";
        $this->storage = Storage::build($efs->path($userDir));
        if (!$this->storage->exists('')) {
            $this->storage->makeDirectory('');
        }
    }

    public function isAuthenticated(): bool {
        return $this->storage->exists(".rmapi-auth");
    }

    public function executeRMApiCommand(string $command) {
        $this->configureEnv();

        $rmapi = base_path('binaries/rmapi');
        $cwdBefore = getcwd();
        if (!chdir($this->storage->path(''))) {
            throw new RuntimeException("Could not cd into userdir");
        }
        try {
            exec("$rmapi -ni $command", $output, $exit_code);
        } finally {
            chdir($cwdBefore);
        }

        $output = collect($output)->filter(function ($line) {
            if (Str::startsWith($line, 'Refreshing tree')) {
                return false;
            }
            if (Str::startsWith($line, "WARNING")) {
                return false;
            }
            if (Str::contains($line, 'Using the new 1.5 sync')) {
                return false;
            }
            if (Str::contains($line, 'Make sure you have a backup')) {
                return false;
            }
            return true;
        });

        return [$output, $exit_code];
    }

    /**
     * @param string $code
     * @return bool
     */
    public function authenticate(string $code): bool {
        $rmapi = base_path('binaries/rmapi');
        $this->configureEnv();
        exec("echo $code | $rmapi", $output, $exit_code);

        foreach ($output as $item) {
            $i = Str::lower($item);
            if (Str::contains($i, 'incorrect')) {
                throw new InvalidArgumentException('Invalid code');
            }
            if (Str::contains($i, 'failed to create a new device token')) {
                throw new RuntimeException("Failed to create token");
            }
            if (Str::contains($i, 'refresh')) {
                event(new ReMarkableAuthenticatedEvent());
                return true;
            }
        }
        if ($exit_code !== 0) {
            // unknown error for now
            throw new RuntimeException('exit code: ' . $exit_code);
        }
        throw new RuntimeException("unknown error");
    }

    public function list(string $path = "/"): array {
        [$output, $exit_code] = $this->executeRMApiCommand("ls \"$path\"");

        return $output->sort()->map(function ($name) use ($path) {
            preg_match("/\[([df])]\s(.+)/", $name, $matches);
            [, $type, $filepath] = $matches;
            return ['type' => $type,
                    'name' => $filepath,
                    'path' => $type === "d" ? "$path$filepath/" : "$path$filepath"];
        })->all();
    }

    /**
     * @throws EmptyPathException
     * @throws InvalidPathStateException
     */
    public function get(string $filePath): bool {
        $rmapiDownloadPath = Str::replace('"', '\"', $filePath);
        [$output, $exit_code] = $this->executeRMApiCommand("get \"$rmapiDownloadPath\"");
        if ($exit_code === 0) {
            $location = $this->getDownloadedZipLocation($rmapiDownloadPath)->toRelative();
        }

        return $exit_code === 0;
    }

    /**
     * @return void
     */
    public function configureEnv(): void {
        putenv('RMAPI_CONFIG=' . $this->storage->path(".rmapi-auth"));
        putenv('XDG_CACHE_HOME=' . $this->storage->path(''));
    }


    private function getDownloadedZipLocation(string $rmapiDownloadPath): PathInterface {
        $filename = Path::fromString($rmapiDownloadPath)->name();
        return Path::fromString($filename)->joinExtensions("zip");
    }

}
