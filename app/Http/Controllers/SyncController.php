<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Sync;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

/**
 *
 */
class SyncController extends Controller {
    /**
     * @return mixed
     */
    public function index(): mixed {
        $user = Auth::user();

        if ($user === null) {
            throw new UnauthorizedException('You need to be logged in in order to interact with the API');
        }
        return Sync::forUser($user)->get(['filename', 'S3_download_path', 'updated_at']);
    }
}
