<?php

namespace App\Models;

use App\Enums\Division;
use App\Enums\ProjectStage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    protected $fillable = [
        'instansi_id',
        'stage',
        'nama_project',
        'nama_pelanggan',
        'nomor_pic',
        'jabatan_pic',
        'division',
        'estimasi_go_live',
        'revenue',
        'description',
        'durasi_bulan',
        'tanggal_win',
        'tanggal_go_live',
        'kontrak_sampai',
        'skema_penagihan',
        'file_pks',
        'file_po',
        'file_npwp',
        'lost_reason',
        'created_by',
        'last_stale_alert_at',
    ];

    protected $casts = [
        'stage'               => ProjectStage::class,
        'division'            => Division::class,
        'estimasi_go_live'    => 'date',
        'tanggal_win'         => 'date',
        'tanggal_go_live'     => 'date',
        'kontrak_sampai'      => 'date',
        'last_stale_alert_at' => 'datetime',
        'revenue'             => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $project) {
            foreach (['file_pks', 'file_po', 'file_npwp'] as $field) {
                if ($project->$field) {
                    Storage::disk('local')->delete($project->$field);
                }
            }
        });
    }

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeStage(Builder $q, ProjectStage $stage): Builder
    {
        return $q->where('stage', $stage->value);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->whereNotIn('stage', [ProjectStage::Lost->value]);
    }
}
