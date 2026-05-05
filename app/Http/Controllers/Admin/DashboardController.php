<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountManager;
use App\Models\Instansi;
use App\Models\Sector;
use App\Models\Witel;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalInstansi = Instansi::count();
        $totalPublished = Instansi::where('is_public', true)->count();
        $totalDraft = $totalInstansi - $totalPublished;

        $sectors = Sector::orderBy('order')->get(['id', 'name', 'slug', 'color']);

        $byWitel = Instansi::query()
            ->select('witel_id', DB::raw('count(*) as total'))
            ->groupBy('witel_id')
            ->with('witel:id,name')
            ->get()
            ->map(fn ($r) => [
                'name'  => $r->witel?->name ?? '—',
                'total' => (int) $r->total,
            ])
            ->sortByDesc('total')
            ->values();

        $byAccountManager = Instansi::query()
            ->select('account_manager_id', DB::raw('count(*) as total'))
            ->groupBy('account_manager_id')
            ->with('accountManager:id,name')
            ->get()
            ->map(fn ($r) => [
                'name'  => $r->accountManager?->name ?? '—',
                'total' => (int) $r->total,
            ])
            ->sortByDesc('total')
            ->values();

        $bySector = Instansi::query()
            ->select('sector_id', DB::raw('count(*) as total'))
            ->groupBy('sector_id')
            ->with('sector:id,name,slug,color')
            ->get()
            ->map(fn ($r) => [
                'name'  => $r->sector?->name ?? '—',
                'slug'  => $r->sector?->slug ?? '',
                'color' => $r->sector?->color ?? '#64748b',
                'total' => (int) $r->total,
            ])
            ->sortByDesc('total')
            ->values();

        $recent = Instansi::with(['witel:id,name', 'accountManager:id,name', 'sector:id,name,slug,color'])
            ->latest('id')
            ->limit(8)
            ->get();

        $mapPoints = Instansi::query()
            ->where('is_public', true)
            ->with('sector:id,slug,color')
            ->get(['id', 'nama_instansi', 'latitude', 'longitude', 'sector_id'])
            ->map(fn ($i) => [
                'name'  => $i->nama_instansi,
                'lat'   => (float) $i->latitude,
                'lng'   => (float) $i->longitude,
                'color' => $i->sector?->color ?? '#64748b',
            ])
            ->values();

        return view('admin.dashboard', [
            'kpi' => [
                'instansi'  => $totalInstansi,
                'published' => $totalPublished,
                'draft'     => $totalDraft,
                'witel'     => Witel::count(),
                'am'        => AccountManager::count(),
                'sector'    => $sectors->count(),
            ],
            'sectors'          => $sectors,
            'byWitel'          => $byWitel,
            'byAccountManager' => $byAccountManager,
            'bySector'         => $bySector,
            'recent'           => $recent,
            'mapPoints'        => $mapPoints,
        ]);
    }
}
