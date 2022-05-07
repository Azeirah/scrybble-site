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

    /**
     * @param string $command
     * @return array
     */
    public function executeRMApi(string $command): array {
        putenv('RMAPI_CONFIG=' . $this->storage->path('.rmapi-auth'));
        putenv('XDG_CACHE_HOME=' . $this->storage->path(''));
        exec($command, $output, $exit_code);
        return [$output, $exit_code];
    }

    /**
     * @param string $code
     * @return bool
     */
    public function authenticate(string $code): bool {
        $rmapi = base_path('binaries/rmapi');
        [$output, $exit_code] = $this->executeRMApi("echo $code | $rmapi");
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
}
