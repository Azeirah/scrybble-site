<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class UserStorage {
    public static function get(User $user): Filesystem {
        $efs = Storage::disk('efs');
        $userDir = "user-" . $user->id . "/";
        $storage = Storage::build($efs->path($userDir));
        if (!$storage->exists('')) {
            $storage->makeDirectory('');
        }

        return $storage;
    }
}
