<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperSync
 */
class Sync extends Model {
    protected $table = 'sync';

    protected $casts = [
        'completed' => 'bool'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * @param Builder $query
     * @param User $user
     * @return Builder
     */
    public function scopeForUser(Builder $query, User $user): Builder {
        return $query->where('user_id', $user->id);
    }

    public function scopeWhereIsCompleted(Builder $query) {
        return $query->where('completed', true);
    }

    public function logs(): HasMany {
        return $this->hasMany(SyncLog::class);
    }
}
