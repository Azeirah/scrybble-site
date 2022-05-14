<?php

namespace App\Services;

use App\Events\ReMarkableAuthenticatedEvent;
use App\Helpers\FileManipulations;
use Eloquent\Pathogen\Exception\EmptyPathException;
use Eloquent\Pathogen\Exception\InvalidPathStateException;
use Eloquent\Pathogen\Path;
use Eloquent\Pathogen\PathInterface;
use Exception;
use http\Exception\InvalidArgumentException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

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
            $to = $this->moveDownloadedFileToUserDir($filePath);
            $this->extractDownloadedZip($to);
            $this->storage->delete($to);
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
     * @param string $filePath
     * @return PathInterface
     * @throws EmptyPathException
     * @throws InvalidPathStateException
     */
    public function moveDownloadedFileToUserDir(string $filePath): PathInterface {
        $fullPath =
            Path::fromString($this->userDir)->joinAtoms("files")->join(Path::fromString($filePath)->toRelative());
        $destination_dir = FileManipulations::ensureDirectoryTreeExists($fullPath, $this->storage);
        $filePathWithExtension = (Path::fromString($filePath))->joinExtensions('zip')->name();
        $from = (Path::fromString($this->userDir))->joinAtoms($filePathWithExtension)->toRelative();
        $to = $destination_dir->joinAtoms($filePathWithExtension);
        $this->storage->move($from, $to);
        return $to;
    }

    /**
     * @param PathInterface $to
     * @return void
     * @throws InvalidPathStateException
     */
    public function extractDownloadedZip(PathInterface $to): void {
        $zip = new \ZipArchive();
        $result = $zip->open($this->storage->path($to));
        if ($result === true) {
            $extract_result = $zip->extractTo($this->storage->path(Path::fromString($to)->parent()->normalize()));
            if ($extract_result !== true) {
                $zip->close();
                throw new \RuntimeException("zip extract problem");
            }
        } else {
            throw new \RuntimeException("zip problem");
        }
    }
}
