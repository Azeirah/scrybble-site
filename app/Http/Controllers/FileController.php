<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\DataClasses\RemarksConfig;
use App\DataClasses\SyncContext;
use App\Jobs\DownloadRemarkableFileJob;
use App\Models\SyncSetting;
use App\Services\RMapi;
use Eloquent\Pathogen\Exception\EmptyPathException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;

/**
 *
 */
class FileController extends Controller {
    /**
     * @param Request $request
     * @param RMapi $RMapi
     * @return void
     * @throws EmptyPathException
     */
    public function show(Request $request): void {
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException();
        }
        $input_filename = $request->get('file');
        $settings = SyncSetting
            ::where('user_id', $user?->id ?? -1)
            ->where('filename', $input_filename)
            ->firstOrCreate([
                'filename' => $input_filename,
                'user_id' => $user->id
            ]);
        $sync_context = new SyncContext($input_filename, $user, new RemarksConfig());
        Log::info("user=`$user` requested file file=`$input_filename`; Dispatching `DownloadRemarkableFileJob`");
        DownloadRemarkableFileJob::dispatch($sync_context);
    }
}
