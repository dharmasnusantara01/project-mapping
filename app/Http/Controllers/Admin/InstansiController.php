<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountManager;
use App\Models\Instansi;
use App\Models\Sector;
use App\Models\Witel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class InstansiController extends Controller
{
    public function index()
    {
        $instansi = Instansi::with(['sector', 'witel', 'accountManager', 'publisher'])
            ->latest('id')
            ->paginate(15);

        return view('admin.instansi.index', ['instansi' => $instansi]);
    }

    public function create()
    {
        return view('admin.instansi.form', $this->formData(new Instansi()));
    }

    public function store(Request $request)
    {
        $data = $this->validateInput($request);
        Instansi::create($data);
        Cache::forget('public.instansi');

        return redirect()->route('admin.instansi.index')->with('status', 'Instansi tersimpan.');
    }

    public function edit(Instansi $instansi)
    {
        return view('admin.instansi.form', $this->formData($instansi));
    }

    public function update(Request $request, Instansi $instansi)
    {
        $data = $this->validateInput($request);
        $instansi->update($data);
        Cache::forget('public.instansi');

        return redirect()->route('admin.instansi.edit', $instansi)->with('status', 'Perubahan tersimpan.');
    }

    public function publish(Request $request, Instansi $instansi)
    {
        if (! $request->user()->canPublishInstansi()) {
            abort(403, 'Hanya Manajer Sales / Superadmin yang berhak mempublikasikan.');
        }

        $publish = $request->boolean('publish');

        $instansi->update([
            'is_public'    => $publish,
            'published_by' => $publish ? $request->user()->id : null,
            'published_at' => $publish ? now() : null,
        ]);

        Cache::forget('public.instansi');

        return back()->with('status', $publish ? 'Instansi dipublikasikan.' : 'Instansi di-unpublish.');
    }

    private function formData(Instansi $instansi): array
    {
        return [
            'instansi'        => $instansi,
            'sectors'         => Sector::orderBy('order')->get(['id', 'name']),
            'witels'          => Witel::orderBy('name')->get(['id', 'name']),
            'accountManagers' => AccountManager::orderBy('name')->get(['id', 'name']),
        ];
    }

    private function validateInput(Request $request): array
    {
        return $request->validate([
            'nama_instansi'      => ['required', 'string', 'max:200'],
            'alamat_instansi'    => ['nullable', 'string', 'max:1000'],
            'telpon_instansi'    => ['nullable', 'string', 'max:30'],
            'witel_id'           => ['required', 'exists:witel,id'],
            'account_manager_id' => ['required', 'exists:account_managers,id'],
            'sector_id'          => ['required', 'exists:sectors,id'],
            'latitude'           => ['required', 'numeric', 'between:-11,6'],
            'longitude'          => ['required', 'numeric', 'between:95,141'],
            'public_summary'     => ['nullable', 'string', 'max:500'],
        ]);
    }
}
