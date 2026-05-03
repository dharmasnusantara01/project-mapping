<?php

namespace App\Models;

use App\Enums\PublicStatus;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'customer_name',
        'sector_id',
        'year',
        'public_summary',
        'is_public',
        'public_status',
        'published_by',
        'published_at',
    ];

    protected $casts = [
        'is_public'     => 'bool',
        'year'          => 'int',
        'public_status' => PublicStatus::class,
        'published_at'  => 'datetime',
    ];

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(ProjectLocation::class);
    }

    public function primaryLocation(): HasOne
    {
        return $this->hasOne(ProjectLocation::class)->where('is_primary', true);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_public', true);
    }
}
