<?php

namespace App\Services;

use App\Helpers\UserStorage;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class DownloadService
{
    public function downloadReMarkableInputZip(User $user, string $sync_id)
    {
//        $storage = UserStorage::get($user);
//
//        return $storage->get("")
    }

    public function downloadProcessedRemarksZip(User $user, string $sync_id): string
    {
        $storage = Storage::disk('efs');
        $user_id = $user->id;
        return $storage->temporaryUrl("user-{$user_id}/processed/${sync_id}.zip", now()->addMinutes(5));
    }
}
