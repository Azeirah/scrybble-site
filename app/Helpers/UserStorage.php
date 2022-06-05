<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

/**
 * Creates a Storage for an individual user based on their ID
 */
class UserStorage {
    /**
     * @param User $user
     * @return Filesystem
     */
    public static function get(User $user): Filesystem {
        $efs = Storage::disk('efs');
        $user_dir = "user-{$user->id}/";
        $storage = Storage::build($efs->path($user_dir));
        if (!$storage->exists('')) {
            $storage->makeDirectory('');
        }

        return $storage;
    }
}
