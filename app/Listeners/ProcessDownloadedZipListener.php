<?php

namespace App\Listeners;

use App\Events\RemarkableFileDownloadedEvent;
use App\Helpers\FileManipulations;
use App\Helpers\UserStorage;
use App\Services\RemarkableService;
use App\Services\RemarksService;
use Eloquent\Pathogen\AbsolutePath;
use Eloquent\Pathogen\RelativePath;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ProcessDownloadedZipListener implements ShouldQueue {
    public function __construct(private RemarksService $remarks_service, private RemarkableService $remarkable_service) {}

    public function handle(RemarkableFileDownloadedEvent $evt) {
        $sync_context = $evt->sync_context;
        $sync_context->logStep("Start processing downloaded files", $sync_context->toArray());
        $sync_context->logStep("Creating folder for processing");
        $zipLocation = new RelativePath([$sync_context->downloaded_zip_location]);
        $user_storage = UserStorage::get($sync_context->user);

        $jobdir = new RelativePath(['jobs', $sync_context->sync_id]);
        $to = $jobdir->joinAtoms('extractedFiles')->toRelative();

        // 1. Create a temporary folder and move the zip to this location.
        FileManipulations::ensureDirectoryTreeExists($user_storage, $to);
        $sync_context->logStep("Folder created");

        // 2. Extract the zip
        $sync_context->logStep("Extracting zip");
        FileManipulations::extractZip($user_storage, from: $zipLocation, to: $to);
        $sync_context->logStep("Extracted zip");

        $version = $this->remarkable_service->determineVersion($user_storage, $to);
        $sync_context->logStep("formatVersion is $version", [
            "version" => $version
        ]);

        // 3. Delete the zip
        // TODO


        switch ($version) {
            // ReMarkable software V2
            case 1:
            {
                // 4. Run remarks over the extracted files
                $absolute_job_dir =
                    AbsolutePath::fromString($user_storage->path($jobdir->joinAtoms('extractedFiles')->string()));
                $absolute_outdir = AbsolutePath::fromString($user_storage->path($jobdir->joinAtoms('out')));
                try {
                    $sync_context->logStep("Processing ReMarkable file");
                    $this->remarks_service->extractNotesAndHighlights(
                        sourceDirectory: $absolute_job_dir,
                        targetDirectory: $absolute_outdir,
                        config: $sync_context->remarks_config);
                    $sync_context->logStep("Processed ReMarkable file");
                } catch (RuntimeException $exception) {
                    $sync_context->logError("Extraction failed. Error", [
                        "error" => $exception->getMessage()
                    ]);
                    //            if (true || $this->user->config()->telemetryEnabled) {
                    throw $exception;
                    //            }
                }
                break;
            }
            // ReMarkable software V3+
            case 2:
            {
                $sync_context->logError("Cannot process ReMarkable version 3 files yet :(");
            }
        }

        // 5. Zip the out dir
        $sync_context->logStep("Zipping results");
        $from = $jobdir->joinAtoms('out')->toRelative();
        $to1 = $jobdir->joinAtoms('out.zip')->toRelative();
        try {
            FileManipulations::zipDirectory($user_storage,
                from: $from,
                to: $to1);
        } catch (RuntimeException $e) {
            $sync_context->logError("Failed to zip", [
                "error" => $e->getMessage()
            ]);
            throw $e;
        }
        $sync_context->logStep("Zipped results");

        // 6. Upload zip to S3
        $sync_context->logStep("Uploading zip to storage");
        $s3_download_path = "userZips/{$sync_context->sync_id}.zip";
        if (!Storage::disk('s3')->put($s3_download_path, $user_storage->get($to1))) {
            $sync_context->logStep("Failed to upload zip to storage");
            throw new RuntimeException('Unable to upload zip to s3');
        }
        $sync_context->logStep("Uploaded zip to storage");

        // 7. Unless user has telemetry=on && remarksService exception happened, delete temporary folder
        // 8. Insert a row in "sync" table
        $sync_context->logStep("Wrapping up sync, making available to user");

        $user = $sync_context->user;
        $lock = Cache::lock('append-to-sync-table-for-userid-' . $user->id, 10);
        $lock->get(fn() => $sync_context->sync->complete($s3_download_path));
    }
}
