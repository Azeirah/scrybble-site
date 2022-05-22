<?php

namespace App\Jobs;

use App\DataClasses\RemarksConfig;
use App\Helpers\FileManipulations;
use App\Helpers\UserStorage;
use App\Models\User;
use App\Services\RemarksService;
use Eloquent\Pathogen\AbsolutePath;
use Eloquent\Pathogen\Exception\InvalidPathStateException;
use Eloquent\Pathogen\RelativePath;
use Eloquent\Pathogen\RelativePathInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
     * @return void
     * @throws InvalidPathStateException
     * @throws \Eloquent\Pathogen\Exception\NonAbsolutePathException
     */
    public function handle(RemarksService $remarksService): void {
        /**
         * Create a temporary folder and move the zip to this location. Might be useful to name this folder as the
         * job's id
         * This is to isolate the processing of each zip in one unique location. Mostly just for debugging purposes.
         * Extract the zip
         * Delete the zip
         * Run remarks over the extracted files
         * Insert a row in "sync" table
         */
        $userStorage = UserStorage::get($this->user);
        $jobId = $this->job->getJobId();
        $jobdir = new RelativePath(['jobs', $jobId]);
        $to = $jobdir->joinAtoms('extractedFiles')->toRelative();
        FileManipulations::ensureDirectoryTreeExists($userStorage, $to);
        if (!$userStorage->exists($jobId)) {
            $userStorage->makeDirectory($jobId);
        }

        // 2. Extract the zip
        FileManipulations::extractZip($userStorage, from: $this->zipLocation, to: $to);

        $absJobdir = AbsolutePath::fromString($userStorage->path($jobdir->string()));
        $absOutdir = AbsolutePath::fromString($userStorage->path($jobdir->joinAtoms('out')));
        $remarksService->extractNotesAndHighlights($absJobdir, $absOutdir, $this->remarksConfig);
    }
}
