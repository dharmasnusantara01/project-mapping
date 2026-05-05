@extends('layouts.admin')

@section('title', 'Import Instansi (CSV)')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-white">Import CSV</h2>
            <p class="mt-0.5 text-sm text-teal-100/70">Upload file CSV untuk menambah banyak instansi sekaligus.</p>
        </div>
        <a href="{{ route('admin.instansi.index') }}" class="btn-ghost rounded-lg px-3 py-1.5 text-xs font-medium">← Kembali</a>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <form method="POST" action="{{ route('admin.instansi.import.process') }}" enctype="multipart/form-data"
              class="glass space-y-4 rounded-2xl p-5 lg:col-span-2">
            @csrf

            <div>
                <label class="text-xs font-medium text-teal-200/80">Sektor (dipakai untuk semua baris)</label>
                <select name="sector_id" required class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                    <option value="">— Pilih sektor —</option>
                    @foreach ($sectors as $s)
                        <option value="{{ $s->id }}" @selected(old('sector_id') == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
                @error('sector_id') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-medium text-teal-200/80">File CSV</label>
                <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required
                       class="input mt-1 w-full rounded-lg px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-emerald-500/20 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-emerald-100 hover:file:bg-emerald-500/30">
                @error('csv_file') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                <p class="mt-1 text-[11px] text-teal-200/60">Maks 5 MB. Encoding UTF-8.</p>
            </div>

            <div class="rounded-lg border border-amber-400/20 bg-amber-500/10 p-3 text-[11px] text-amber-100/90">
                <strong class="font-semibold">Catatan:</strong>
                Witel & AM yang belum ada di database akan dibuat otomatis berdasarkan nama. Hindari typo agar tidak duplikat.
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn-primary rounded-lg px-4 py-2 text-sm font-semibold">Upload & Import</button>
                <a href="{{ route('admin.instansi.index') }}" class="btn-ghost rounded-lg px-4 py-2 text-sm font-medium">Batal</a>
            </div>
        </form>

        <div class="glass space-y-3 rounded-2xl p-5">
            <h3 class="text-sm font-semibold text-white">Format CSV</h3>
            <p class="text-[11px] text-teal-200/70">Baris pertama = header. 6 kolom dengan urutan:</p>

            <ol class="space-y-1 text-[11px] text-teal-100/85">
                <li>1. <span class="font-semibold">Witel</span></li>
                <li>2. <span class="font-semibold">AM</span></li>
                <li>3. <span class="font-semibold">Nama Instansi</span></li>
                <li>4. <span class="font-semibold">Alamat Lengkap</span></li>
                <li>5. <span class="font-semibold">Koordinat (Lat, Long)</span> — wajib di-quote, contoh <code class="rounded bg-black/30 px-1 py-0.5">"-6.224, 106.808"</code></li>
                <li>6. <span class="font-semibold">No. Telp</span></li>
            </ol>

            <div class="rounded-lg border border-teal-400/15 bg-black/30 p-2.5 font-mono text-[10.5px] leading-relaxed text-teal-100/90">
                <div>Witel,AM,Nama Instansi,Alamat Lengkap,"Koordinat (Lat, Long)",No. Telp</div>
                <div>WITEL JAKARTA,Budi Santoso,Kanim Jakpus,"Jl. Merdeka No.1, Jakarta Pusat","-6.1751, 106.8650",021-1234567</div>
                <div>WITEL BANDUNG,Sari Wijaya,Polrestabes Bandung,"Jl. Merdeka No.18, Bandung","-6.9147, 107.6098",022-4203121</div>
            </div>

            <p class="text-[11px] text-teal-200/60">
                Karena koordinat berisi koma, sel itu wajib dibungkus tanda kutip ganda <code class="rounded bg-black/30 px-1">"</code>.
            </p>

            <a href="{{ route('admin.instansi.index') }}/sample.csv" download
               id="download-sample"
               class="btn-ghost mt-2 inline-block rounded-lg px-3 py-1.5 text-xs font-medium">
                Download contoh CSV
            </a>
        </div>
    </div>

    <script>
        // Generate sample CSV in-browser when "Download contoh CSV" clicked
        document.getElementById('download-sample').addEventListener('click', e => {
            e.preventDefault();
            const csv = [
                'Witel,AM,Nama Instansi,Alamat Lengkap,"Koordinat (Lat, Long)",No. Telp',
                'WITEL JAKARTA,Budi Santoso,Kanim Jakpus,"Jl. Merdeka No.1, Jakarta Pusat","-6.1751, 106.8650",021-1234567',
                'WITEL BANDUNG,Sari Wijaya,Polrestabes Bandung,"Jl. Merdeka No.18, Bandung","-6.9147, 107.6098",022-4203121',
            ].join('\n');
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = 'contoh-instansi.csv';
            document.body.appendChild(a); a.click(); a.remove();
            URL.revokeObjectURL(url);
        });
    </script>
@endsection
