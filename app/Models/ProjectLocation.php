<?php

namespace App\Models;

use Database\Factories\ProjectLocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectLocation extends Model
{
    /** @use HasFactory<ProjectLocationFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'city',
        'province',
        'latitude',
        'longitude',
        'is_manual_override',
        'geocoded_at',
        'is_primary',
    ];

    protected $casts = [
        'latitude'           => 'decimal:7',
        'longitude'          => 'decimal:7',
        'is_manual_override' => 'bool',
        'is_primary'         => 'bool',
        'geocoded_at'        => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
