<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Division;
use App\Enums\ProjectStage;
use App\Http\Controllers\Controller;
use App\Models\AccountManager;
use App\Models\Instansi;
use App\Models\Project;
use App\Models\Witel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['instansi:id,nama_instansi,witel_id,account_manager_id', 'instansi.witel:id,name', 'instansi.accountManager:id,name', 'creator:id,name'])
            ->latest('updated_at');

        if ($request->filled('witel_id')) {
            $query->whereHas('instansi', fn ($q) => $q->where('witel_id', $request->integer('witel_id')));
        }
        if ($request->filled('account_manager_id')) {
            $query->whereHas('instansi', fn ($q) => $q->where('account_manager_id', $request->integer('account_manager_id')));
        }
        if ($request->filled('division')) {
            $query->where('division', $request->string('division'));
        }
        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $query->where(function ($q) use ($term) {
                $q->where('nama_project', 'like', $term)
                  ->orWhere('nama_pelanggan', 'like', $term);
            });
        }

        $byStage = $query->get()->groupBy(fn ($p) => $p->stage->value);

        return view('admin.projects.index', [
            'byStage'         => $byStage,
            'stages'          => ProjectStage::cases(),
            'witels'          => Witel::orderBy('name')->get(['id', 'name']),
            'accountManagers' => AccountManager::orderBy('name')->get(['id', 'name']),
            'divisions'       => Division::cases(),
            'filters'         => $request->only(['witel_id', 'account_manager_id', 'division', 'q']),
        ]);
    }

    public function create(Request $request, ?Instansi $instansi = null)
    {
        return view('admin.projects.form', [
            'project'         => new Project([
                'instansi_id' => $instansi?->id,
                'stage'       => ProjectStage::Qualified,
            ]),
            'instansi'        => $instansi,
            'allInstansi'     => $instansi ? collect() : Instansi::orderBy('nama_instansi')->get(['id', 'nama_instansi']),
        ]);
    }

    public function store(Request $request, ?Instansi $instansi = null)
    {
        $rules = $this->qualifiedRules();
        if (! $instansi) {
            $rules['instansi_id'] = ['required', 'exists:instansi,id'];
        }

        $data = $request->validate($rules);
        $data['estimasi_go_live'] = $this->normalizeMonth($data['estimasi_go_live']);
        $data['instansi_id'] = $instansi?->id ?? $data['instansi_id'];
        $data['stage'] = ProjectStage::Qualified->value;
        $data['created_by'] = $request->user()->id;

        $project = Project::create($data);

        return redirect()->route('admin.projects.edit', $project)->with('status', 'Project tersimpan (stage Qualified).');
    }

    public function edit(Project $project)
    {
        $project->load('instansi');

        return view('admin.projects.form', [
            'project'  => $project,
            'instansi' => $project->instansi,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $stage = $project->stage;

        $rules = $this->qualifiedRules();
        if (in_array($stage, [ProjectStage::Submit, ProjectStage::Win], true)) {
            $rules = array_merge($rules, $this->submitRules());
        }
        if ($stage === ProjectStage::Win) {
            $rules = array_merge($rules, $this->winRules(false));
        }
        if ($stage === ProjectStage::Lost) {
            $rules['lost_reason'] = ['nullable', 'string', 'max:500'];
        }

        $data = $request->validate($rules);

        if (isset($data['estimasi_go_live'])) {
            $data['estimasi_go_live'] = $this->normalizeMonth($data['estimasi_go_live']);
        }

        // file replacements (only relevant when already in Win)
        if ($stage === ProjectStage::Win) {
            foreach (['file_pks', 'file_po', 'file_npwp'] as $field) {
                if ($request->hasFile($field)) {
                    if ($project->$field) {
                        Storage::disk('local')->delete($project->$field);
                    }
                    $data[$field] = $this->storeFile($request->file($field), $project->id, $field);
                } else {
                    unset($data[$field]);
                }
            }
        }

        $project->update($data);

        return redirect()->route('admin.projects.edit', $project)->with('status', 'Perubahan tersimpan.');
    }

    public function advance(Request $request, Project $project)
    {
        $next = $project->stage->next();
        if (! $next) {
            return back()->withErrors(['stage' => 'Project sudah berada di stage final.']);
        }

        if ($next === ProjectStage::Win && ! $request->user()->canAdvanceToWin()) {
            abort(403, 'Hanya Manajer Sales / Superadmin yang berhak mengubah stage ke Win.');
        }

        if ($next === ProjectStage::Submit) {
            $data = $request->validate($this->submitRules());
        } else {
            // Win: validate win fields including required files
            $data = $request->validate($this->winRules(true));
            foreach (['file_pks', 'file_po', 'file_npwp'] as $field) {
                if ($request->hasFile($field)) {
                    $data[$field] = $this->storeFile($request->file($field), $project->id, $field);
                } else {
                    unset($data[$field]);
                }
            }
        }

        $data['stage'] = $next->value;
        $project->update($data);

        return redirect()->route('admin.projects.edit', $project)->with('status', "Project naik ke stage {$next->label()}.");
    }

    public function lost(Request $request, Project $project)
    {
        $data = $request->validate([
            'lost_reason' => ['nullable', 'string', 'max:500'],
        ]);
        $data['stage'] = ProjectStage::Lost->value;
        $project->update($data);

        return redirect()->route('admin.projects.index')->with('status', 'Project di-mark sebagai Lost.');
    }

    public function destroy(Request $request, Project $project)
    {
        if (! $request->user()->canDeleteProject()) {
            abort(403);
        }
        $instansiId = $project->instansi_id;
        $project->delete();

        return redirect()->route('admin.instansi.edit', $instansiId)->with('status', 'Project dihapus.');
    }

    public function file(Request $request, Project $project, string $type)
    {
        $allowed = ['pks', 'po', 'npwp'];
        if (! in_array($type, $allowed, true)) {
            abort(404);
        }
        $field = "file_{$type}";
        $path = $project->$field;
        if (! $path || ! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path, basename($path));
    }

    // ── Validation rule sets ────────────────────────────────────────────

    private function qualifiedRules(): array
    {
        return [
            'nama_project'     => ['required', 'string', 'max:200'],
            'nama_pelanggan'   => ['required', 'string', 'max:200'],
            'nomor_pic'        => ['required', 'string', 'max:30'],
            'jabatan_pic'      => ['required', 'string', 'max:100'],
            'division'         => ['required', 'in:government,enterprise,sme'],
            'estimasi_go_live' => ['required', 'date'],
            'revenue'          => ['required', 'numeric', 'min:0'],
        ];
    }

    private function submitRules(): array
    {
        return [
            'description'  => ['required', 'string'],
            'durasi_bulan' => ['required', 'integer', 'min:1', 'max:240'],
        ];
    }

    private function winRules(bool $filesRequired): array
    {
        $fileRule = $filesRequired ? 'required' : 'nullable';

        return [
            'tanggal_win'      => ['required', 'date'],
            'tanggal_go_live'  => ['required', 'date'],
            'kontrak_sampai'   => ['required', 'date', 'after:tanggal_go_live'],
            'skema_penagihan'  => ['required', 'string', 'max:100'],
            'file_pks'         => [$fileRule, 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'file_po'          => [$fileRule, 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'file_npwp'        => [$fileRule, 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    private function validateQualified(Request $request): array
    {
        return $request->validate($this->qualifiedRules());
    }

    private function storeFile(\Illuminate\Http\UploadedFile $file, int $projectId, string $field): string
    {
        $ext = $file->getClientOriginalExtension();
        $name = str_replace('file_', '', $field).'-'.$projectId.'-'.time().'.'.$ext;

        return $file->storeAs("projects/{$projectId}", $name, 'local');
    }

    private function normalizeMonth(string $value): string
    {
        // Form input type=month sends "YYYY-MM"; date column needs YYYY-MM-DD.
        if (preg_match('/^\d{4}-\d{2}$/', $value)) {
            return $value.'-01';
        }
        return $value;
    }
}
