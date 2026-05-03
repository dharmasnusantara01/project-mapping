<?php

namespace App\Http\Controllers;

use App\Models\Project;
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

    public function projects(): JsonResponse
    {
        $payload = Cache::remember('public.projects', now()->addMinutes(5), function () {
            return Project::query()
                ->published()
                ->with(['sector:id,name,slug,color', 'primaryLocation'])
                ->get()
                ->filter(fn ($p) => $p->primaryLocation)
                ->map(fn ($p) => [
                    'id'            => $p->id,
                    'name'          => $p->name,
                    'customer_name' => $p->customer_name,
                    'sector'        => [
                        'name'  => $p->sector->name,
                        'slug'  => $p->sector->slug,
                        'color' => $p->sector->color,
                    ],
                    'city'      => $p->primaryLocation->city,
                    'province'  => $p->primaryLocation->province,
                    'latitude'  => (float) $p->primaryLocation->latitude,
                    'longitude' => (float) $p->primaryLocation->longitude,
                    'year'      => $p->year,
                    'status'    => $p->public_status->value,
                    'summary'   => $p->public_summary,
                ])
                ->values()
                ->toArray();
        });

        return response()->json($payload);
    }
}
