@extends('layouts.admin')

@section('title', 'Pipeline Project')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-white">Pipeline Project</h2>
            <p class="mt-0.5 text-sm text-teal-100/70">Tracker sales lifecycle: Qualified → Submit → Win (atau Lost).</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="flex rounded-lg border border-white/5 bg-black/30 p-0.5 text-xs">
                <button type="button" data-view="kanban" class="view-toggle rounded-md px-3 py-1.5 font-medium text-teal-100 bg-emerald-500/20">Kanban</button>
                <button type="button" data-view="list"   class="view-toggle rounded-md px-3 py-1.5 font-medium text-teal-200/70">List</button>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="btn-primary rounded-lg px-4 py-2 text-xs font-semibold">+ Project Baru</a>
        </div>
    </div>

    {{-- Filter bar --}}
    <form method="GET" class="glass mb-4 flex flex-wrap items-end gap-3 rounded-2xl p-4">
        <div class="flex-1 min-w-[180px]">
            <label class="text-[10px] uppercase tracking-wider text-teal-200/70">Cari</label>
            <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nama project / pelanggan"
                   class="input mt-1 w-full rounded-lg px-3 py-2 text-xs">
        </div>
        <div class="min-w-[160px]">
            <label class="text-[10px] uppercase tracking-wider text-teal-200/70">Witel</label>
            <select name="witel_id" class="input mt-1 w-full rounded-lg px-3 py-2 text-xs">
                <option value="">Semua</option>
                @foreach ($witels as $w)
                    <option value="{{ $w->id }}" @selected(($filters['witel_id'] ?? '') == $w->id)>{{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[180px]">
            <label class="text-[10px] uppercase tracking-wider text-teal-200/70">Account Manager</label>
            <select name="account_manager_id" class="input mt-1 w-full rounded-lg px-3 py-2 text-xs">
                <option value="">Semua</option>
                @foreach ($accountManagers as $am)
                    <option value="{{ $am->id }}" @selected(($filters['account_manager_id'] ?? '') == $am->id)>{{ $am->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[140px]">
            <label class="text-[10px] uppercase tracking-wider text-teal-200/70">Division</label>
            <select name="division" class="input mt-1 w-full rounded-lg px-3 py-2 text-xs">
                <option value="">Semua</option>
                @foreach ($divisions as $d)
                    <option value="{{ $d->value }}" @selected(($filters['division'] ?? '') === $d->value)>{{ $d->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button class="btn-primary rounded-lg px-4 py-2 text-xs font-semibold">Terapkan</button>
            <a href="{{ route('admin.projects.index') }}" class="btn-ghost rounded-lg px-4 py-2 text-xs font-medium">Reset</a>
        </div>
    </form>

    {{-- Kanban view --}}
    <div id="view-kanban" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        @foreach ($stages as $stage)
            @php $items = $byStage->get($stage->value, collect()); @endphp
            <div class="glass rounded-2xl p-3" style="border-top: 3px solid {{ $stage->color() }};">
                <div class="mb-3 flex items-center justify-between px-1">
                    <h3 class="text-sm font-semibold" style="color: {{ $stage->color() }};">{{ $stage->label() }}</h3>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold text-white"
                          style="background: {{ $stage->color() }}; opacity: .85;">{{ $items->count() }}</span>
                </div>

                <div class="space-y-2">
                    @forelse ($items as $p)
                        <div class="group rounded-xl border border-white/5 bg-black/25 p-3 transition hover:border-teal-400/30">
                            <a href="{{ route('admin.projects.edit', $p) }}" class="block">
                                <div class="text-xs font-semibold text-white">{{ $p->nama_project }}</div>
                                <div class="mt-0.5 text-[11px] text-teal-200/70">{{ $p->nama_pelanggan }}</div>
                                <div class="mt-1 text-[10.5px] text-teal-200/55">
                                    @if ($p->instansi)
                                        <span>{{ $p->instansi->nama_instansi }}</span>
                                    @endif
                                </div>
                            </a>
                            <div class="mt-2 flex flex-wrap items-center justify-between gap-2 border-t border-white/5 pt-2">
                                <span class="rounded-full bg-white/5 px-2 py-0.5 text-[10px] font-semibold text-teal-200">
                                    {{ $p->division?->shortCode() ?? '—' }}
                                </span>
                                <span class="text-[10.5px] text-emerald-300">Rp {{ number_format((float) $p->revenue, 0, ',', '.') }}</span>
                            </div>
                            <div class="mt-1 text-[10px] text-teal-200/55">
                                Est. Go-Live: {{ $p->estimasi_go_live?->translatedFormat('M Y') }}
                            </div>
                            <div class="mt-2 flex gap-1.5 border-t border-white/5 pt-2">
                                <a href="{{ route('admin.projects.edit', $p) }}"
                                   class="flex-1 rounded-md bg-emerald-500/15 px-2 py-1 text-center text-[10.5px] font-semibold text-emerald-200 hover:bg-emerald-500/25">Edit</a>
                                @if (auth()->user()->canDeleteProject())
                                    <form method="POST" action="{{ route('admin.projects.destroy', $p) }}"
                                          onsubmit="return confirm('Hapus project {{ $p->nama_project }}?');">
                                        @csrf @method('DELETE')
                                        <button class="rounded-md bg-red-500/15 px-2 py-1 text-[10.5px] font-semibold text-red-200 hover:bg-red-500/25">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-white/5 px-3 py-6 text-center text-[11px] text-teal-200/40">
                            Tidak ada project di stage ini.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- List view (alternative) --}}
    @php
        $allProjects = $byStage->flatten();
    @endphp
    <div id="view-list" class="glass mt-2 hidden overflow-hidden rounded-2xl">
        <table class="w-full text-left text-sm">
            <thead class="bg-white/5 text-[11px] uppercase tracking-wider text-teal-200/70">
                <tr>
                    <th class="px-4 py-3">Nama Project</th>
                    <th class="px-4 py-3">Instansi</th>
                    <th class="px-4 py-3">Pelanggan</th>
                    <th class="px-4 py-3">Stage</th>
                    <th class="px-4 py-3">Division</th>
                    <th class="px-4 py-3">Revenue</th>
                    <th class="px-4 py-3">Est. Go-Live</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($allProjects as $p)
                    <tr class="hover:bg-white/5">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.projects.edit', $p) }}" class="font-medium text-white hover:text-teal-200">{{ $p->nama_project }}</a>
                        </td>
                        <td class="px-4 py-3 text-teal-100/85">{{ $p->instansi?->nama_instansi ?? '—' }}</td>
                        <td class="px-4 py-3 text-teal-100/85">{{ $p->nama_pelanggan }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-[10.5px] font-bold text-white"
                                  style="background: {{ $p->stage->color() }};">{{ $p->stage->label() }}</span>
                        </td>
                        <td class="px-4 py-3 text-teal-100/85">{{ $p->division?->label() ?? '—' }}</td>
                        <td class="px-4 py-3 text-emerald-300">Rp {{ number_format((float) $p->revenue, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-[11px] text-teal-200/70">{{ $p->estimasi_go_live?->translatedFormat('M Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.projects.edit', $p) }}" class="btn-ghost rounded-lg px-3 py-1.5 text-xs">Edit</a>
                            @if (auth()->user()->canDeleteProject())
                                <form method="POST" action="{{ route('admin.projects.destroy', $p) }}" class="ml-1 inline-block"
                                      onsubmit="return confirm('Hapus project {{ $p->nama_project }}?');">
                                    @csrf @method('DELETE')
                                    <button class="btn-ghost rounded-lg px-3 py-1.5 text-xs text-red-200">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-sm text-teal-200/60">
                            Belum ada project. Klik <span class="text-white">+ Project Baru</span>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        document.querySelectorAll('.view-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const view = btn.dataset.view;
                document.querySelectorAll('.view-toggle').forEach(b => {
                    b.classList.toggle('bg-emerald-500/20', b.dataset.view === view);
                    b.classList.toggle('text-teal-100', b.dataset.view === view);
                    b.classList.toggle('text-teal-200/70', b.dataset.view !== view);
                });
                document.getElementById('view-kanban').classList.toggle('hidden', view !== 'kanban');
                document.getElementById('view-list').classList.toggle('hidden', view !== 'list');
            });
        });
    </script>
@endsection
