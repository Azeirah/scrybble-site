<?php

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
use RuntimeException;

class ProcessDownloadedZip implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private RelativePathInterface $zipLocation;
    private RemarksConfig $remarksConfig;
    private User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $zipLocation, RemarksConfig $remarksConfig, User $user) {
        $this->zipLocation = new RelativePath([$zipLocation]);
        $this->remarksConfig = $remarksConfig;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @param RemarksService $remarksService
     * @return void
     * @throws EmptyPathException
     * @throws InvalidPathStateException
     * @throws NonAbsolutePathException
     */
    public function handle(RemarksService $remarksService, RemarkableService $remarkableService): void {
        $userStorage = UserStorage::get($this->user);
        $jobId = $this->job->getJobId();

        $jobdir = new RelativePath(['jobs', $jobId]);
        $to = $jobdir->joinAtoms('extractedFiles')->toRelative();

        // 1. Create a temporary folder and move the zip to this location.
        FileManipulations::ensureDirectoryTreeExists($userStorage, $to);

        // 2. Extract the zip
        FileManipulations::extractZip($userStorage, from: $this->zipLocation, to: $to);

        // 3. Delete the zip
        // TODO


        // 4. Run remarks over the extracted files
        $absJobdir = AbsolutePath::fromString($userStorage->path($jobdir->joinAtoms("extractedFiles")->string()));
        $absOutdir = AbsolutePath::fromString($userStorage->path($jobdir->joinAtoms('out')));
        try {
            $remarksService->extractNotesAndHighlights(
                sourceDirectory: $absJobdir,
                targetDirectory: $absOutdir,
                config: $this->remarksConfig);
        } catch (RuntimeException $exception) {
            //            if (true || $this->user->config()->telemetryEnabled) {
            throw $exception;
            //            }
        }

        // 5. Zip the out dir
        $from = $jobdir->joinAtoms('out')->toRelative();
        $to1 = $jobdir->joinAtoms('out.zip')->toRelative();
        FileManipulations::zipDirectory($userStorage,
            from: $from,
            to: $to1);

        // 6. Upload zip to S3
        if (!Storage::disk('s3')->put('userZips/' . $jobId . '.zip', $userStorage->get($to1))) {
            throw new RuntimeException("Unable to upload zip to s3");
        }

        // 7. Unless user has telemetry=on && remarksService exception happened, delete temporary folder
        // 8. Insert a row in "sync" table
        $rmFileName = $remarkableService->filename($userStorage, $to);

        $user = $this->user;
        $lock = Cache::lock("append-to-sync-table-for-userid-" . $user->id, 10);
        $lock->get(function () use ($user, $rmFileName) {
            $syncRow = new Sync();
            $syncRow->filename = $rmFileName;
            $syncRow->user()->associate($user);
            $syncRow->save();
        });
    }
}
