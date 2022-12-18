<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Sync;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\UnauthorizedException;

/**
 *
 */
class SyncController extends Controller {
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse {
        $user = Auth::user();

        if ($user === null) {
            throw new UnauthorizedException('You need to be logged in in order to interact with the API');
        }
        $disk = Storage::disk('s3');
        $results =
            Sync::forUser($user)
                ->whereIsCompleted()
                ->get(['filename', 'S3_download_path', 'id'])
                ->map(fn(Sync $sync) => [
                    'download_url' => $disk->temporaryUrl(
                        $sync->S3_download_path,
                        now()->addMinutes(5)),
                    'filename' => $sync->filename,
                    'id' => $sync->id
                ]);

        return response()->json($results);
    }
}
