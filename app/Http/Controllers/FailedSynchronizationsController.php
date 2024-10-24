<?php

namespace App\Http\Controllers;

use App\Models\RemarkableDocumentShare;
use App\Services\DownloadService;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;

class FailedSynchronizationsController extends Controller
{
    public function index(DownloadService $downloadService)
    {
        $shared = RemarkableDocumentShare::with("sync:id,filename,created_at,sync_id,user_id")->where('developer_access_consent_granted', true)->get();

        return view("admin.failedSyncs", [
            'shared' => $shared->map(function ($shared) use ($downloadService) {
                $public_sync_id = $shared->sync->sync_id;

                $output_href = null;
                if ($public_sync_id) {
                    try {
                        $output_href = $downloadService->downloadProcessedRemarksZip($shared['user_id'], $public_sync_id);
                    } catch (GoneHttpException) {}
                }

                $input_href = null;
                if ($public_sync_id) {
                    try {
                        $input_href = $downloadService->downloadReMarkableInputZip($shared['user_id'], $public_sync_id);
                    } catch (GoneHttpException) {}
                }

                return [
                    'id' => $public_sync_id,
                    'created_at' => $shared->sync->created_at,
                    'output_href' => $output_href,
                    'input_href' => $input_href,
                    'user' => $shared->sync->user_id,
                    'filename' => $shared->sync->filename,
                ];
            })
        ]);
    }
}
