<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Instansi extends Model
{
    protected $table = 'instansi';

    protected $fillable = [
        'nama_instansi',
        'alamat_instansi',
        'telpon_instansi',
        'latitude',
        'longitude',
        'witel_id',
        'account_manager_id',
        'sector_id',
        'public_summary',
        'is_public',
        'published_by',
        'published_at',
    ];

    protected $casts = [
        'latitude'     => 'decimal:7',
        'longitude'    => 'decimal:7',
        'is_public'    => 'bool',
        'published_at' => 'datetime',
    ];

    public function witel(): BelongsTo
    {
        return $this->belongsTo(Witel::class);
    }

    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(AccountManager::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
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
