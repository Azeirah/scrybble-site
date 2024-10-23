<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\DataClasses\SyncContext;
use App\Jobs\DownloadRemarkableFileJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;

/**
 *
 */
class FileController extends Controller
{
    /**
     * @param Request $request
     * @return void
     */
    public function show(Request $request): void
    {
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException();
        }
        $input_filename = $request->get('file');
        $sync_context = new SyncContext($input_filename, $user);
        Log::info("user=`$user` requested file file=`$input_filename`; Dispatching `DownloadRemarkableFileJob`");
        DownloadRemarkableFileJob::dispatch($sync_context);
    }
}
