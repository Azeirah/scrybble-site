<?php
declare(strict_types=1);

namespace App\Jobs;

use App\DataClasses\RemarksConfig;
use App\Helpers\FileManipulations;
use App\Helpers\UserStorage;
use App\Models\Sync;
use App\Models\User;
use App\Services\RemarkableService;
use App\Services\RemarksService;
use Eloquent\Pathogen\AbsolutePath;
use Eloquent\Pathogen\Exception\EmptyPathException;
use Eloquent\Pathogen\Exception\InvalidPathStateException;
use Eloquent\Pathogen\Exception\NonAbsolutePathException;
use Eloquent\Pathogen\RelativePath;
use Eloquent\Pathogen\RelativePathInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use JsonException;
use RuntimeException;

/**
 *
 */
class ProcessDownloadedZip implements ShouldQueue {
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private RelativePathInterface $zipLocation;
    private RemarksConfig $remarksConfig;
    private User $user;
    /**
     * @var string Path of the file in the user-facing RM directory structure
     */
    private string $RMFilePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $zipLocation, string $RMFilePath, RemarksConfig $remarksConfig, User $user) {
        $this->zipLocation = new RelativePath([$zipLocation]);
        $this->remarksConfig = $remarksConfig;
        $this->user = $user;
        $this->RMFilePath = $RMFilePath;
    }

    /**
     * Execute the job.
     *
     * @param RemarksService $remarksService
     * @param RemarkableService $remarkableService
     * @return void
     * @throws EmptyPathException
     * @throws InvalidPathStateException
     * @throws JsonException
     * @throws NonAbsolutePathException
     */
    public function handle(RemarksService $remarksService, RemarkableService $remarkableService): void {
        $user_storage = UserStorage::get($this->user);
        $job_id = $this->job->getJobId();

        $jobdir = new RelativePath(['jobs', $job_id]);
        $to = $jobdir->joinAtoms('extractedFiles')->toRelative();

        // 1. Create a temporary folder and move the zip to this location.
        FileManipulations::ensureDirectoryTreeExists($user_storage, $to);

        // 2. Extract the zip
        FileManipulations::extractZip($user_storage, from: $this->zipLocation, to: $to);

        // 3. Delete the zip
        // TODO


        // 4. Run remarks over the extracted files
        $absolute_job_dir = AbsolutePath::fromString($user_storage->path($jobdir->joinAtoms('extractedFiles')->string()));
        $absolute_outdir = AbsolutePath::fromString($user_storage->path($jobdir->joinAtoms('out')));
        try {
            $remarksService->extractNotesAndHighlights(
                sourceDirectory: $absolute_job_dir,
                targetDirectory: $absolute_outdir,
                config: $this->remarksConfig);
        } catch (RuntimeException $exception) {
            //            if (true || $this->user->config()->telemetryEnabled) {
            throw $exception;
            //            }
        }

        // 5. Zip the out dir
        $from = $jobdir->joinAtoms('out')->toRelative();
        $to1 = $jobdir->joinAtoms('out.zip')->toRelative();
        FileManipulations::zipDirectory($user_storage,
            from: $from,
            to: $to1);

        // 6. Upload zip to S3
        $s3_download_path = 'userZips/' . $job_id . '.zip';
        if (!Storage::disk('s3')->put($s3_download_path, $user_storage->get($to1))) {
            throw new RuntimeException('Unable to upload zip to s3');
        }

        // 7. Unless user has telemetry=on && remarksService exception happened, delete temporary folder
        // 8. Insert a row in "sync" table
        $rm_filename = $remarkableService->filename($user_storage, $to);

        $rm_filepath = "$this->RMFilePath/$rm_filename";
        $user = $this->user;
        $lock = Cache::lock('append-to-sync-table-for-userid-' . $user->id, 10);
        $lock->get(function () use ($user, $rm_filepath, $s3_download_path) {
            $sync = new Sync();
            $sync->filename = $rm_filepath;
            $sync->S3_download_path = $s3_download_path;
            $sync->user()->associate($user);
            $sync->save();
        });
    }
}
