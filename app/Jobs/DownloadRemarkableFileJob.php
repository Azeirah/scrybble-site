<?php

namespace App\Jobs;

use App\DataClasses\SyncContext;
use App\Events\RemarkableFileDownloadedEvent;
use App\Services\RMapi;
use Eloquent\Pathogen\Exception\EmptyPathException;
use Eloquent\Pathogen\Exception\NonAbsolutePathException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DownloadRemarkableFileJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private SyncContext $sync_context;

    public function __construct($sync_context) {
        $this->sync_context = $sync_context;
    }

    /**
     * @throws NonAbsolutePathException
     * @throws EmptyPathException
     */
    public function handle(RMapi $RMapi) {
        $this->sync_context->logStep("Downloading file");
        $results = $RMapi->get($this->sync_context->input_filename);
        $output = $results['output'];
        $this->sync_context->logStep("Remarks API finished: `$output`");
        $this->sync_context->downloaded_zip_location = $results['downloaded_zip_location'];
        $this->sync_context->folder = $results['folder'];

        event(new RemarkableFileDownloadedEvent($this->sync_context));
    }
}
