<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountManager;
use App\Models\Instansi;
use App\Models\Sector;
use App\Models\Witel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

    public function importForm()
    {
        return view('admin.instansi.import', [
            'sectors' => Sector::orderBy('order')->get(['id', 'name']),
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'sector_id' => ['required', 'exists:sectors,id'],
            'csv_file'  => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');
        if (! $handle) {
            return back()->withErrors(['csv_file' => 'Gagal membaca file.'])->withInput();
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'CSV kosong.'])->withInput();
        }

        $created = 0;
        $newWitels = [];
        $newAms = [];
        $errors = [];
        $rowNum = 1;

        DB::transaction(function () use ($handle, $request, &$created, &$newWitels, &$newAms, &$errors, &$rowNum) {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                    continue; // skip empty
                }

                [$witelName, $amName, $namaInstansi, $alamat, $coord, $telp] = array_pad($row, 6, null);
                $witelName    = trim((string) $witelName);
                $amName       = trim((string) $amName);
                $namaInstansi = trim((string) $namaInstansi);
                $alamat       = trim((string) ($alamat ?? ''));
                $coord        = trim((string) ($coord ?? ''));
                $telp         = trim((string) ($telp ?? ''));

                if ($witelName === '' || $amName === '' || $namaInstansi === '' || $coord === '') {
                    $errors[] = "Baris {$rowNum}: Witel/AM/Nama Instansi/Koordinat wajib diisi.";
                    continue;
                }

                $parts = preg_split('/\s*,\s*/', $coord);
                if (! $parts || count($parts) !== 2 || ! is_numeric($parts[0]) || ! is_numeric($parts[1])) {
                    $errors[] = "Baris {$rowNum}: format koordinat '{$coord}' tidak valid (harap 'lat, long').";
                    continue;
                }
                $lat = (float) $parts[0];
                $lng = (float) $parts[1];
                if ($lat < -11 || $lat > 6 || $lng < 95 || $lng > 141) {
                    $errors[] = "Baris {$rowNum}: koordinat di luar rentang Indonesia (lat -11..6, long 95..141).";
                    continue;
                }

                $witel = Witel::firstOrCreate(['name' => $witelName]);
                if ($witel->wasRecentlyCreated) {
                    $newWitels[] = $witelName;
                }
                $am = AccountManager::firstOrCreate(['name' => $amName]);
                if ($am->wasRecentlyCreated) {
                    $newAms[] = $amName;
                }

                Instansi::create([
                    'nama_instansi'      => $namaInstansi,
                    'alamat_instansi'    => $alamat ?: null,
                    'telpon_instansi'    => $telp ?: null,
                    'latitude'           => $lat,
                    'longitude'          => $lng,
                    'witel_id'           => $witel->id,
                    'account_manager_id' => $am->id,
                    'sector_id'          => $request->integer('sector_id'),
                ]);
                $created++;
            }
        });

        fclose($handle);
        Cache::forget('public.instansi');

        $msg = "Import selesai: {$created} instansi tersimpan.";
        if ($newWitels) {
            $msg .= ' Witel baru: '.implode(', ', array_unique($newWitels)).'.';
        }
        if ($newAms) {
            $msg .= ' AM baru: '.implode(', ', array_unique($newAms)).'.';
        }

        return redirect()->route('admin.instansi.index')
            ->with('status', $msg)
            ->with('importErrors', $errors);
    }
}
