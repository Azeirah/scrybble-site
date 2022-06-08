<?php
declare(strict_types=1);

namespace App\Services;

use App\DataClasses\RemarksConfig;
use App\Events\ReMarkableAuthenticatedEvent;
use App\Helpers\UserStorage;
use App\Jobs\ProcessDownloadedZip;
use Eloquent\Pathogen\Exception\EmptyPathException;
use Eloquent\Pathogen\Path;
use Eloquent\Pathogen\PathInterface;
use Eloquent\Pathogen\RelativePath;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;

/**
 *
 */
class RMapi {
    private Filesystem $storage;

    public function __construct() {
        $this->storage = UserStorage::get(Auth::user());
    }

    /**
     * @return bool
     */
    public function isAuthenticated(): bool {
        return $this->storage->exists('.rmapi-auth');
    }

    /**
     * @param string $command
     * @return array
     */
    #[ArrayShape([Collection::class, 'int'])]
    public function executeRMApiCommand(string $command): array {
        $this->configureEnv();

        $rmapi = base_path('binaries/rmapi');
        $cwd_before = getcwd();
        if (!chdir($this->storage->path(''))) {
            throw new RuntimeException('Could not cd into userdir');
        }
        try {
            exec("$rmapi -ni $command", $output, $exit_code);
        } finally {
            chdir($cwd_before);
        }

        $output = collect($output)->filter(function ($line) {
            if (Str::startsWith($line, 'Refreshing tree')) {
                return false;
            }
            if (Str::startsWith($line, 'WARNING')) {
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
            $index = Str::lower($item);
            if (Str::contains($index, 'incorrect')) {
                throw new InvalidArgumentException('Invalid code');
            }
            if (Str::contains($index, 'failed to create a new device token')) {
                throw new RuntimeException('Failed to create token');
            }
            if (Str::contains($index, 'refresh')) {
                event(new ReMarkableAuthenticatedEvent());
                return true;
            }
        }
        if ($exit_code !== 0) {
            // unknown error for now
            throw new RuntimeException('exit code: ' . $exit_code);
        }
        throw new RuntimeException('unknown error');
    }

    /**
     *
     */
    public function list(string $path = '/'): array {
        [$output, $exit_code] = $this->executeRMApiCommand("ls \"$path\"");

        if ($exit_code !== 0) {
            throw new RuntimeException("rmapi ls path failed with exit code `$exit_code`: " . implode("\n",
                    $output->toArray()));
        }

        return $output->sort()->map(function ($name) use ($path) {
            preg_match('/\[([df])]\s(.+)/', $name, $matches);
            [, $type, $filepath] = $matches;
            return ['type' => $type,
                    'name' => $filepath,
                    'path' => $type === 'd' ? "$path$filepath/" : "$path$filepath"];
        })->all();
    }

    /**
     * @throws EmptyPathException
     */
    public function get(string $filePath): void {
        $rmapi_download_path = Str::replace('"', '\"', $filePath);
        [, $exit_code] = $this->executeRMApiCommand("get \"$rmapi_download_path\"");
        if ($exit_code !== 0) {
            throw new RuntimeException('RMapi `get` command failed');
        }
        $location = $this->getDownloadedZipLocation($rmapi_download_path)->toRelative();

        $folders = RelativePath::fromString($filePath);
        ProcessDownloadedZip::dispatch($location->string(), $folders->replaceName('')->string(), new RemarksConfig(), Auth::user());
    }

    /**
     * @return void
     */
    public function configureEnv(): void {
        putenv('RMAPI_CONFIG=' . $this->storage->path('.rmapi-auth'));
        putenv('XDG_CACHE_HOME=' . $this->storage->path(''));
    }


    /**
     * @param string $rmapiDownloadPath
     * @return PathInterface
     */
    private function getDownloadedZipLocation(string $rmapiDownloadPath): PathInterface {
        $filename = Path::fromString($rmapiDownloadPath)->name();
        return Path::fromString($filename)->joinExtensions('zip');
    }

}
