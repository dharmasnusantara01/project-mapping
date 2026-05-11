<?php

namespace App\Services;

use App\Enums\ProjectStage;
use App\Models\AccountManager;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AmReminderBuilder
{
    /** Project dianggap stale kalau tidak ada update selama N hari ini. */
    public const STALE_DAYS = 7;

    /**
     * Project yang sudah ter-alert tidak akan di-alert lagi
     * sampai stale lagi setelah N hari berikutnya — re-alert mingguan.
     */
    public const REALERT_DAYS = 7;

    /**
     * Ambil semua project AM yang eligible untuk alert stale saat ini.
     */
    public function staleProjects(AccountManager $am): Collection
    {
        $now           = Carbon::now();
        $staleCutoff   = $now->copy()->subDays(self::STALE_DAYS);
        $realertCutoff = $now->copy()->subDays(self::REALERT_DAYS);

        return Project::query()
            ->whereHas('instansi', fn ($q) => $q->where('account_manager_id', $am->id))
            ->whereIn('stage', [ProjectStage::Qualified->value, ProjectStage::Submit->value])
            ->where('updated_at', '<', $staleCutoff)
            ->where(function ($q) use ($realertCutoff) {
                $q->whereNull('last_stale_alert_at')
                  ->orWhere('last_stale_alert_at', '<', $realertCutoff);
            })
            ->with('instansi:id,nama_instansi')
            ->orderBy('updated_at')
            ->get();
    }

    /**
     * Kembalikan pesan HTML siap kirim, atau null kalau AM tidak punya
     * project yang eligible untuk alert.
     */
    public function build(AccountManager $am): ?string
    {
        $stale = $this->staleProjects($am);
        if ($stale->isEmpty()) {
            return null;
        }

        $now = Carbon::now();
        $appUrl = rtrim(config('app.url', ''), '/');
        $pipelineLink = $appUrl ? "{$appUrl}/admin/projects?account_manager_id={$am->id}" : null;

        $lines = [];
        $lines[] = "🚨 <b>Heads up, " . e($am->name) . "</b>";
        $lines[] = '';
        $lines[] = "Ada <b>" . $stale->count() . " project</b> tanpa progress >" . self::STALE_DAYS . " hari. Mohon di-update statusnya:";
        $lines[] = '';

        foreach ($stale as $i => $p) {
            $age = (int) $p->updated_at->diffInDays($now);
            $rev = number_format((float) $p->revenue, 0, ',', '.');
            $lines[] = ($i + 1) . ". <b>" . e($p->nama_project) . "</b>";
            $lines[] = "   • Stage: " . $p->stage->label() . " ({$age} hari diam)";
            $lines[] = "   • Instansi: " . e($p->instansi?->nama_instansi ?? '—');
            $lines[] = "   • Revenue: Rp {$rev}";
        }

        if ($pipelineLink) {
            $lines[] = '';
            $lines[] = "🔗 <a href=\"{$pipelineLink}\">Buka pipeline</a>";
        }

        return implode("\n", $lines);
    }
}
