<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Sync;
use App\Services\interfaces\PRMStorageInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

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

        // TODO: Isn't this typically done with a Laravel guard or policy?
        if ($user === null) {
            throw new UnauthorizedException('You need to be logged in in order to interact with the API');
        }

        $results =
            Sync::forUser($user)
                ->whereIsCompleted()
                ->get(['filename', 'S3_download_path', 'id'])
                ->map(fn(Sync $sync) => [
                    'download_url' => $PRMStorage->getDownloadURL($sync->S3_download_path),
                    'filename' => $sync->filename,
                    'id' => $sync->id
                ]);

        return response()->json($results);
    }
}
