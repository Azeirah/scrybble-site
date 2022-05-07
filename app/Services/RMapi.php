<?php

namespace App\Services;

use App\Events\ReMarkableAuthenticatedEvent;
use Exception;
use http\Exception\InvalidArgumentException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use function PHPUnit\Framework\directoryExists;

class RMapi {
    public function isAuthenticated() {
        return false;
    }

    private function userPath(string $path = "") {
        if (strlen($path)) {
            return storage_path("user-" . Auth::user()->id . "/" . $path);
        }
        return storage_path("user-" . Auth::user()->id . "/");
    }

    private function getRMApiPath() {
        return base_path('binaries/rmapi');
    }

    private function resetUserFolder() {
        $path =
            $this->userPath();
        $auth =
            $this->userPath('.rmapi-auth');
        $tree = $this->userPath('rmapi/.tree');
        $rmapiDir = $this->userPath('rmapi');

        if (file_exists($path)) {
            if (file_exists($auth)) {
                unlink($auth);
            }
            if (file_exists($tree)) {
                unlink($tree);
            }
            if (file_exists($rmapiDir)) {
                rmdir($rmapiDir);
            }
            rmdir($path);
        }
        mkdir($path);
    }

    /**
     * @param string $code
     * @return bool
     */
    public function authenticate(string $code): bool {
        $rmapi =
            $this->getRMApiPath();
        $this->resetUserFolder();
        putenv('RMAPI_CONFIG=' . $this->userPath('.rmapi-auth'));
        putenv('XDG_CACHE_HOME=' . $this->userPath());
        exec("echo $code | $rmapi", $output, $exit_code);
        foreach ($output as $item) {
            $i =
                Str::lower($item);
            if (Str::contains($i, 'incorrect')) {
                throw new InvalidArgumentException('Invalid code');
            }
            if (Str::contains($i, 'failed to create a new device token')) {
                throw new RuntimeException("Failed to create token");
            }
            if (Str::contains($i, 'refresh')) {
                $s = Storage::build($this->userPath());
                event(new ReMarkableAuthenticatedEvent($s->get('.rmapi-auth')));
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
