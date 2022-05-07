<?php

namespace App\Services;

use App\Events\ReMarkableAuthenticatedEvent;
use http\Exception\InvalidArgumentException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class RMapi {
    private Filesystem $storage;

    public function __construct() {
        $this->storage = Storage::disk('efs');
        if (!$this->storage->exists("user-" . Auth::user()->id)) {
            $this->storage->makeDirectory("user-" . Auth::user()->id);
        }
    }

    public function isAuthenticated(): bool {
        return $this->storage->exists('.rmapi-auth');
    }

    public function executeRMApiCommand(string $command) {
        putenv('RMAPI_CONFIG=' . $this->storage->path('.rmapi-auth'));
        putenv('XDG_CACHE_HOME=' . $this->storage->path(''));

        $rmapi = base_path('binaries/rmapi');
        exec("$rmapi -ni $command", $output, $exit_code);

        $output = collect($output)->filter(function ($line) {
            if (Str::startsWith($line, 'Refreshing tree')) {
                return false;
            }
            if (Str::startsWith( $line, "WARNING")) {
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
        putenv('RMAPI_CONFIG=' . $this->storage->path('.rmapi-auth'));
        putenv('XDG_CACHE_HOME=' . $this->storage->path(''));
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

    public function list(string $path="/"): Array {
        $rmapi = base_path('binaries/rmapi');
        [$output, $exit_code] = $this->executeRMApiCommand("ls $path");

        return $output->sort()->map(function ($name) use ($path) {
            preg_match("/\[([df])]\s(.+)/", $name, $matches);
            [, $type, $filepath] = $matches;
            return [
                'type' => $type,
                'name' => $filepath,
                'path' => "$path$filepath/"
            ];
        })->all();
    }
}
