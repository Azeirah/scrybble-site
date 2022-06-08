<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Sync;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\UnauthorizedException;

/**
 *
 */
class SyncController extends Controller {
    /**
     * @return array
     */
    public function index(): array {
        $user = Auth::user();

        if ($user === null) {
            throw new UnauthorizedException('You need to be logged in in order to interact with the API');
        }
        $disk = Storage::disk('s3');
        return Sync::forUser($user)
                   ->get(['filename', 'S3_download_path'])
                   ->map(fn($sync) => ['download_url' => $disk->temporaryUrl($sync->S3_download_path,
                       now()->addMinutes(5)),
                                       'filename' => $sync->filename]);
    }
}
