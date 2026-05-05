<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use App\Models\Sector;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PublicMapController extends Controller
{
    public function index()
    {
        $sectors = Sector::orderBy('order')->get(['id', 'name', 'slug', 'color']);

        return view('public.map', [
            'sectors' => $sectors,
        ]);
    }

    public function instansi(): JsonResponse
    {
        $payload = Cache::remember('public.instansi', now()->addMinutes(5), function () {
            return Instansi::query()
                ->published()
                ->with([
                    'sector:id,name,slug,color',
                    'witel:id,name',
                    'accountManager:id,name',
                ])
                ->get()
                ->map(fn ($i) => [
                    'id'              => $i->id,
                    'nama_instansi'   => $i->nama_instansi,
                    'alamat_instansi' => $i->alamat_instansi,
                    'telpon_instansi' => $i->telpon_instansi,
                    'latitude'        => (float) $i->latitude,
                    'longitude'       => (float) $i->longitude,
                    'witel'           => [
                        'id'   => $i->witel->id,
                        'name' => $i->witel->name,
                    ],
                    'account_manager' => [
                        'id'   => $i->accountManager->id,
                        'name' => $i->accountManager->name,
                    ],
                    'sector' => [
                        'name'  => $i->sector->name,
                        'slug'  => $i->sector->slug,
                        'color' => $i->sector->color,
                    ],
                    'summary' => $i->public_summary,
                ])
                ->values()
                ->toArray();
        });

        return response()->json($payload);
    }
}
