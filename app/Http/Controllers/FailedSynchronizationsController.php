<?php

namespace App\Http\Controllers;

use App\Helpers\UserStorage;
use App\Models\Sync;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class FailedSynchronizationsController extends Controller
{
    public function index()
    {
        $failed_syncs = Sync
            ::where('completed', false)
            ->where('message', 'Start processing downloaded files')
            ->orWhere('severity', 'error')
            ->join('sync_logs', 'sync_id', '=', 'sync.id')
            ->join('users', 'sync.user_id', '=', 'users.id')
            ->get(['sync.id', 'sync.filename', 'severity', 'context->user->id as user_id', 'context', 'sync.created_at', 'users.name'])
            ->groupBy('id')
            ->filter(fn($fs) => is_numeric($fs[0]['user_id']))
            ->filter(fn($fs) => sizeof($fs) > 1)
            ->map(fn($fs) => [
                'id' => $fs[0]['id'],
                'filename' => $fs[0]['filename'],
                'user' => $fs[0]['name'],
                'user_id' => $fs[0]['user_id'],
                'contexts' => $fs[1]['context'],
                'created_at' => $fs[0]['created_at']->diffForHumans()
            ]);
        return view('admin.failedSyncs', [
            'failed_syncs' => $failed_syncs
        ]);
    }

    public function download()
    {
        $user = User::find(request()->query('user'));
        $path = request()->query('path') . ".zip";
        $userStorage = UserStorage::get($user);

        $storage = Storage::disk('s3');
        $file = $userStorage->get($path);
        $s3_path = "failed_sync/$path";
        $storage->put($s3_path, $file);

        return redirect($storage->temporaryUrl($s3_path, now()->addMinutes(1)));
    }


}
