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
    private string $userDir;

    public function __construct() {
        $this->storage = Storage::disk('efs');
        $this->userDir = "user-" . Auth::user()->id . "/";
        if (!$this->storage->exists($this->userDir)) {
            $this->storage->makeDirectory($this->userDir);
        }
    }

    public function isAuthenticated(): bool {
        return $this->storage->exists("{$this->userDir}.rmapi-auth");
    }

    public function executeRMApiCommand(string $command) {
        $this->configureEnv();

        $rmapi = base_path('binaries/rmapi');
        $cwdBefore = getcwd();
        chdir($this->storage->path($this->userDir));
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
        $rmapi = base_path('binaries/rmapi');
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
            $this->extractDownloadedZip($location);
            $deleted = $this->storage->delete($location->string());
            if (!$deleted) {
                throw new \RuntimeException("Failed to delete zip");
            }
        }

        return $exit_code === 0;
    }

    /**
     * @return void
     */
    public function configureEnv(): void {
        putenv('RMAPI_CONFIG=' . $this->storage->path("{$this->userDir}.rmapi-auth"));
        putenv('XDG_CACHE_HOME=' . $this->storage->path($this->userDir));
    }

    /**
     * @param RelativePathInterface $to
     * @return void
     * @throws InvalidPathStateException
     */
    public function extractDownloadedZip(RelativePathInterface $to): void {
        $zip = new ZipArchive();
        $result = $zip->open($this->storage->path($to));
        if ($result === true) {
            $extract_result = $zip->extractTo($this->storage->path(Path::fromString($to)->parent()->normalize()));
            if ($extract_result !== true) {
                $zip->close();
                throw new RuntimeException("Unable to extract zip");
            }
        } else {
            throw new RuntimeException("Unable to open zip");
        }
    }

    private function getDownloadedZipLocation(string $rmapiDownloadPath): PathInterface {
        $filename = Path::fromString($rmapiDownloadPath)->name();
        return Path::fromString($this->userDir)->joinAtoms($filename)->joinExtensions("zip");
    }

}
