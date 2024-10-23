<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Sync;
use App\Services\PRMStorage\PRMStorageInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 *
 */
class SyncController extends Controller
{
    /**
     * @param PRMStorageInterface $PRMStorage
     * @return JsonResponse
     */
    public function index(PRMStorageInterface $PRMStorage): JsonResponse
    {
        $user = Auth::user();

        $results =
            Sync::forUser($user)
                ->whereIsCompleted()
                ->get(['filename', 'sync_id', 'id'])
                ->map(fn(Sync $sync) => [
                    'download_url' => $PRMStorage->getDownloadURL("userZips/" . $sync->sync_id . ".zip"),
                    'filename' => $sync->filename,
                    'id' => $sync->id
                ]);

        return response()->json($results);
    }
}
