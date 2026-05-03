<?php

namespace App\Models;

use Database\Factories\SectorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    /** @use HasFactory<SectorFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color', 'order'];

    protected $casts = [
        'order' => 'int',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
