<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\DataClasses\RemarksConfig;
use App\DataClasses\SyncContext;
use App\Jobs\DownloadRemarkableFileJob;
use App\Services\RMapi;
use Eloquent\Pathogen\Exception\EmptyPathException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $sync_context = new SyncContext($request->get('file'), Auth::user(), new RemarksConfig());
        DownloadRemarkableFileJob::dispatch($sync_context);
    }
}
