<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncLog extends Model {

    public function belongsToSync(): BelongsTo {
        return $this->belongsTo(Sync::class, "sync_id", "id");
    }
}
