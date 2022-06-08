<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperSync
 */
class Sync extends Model {
    protected $table = 'sync';

    /**
     * Filename without escapes
     * @return Attribute
     */
    protected function cleanFilename(): Attribute {
        return Attribute::make(
            get: fn () => Str::replace('\\', '/', $this->filename)
        );
    }

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
}
