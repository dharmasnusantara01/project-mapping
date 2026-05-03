<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PublicStatus;
use App\Http\Controllers\Controller;
use App\Models\CityReference;
use App\Models\Project;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['sector', 'primaryLocation', 'publisher'])
            ->latest('id')
            ->paginate(15);

        return view('admin.projects.index', ['projects' => $projects]);
    }

    public function create()
    {
        return view('admin.projects.form', $this->formData(new Project()));
    }

    public function store(Request $request)
    {
        $data = $this->validateInput($request);
        $project = Project::create($data['project']);
        $project->locations()->create($data['location']);
        Cache::forget('public.projects');

        return redirect()->route('admin.projects.edit', $project)->with('status', 'Project tersimpan.');
    }

    public function edit(Project $project)
    {
        $project->load('primaryLocation');

        return view('admin.projects.form', $this->formData($project));
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validateInput($request);
        $project->update($data['project']);

        $location = $project->primaryLocation ?? $project->locations()->make(['is_primary' => true]);
        $location->fill($data['location']);
        $location->save();

        Cache::forget('public.projects');

        return redirect()->route('admin.projects.edit', $project)->with('status', 'Perubahan tersimpan.');
    }

    public function publish(Request $request, Project $project)
    {
        if (! $request->user()->canPublishProjects()) {
            abort(403, 'Hanya Manajer Sales / Superadmin yang berhak mempublikasikan.');
        }

        $publish = $request->boolean('publish');

        $project->update([
            'is_public'    => $publish,
            'published_by' => $publish ? $request->user()->id : null,
            'published_at' => $publish ? now() : null,
        ]);

        Cache::forget('public.projects');

        return back()->with('status', $publish ? 'Project dipublikasikan.' : 'Project di-unpublish.');
    }

    private function formData(Project $project): array
    {
        return [
            'project'  => $project,
            'sectors'  => Sector::orderBy('order')->get(['id', 'name']),
            'statuses' => PublicStatus::options(),
            'cities'   => CityReference::orderBy('province')->orderBy('name')->get(['id', 'name', 'province', 'latitude', 'longitude']),
        ];
    }

    private function validateInput(Request $request): array
    {
        $validated = $request->validate([
            'name'                        => ['required', 'string', 'max:200'],
            'customer_name'               => ['required', 'string', 'max:200'],
            'sector_id'                   => ['required', 'exists:sectors,id'],
            'year'                        => ['required', 'integer', 'min:2000', 'max:'.(date('Y') + 1)],
            'public_status'               => ['required', 'in:berjalan,selesai'],
            'public_summary'              => ['nullable', 'string', 'max:500'],
            'location.city'               => ['required', 'string', 'max:120'],
            'location.province'           => ['required', 'string', 'max:120'],
            'location.latitude'           => ['required', 'numeric', 'between:-11,6'],
            'location.longitude'          => ['required', 'numeric', 'between:95,141'],
            'location.is_manual_override' => ['nullable', 'boolean'],
        ]);

        return [
            'project' => [
                'name'           => $validated['name'],
                'customer_name'  => $validated['customer_name'],
                'sector_id'      => $validated['sector_id'],
                'year'           => $validated['year'],
                'public_status'  => $validated['public_status'],
                'public_summary' => $validated['public_summary'] ?? null,
            ],
            'location' => [
                'city'               => $validated['location']['city'],
                'province'           => $validated['location']['province'],
                'latitude'           => $validated['location']['latitude'],
                'longitude'          => $validated['location']['longitude'],
                'is_manual_override' => (bool) ($validated['location']['is_manual_override'] ?? false),
                'geocoded_at'        => now(),
                'is_primary'         => true,
            ],
        ];
    }
}
