<?php

namespace App\Jobs;

use App\DataClasses\RemarksConfig;
use App\Helpers\UserStorage;
use App\Models\User;
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
    public function __construct(RelativePathInterface $zipLocation, RemarksConfig $remarksConfig, User $user) {
        $this->zipLocation = $zipLocation;
        $this->remarksConfig = $remarksConfig;
        $this->user = $user;
        $this->userStorage = UserStorage::get($user);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $jobId = $this->job->getJobId();
        /**
         * Create a temporary folder and move the zip to this location. Might be useful to name this folder as the
         * job's id
         * This is to isolate the processing of each zip in one unique location. Mostly just for debugging purposes.
         * Extract the zip
         * Delete the zip
         * Run remarks over the extracted files
         * Insert a row in "sync" table
         */
    }
}
