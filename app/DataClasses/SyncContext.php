<?php

namespace App\DataClasses;

use App\Models\Sync;
use App\Models\SyncLog;
use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SyncContext implements Arrayable {
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

    public function logStep(string $string, array $context = []): void {
        $log = new SyncLog;
        $log->message = $string;
        $log->severity = "info";
        if (count($context)) {
            $log->context = $context;
        }
        $log->belongsToSync()->associate($this->sync);
        $log->save();
    }

    public function logError(string $message, array $context = []): void {
        $log = new SyncLog;
        $log->message = $message;
        $log->severity = "error";
        if (count($context)) {
            $log->context = $context;
        }
        $log->belongsToSync()->associate($this->sync);
        $log->save();
    }

    public function toArray() {
        return [
            "user" => $this->user,
            "sync_id" => $this->sync_id,
            "input_filename" => $this->input_filename
        ];
    }
}
