<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperSync
 */
class Sync extends Model {
    protected $table = 'sync';

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
