<?php

namespace App\DataClasses;

use App\Models\Sync;
use App\Models\SyncLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SyncContext {
    public string $sync_id;
    public Sync $sync;
    public mixed $folder;
    public string $downloaded_zip_location;

    public function __construct(public string $input_filename, public User $user, public RemarksConfig $remarks_config) {
        $this->sync_id = Str::random();

        $sync = new Sync();
        $sync->filename = $this->input_filename;
        $sync->user()->associate(Auth::user());
        $sync->save();
        $this->sync = $sync;
    }

    public function logStep(string $string): void {
        $log = new SyncLog;
        $log->message = $string;
        $log->severity = "info";
        $log->belongsToSync()->associate($this->sync);
        $log->save();
    }

    public function logError(string $message): void {
        $log = new SyncLog;
        $log->message = $message;
        $log->severity = "error";
        $log->belongsToSync()->associate($this->sync);
        $log->save();
    }

}
