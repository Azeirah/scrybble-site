<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;

class DownloadService
{
    public function downloadReMarkableInputZip(User $user, string $sync_id)
    {
//        $storage = UserStorage::get($user);
//
//        return $storage->get("")
    }

    /**
     * @param int $user_id
     * @param string $sync_id
     * @return string
     */
    public function downloadProcessedRemarksZip(int $user_id, string $sync_id): string
    {
        $storage = Storage::disk('efs');

        $path = "user-{$user_id}/processed/${sync_id}.zip";
        if ($storage->exists($path)) {
            return $storage->temporaryUrl($path, now()->addMinutes(5));
        }
        throw new GoneHttpException("File has been deleted");
    }
}
