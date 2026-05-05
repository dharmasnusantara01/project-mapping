<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Witel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class WitelController extends Controller
{
    public function index()
    {
        $witels = Witel::withCount('instansi')->orderBy('name')->paginate(20);

        return view('admin.witel.index', ['witels' => $witels]);
    }

    public function create()
    {
        return view('admin.witel.form', ['witel' => new Witel()]);
    }

    public function store(Request $request)
    {
        Witel::create($this->validated($request));
        Cache::forget('public.instansi');

        return redirect()->route('admin.witel.index')->with('status', 'Witel tersimpan.');
    }

    public function edit(Witel $witel)
    {
        return view('admin.witel.form', ['witel' => $witel]);
    }

    public function update(Request $request, Witel $witel)
    {
        $witel->update($this->validated($request, $witel->id));
        Cache::forget('public.instansi');

        return redirect()->route('admin.witel.index')->with('status', 'Witel diperbarui.');
    }

    public function destroy(Witel $witel)
    {
        if ($witel->instansi()->exists()) {
            return back()->withErrors(['witel' => 'Witel tidak bisa dihapus karena masih dipakai instansi.']);
        }

        $witel->delete();

        return redirect()->route('admin.witel.index')->with('status', 'Witel dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:200', Rule::unique('witel', 'name')->ignore($ignoreId)],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('witel', 'code')->ignore($ignoreId)],
        ]);
    }
}
