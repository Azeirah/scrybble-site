<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Sync;
use App\Services\DownloadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 *
 */
class SyncController extends Controller
{
    public function index(DownloadService $downloadService): JsonResponse
    {
        $user = Auth::user();

        $results =
            Sync::forUser($user)
                ->whereIsCompleted()
                ->get(['filename', 'sync_id', 'id'])
                ->map(fn(Sync $sync) => [
                    'download_url' => $downloadService->downloadProcessedRemarksZip($user->id, $sync->sync_id),
                    'filename' => $sync->filename,
                    'id' => $sync->id
                ]);

        return response()->json($results);
    }
}
