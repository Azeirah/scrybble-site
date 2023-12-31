<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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

    public function isOld(): bool {
        $minutes = 5;
        return Carbon::now()->addMinutes($minutes)->lessThan($this->created_at);
    }

    public function hasError() {
        return $this->logs()->where('severity', 'error')->count() > 0;
    }

    public function complete(string $s3_download_path) {
        $this->completed = true;
        $this->S3_download_path = $s3_download_path;
        $this->save();
    }
}
