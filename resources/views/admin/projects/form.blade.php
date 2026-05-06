@extends('layouts.admin')

@section('title', $project->exists ? 'Edit Project' : 'Project Baru')

@section('content')
    @php
        use App\Enums\ProjectStage;
        use App\Enums\Division;

        $stage      = $project->stage ?? ProjectStage::Qualified;
        $isQualified = $stage === ProjectStage::Qualified;
        $isSubmit    = $stage === ProjectStage::Submit;
        $isWin       = $stage === ProjectStage::Win;
        $isLost      = $stage === ProjectStage::Lost;
        $next        = $stage->next();
        $canAdvance  = $project->exists && $next && ! $isLost && ($next === ProjectStage::Win ? auth()->user()->canAdvanceToWin() : true);

        $estimasiValue = old('estimasi_go_live', optional($project->estimasi_go_live)->format('Y-m-d'));
        $tanggalWinValue = old('tanggal_win', optional($project->tanggal_win)->format('Y-m-d'));
        $tanggalGoLiveValue = old('tanggal_go_live', optional($project->tanggal_go_live)->format('Y-m-d'));
        $kontrakSampaiValue = old('kontrak_sampai', optional($project->kontrak_sampai)->format('Y-m-d'));
    @endphp

    {{-- Standalone forms (placed outside main update form to avoid nested forms) --}}
    @if ($project->exists)
        @if ($canAdvance)
            <form id="advance-form" method="POST" action="{{ route('admin.projects.advance', $project) }}" enctype="multipart/form-data" class="hidden"></form>
        @endif
        @if (! $isLost)
            <form id="lost-form" method="POST" action="{{ route('admin.projects.lost', $project) }}" class="hidden">
                @csrf
            </form>
        @endif
        @if (auth()->user()->canDeleteProject())
            <form id="delete-form" method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="hidden"
                  onsubmit="return confirm('Hapus project ini? File terkait juga akan terhapus.')">
                @csrf
                @method('DELETE')
            </form>
        @endif
    @endif

    {{-- Header --}}
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-white">
                {{ $project->exists ? $project->nama_project : 'Project Baru' }}
            </h2>
            <p class="mt-0.5 text-sm text-teal-100/70">
                @if ($instansi)
                    Instansi:
                    <a href="{{ route('admin.instansi.edit', $instansi) }}" class="text-teal-300 hover:text-teal-200">{{ $instansi->nama_instansi }}</a>
                    ·
                @endif
                Stage:
                <span class="ml-1 inline-block rounded-full px-2 py-0.5 text-[11px] font-bold text-white"
                      style="background: {{ $stage->color() }};">{{ $stage->label() }}</span>
            </p>
        </div>
        <a href="{{ route('admin.projects.index') }}" class="btn-ghost rounded-lg px-3 py-1.5 text-xs font-medium">← Pipeline</a>
    </div>

    @php
        $formAction = $project->exists
            ? route('admin.projects.update', $project)
            : ($instansi ? route('admin.instansi.projects.store', $instansi) : route('admin.projects.store'));
    @endphp
    <form method="POST" action="{{ $formAction }}"
          enctype="multipart/form-data"
          class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        @csrf
        @if ($project->exists) @method('PUT') @endif

        <div class="space-y-5 lg:col-span-2">
            {{-- ── Qualified ─────────────────────────────────────── --}}
            <div class="glass space-y-4 rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-white">1. Qualified</h3>
                    <span class="rounded-full px-2 py-0.5 text-[10.5px] font-semibold"
                          style="background: rgba(14,165,233,0.15); color: #7dd3fc;">Wajib diisi</span>
                </div>

                @if (! $project->exists && ! $instansi)
                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Instansi</label>
                        <select name="instansi_id" required class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                            <option value="">— Pilih instansi —</option>
                            @foreach ($allInstansi as $i)
                                <option value="{{ $i->id }}" @selected(old('instansi_id') == $i->id)>{{ $i->nama_instansi }}</option>
                            @endforeach
                        </select>
                        @error('instansi_id') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Nama Project</label>
                        <input name="nama_project" value="{{ old('nama_project', $project->nama_project) }}" required
                               class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @error('nama_project') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Nama Pelanggan</label>
                        <input name="nama_pelanggan" value="{{ old('nama_pelanggan', $project->nama_pelanggan) }}" required
                               class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @error('nama_pelanggan') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Nomor PIC</label>
                        <input name="nomor_pic" value="{{ old('nomor_pic', $project->nomor_pic) }}" required
                               class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @error('nomor_pic') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Jabatan PIC</label>
                        <input name="jabatan_pic" value="{{ old('jabatan_pic', $project->jabatan_pic) }}" required
                               class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @error('jabatan_pic') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Division</label>
                        <select name="division" required class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                            <option value="">— Pilih —</option>
                            @foreach (Division::cases() as $d)
                                <option value="{{ $d->value }}" @selected(old('division', $project->division?->value) === $d->value)>{{ $d->label() }}</option>
                            @endforeach
                        </select>
                        @error('division') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Estimasi Go-Live</label>
                        <input name="estimasi_go_live" type="month" required
                               value="{{ $estimasiValue ? \Illuminate\Support\Carbon::parse($estimasiValue)->format('Y-m') : '' }}"
                               class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @error('estimasi_go_live') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Revenue (Rp)</label>
                        <input name="revenue" type="number" step="1000" min="0" required
                               value="{{ old('revenue', $project->revenue) }}"
                               class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @error('revenue') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- ── Submit ────────────────────────────────────────── --}}
            <div class="glass space-y-4 rounded-2xl p-5 {{ $isQualified ? 'opacity-60' : '' }}">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-white">2. Submit</h3>
                    <span class="rounded-full px-2 py-0.5 text-[10.5px] font-semibold"
                          style="background: rgba(245,158,11,0.15); color: #fbbf24;">
                        {{ $isQualified ? 'Akan diisi saat advance' : 'Aktif' }}
                    </span>
                </div>

                <div>
                    <label class="text-xs font-medium text-teal-200/80">Description</label>
                    <textarea name="description" rows="3" {{ $isQualified ? 'disabled' : '' }}
                              class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">{{ old('description', $project->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-medium text-teal-200/80">Durasi (bulan)</label>
                    <input name="durasi_bulan" type="number" min="1" max="240"
                           value="{{ old('durasi_bulan', $project->durasi_bulan) }}"
                           {{ $isQualified ? 'disabled' : '' }}
                           class="input mt-1 w-40 rounded-lg px-3 py-2 text-sm">
                    @error('durasi_bulan') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- ── Win ───────────────────────────────────────────── --}}
            <div class="glass space-y-4 rounded-2xl p-5 {{ in_array($stage, [ProjectStage::Qualified, ProjectStage::Submit], true) ? 'opacity-60' : '' }}">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-white">3. Win</h3>
                    <span class="rounded-full px-2 py-0.5 text-[10.5px] font-semibold"
                          style="background: rgba(16,185,129,0.15); color: #6ee7b7;">
                        {{ $isWin ? 'Aktif' : 'Akan diisi saat advance' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Tanggal Win</label>
                        <input name="tanggal_win" type="date" {{ $isWin ? '' : 'disabled' }}
                               value="{{ $tanggalWinValue }}"
                               class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @error('tanggal_win') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Tanggal Go-Live</label>
                        <input name="tanggal_go_live" type="date" {{ $isWin ? '' : 'disabled' }}
                               value="{{ $tanggalGoLiveValue }}"
                               class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @error('tanggal_go_live') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Kontrak Sampai</label>
                        <input name="kontrak_sampai" type="date" {{ $isWin ? '' : 'disabled' }}
                               value="{{ $kontrakSampaiValue }}"
                               class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @error('kontrak_sampai') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="text-xs font-medium text-teal-200/80">Skema Penagihan</label>
                    <input name="skema_penagihan" {{ $isWin ? '' : 'disabled' }}
                           value="{{ old('skema_penagihan', $project->skema_penagihan) }}"
                           placeholder="Contoh: Bulanan, Termin 3x, One-time"
                           class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                    @error('skema_penagihan') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>

                @foreach (['file_pks' => 'PKS', 'file_po' => 'PO', 'file_npwp' => 'NPWP Pelanggan'] as $field => $label)
                    <div class="border-t border-white/5 pt-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-medium text-teal-200/80">{{ $label }} (PDF/JPG/PNG, max 5MB)</label>
                            @if ($project->$field)
                                @php $type = str_replace('file_', '', $field); @endphp
                                <a href="{{ route('admin.projects.file', [$project, $type]) }}"
                                   class="text-[11px] font-medium text-teal-300 hover:text-teal-200">↓ Download saat ini</a>
                            @endif
                        </div>
                        <input name="{{ $field }}" type="file" accept=".pdf,.jpg,.jpeg,.png" {{ $isWin ? '' : 'disabled' }}
                               class="input mt-1 w-full rounded-lg px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-emerald-500/20 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-emerald-100 hover:file:bg-emerald-500/30">
                        @error($field) <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>

            {{-- ── Lost ──────────────────────────────────────────── --}}
            @if ($isLost)
                <div class="glass space-y-3 rounded-2xl p-5" style="border-left: 3px solid #ef4444;">
                    <h3 class="text-sm font-semibold text-red-300">Lost</h3>
                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Alasan</label>
                        <textarea name="lost_reason" rows="2" maxlength="500"
                                  class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">{{ old('lost_reason', $project->lost_reason) }}</textarea>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right rail: actions --}}
        <div class="space-y-4">
            <div class="glass space-y-3 rounded-2xl p-5">
                <h3 class="text-sm font-semibold text-white">Aksi</h3>

                <button type="submit" class="btn-primary w-full rounded-lg px-3 py-2.5 text-sm font-semibold">
                    {{ $project->exists ? 'Simpan Perubahan' : 'Simpan & Mulai Qualified' }}
                </button>

                @if ($canAdvance)
                    <button type="submit" form="advance-form"
                            class="btn-ghost w-full rounded-lg px-3 py-2 text-xs font-semibold"
                            style="border-color: {{ $next->color() }}; color: {{ $next->color() }};">
                        Advance → {{ $next->label() }}
                    </button>
                    @if ($next === ProjectStage::Win)
                        <p class="text-[11px] text-amber-200/80">Advance ke Win wajib upload file PKS, PO, NPWP. Isi field Win di atas, lalu klik tombol ini.</p>
                    @else
                        <p class="text-[11px] text-teal-200/60">Isi field Submit di atas (description + durasi), lalu klik tombol ini.</p>
                    @endif
                @endif

                @if ($project->exists && ! $isLost)
                    <button type="submit" form="lost-form"
                            class="btn-ghost w-full rounded-lg px-3 py-2 text-xs font-semibold text-red-200"
                            onclick="event.preventDefault(); const r = prompt('Alasan Lost (opsional):'); if (r === null) return; const f = document.getElementById('lost-form'); const i = document.createElement('input'); i.type='hidden'; i.name='lost_reason'; i.value = r; f.appendChild(i); f.submit();">
                        Mark as Lost
                    </button>
                @endif

                @if ($project->exists && auth()->user()->canDeleteProject())
                    <button type="submit" form="delete-form"
                            class="btn-ghost w-full rounded-lg px-3 py-2 text-xs font-semibold text-red-300">
                        Hapus Project
                    </button>
                @endif

                <a href="{{ route('admin.instansi.edit', $instansi) }}"
                   class="block rounded-lg px-3 py-2 text-center text-xs font-medium text-teal-200/70 hover:text-teal-200">
                    ← Kembali ke Instansi
                </a>
            </div>

            @if ($project->exists)
                <div class="glass rounded-2xl p-5">
                    <h3 class="text-sm font-semibold text-white">Info</h3>
                    <dl class="mt-2 space-y-1.5 text-[11.5px] text-teal-100/80">
                        <div class="flex justify-between"><dt>Stage</dt><dd class="font-semibold" style="color: {{ $stage->color() }};">{{ $stage->label() }}</dd></div>
                        <div class="flex justify-between"><dt>Dibuat</dt><dd>{{ $project->created_at?->format('d M Y') }}</dd></div>
                        <div class="flex justify-between"><dt>Update</dt><dd>{{ $project->updated_at?->diffForHumans() }}</dd></div>
                        @if ($project->creator)
                            <div class="flex justify-between"><dt>Oleh</dt><dd>{{ $project->creator->name }}</dd></div>
                        @endif
                    </dl>
                </div>
            @endif
        </div>
    </form>
@endsection
