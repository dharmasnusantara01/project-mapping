@extends('layouts.admin')

@section('title', 'Instansi')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-white">Daftar Instansi</h2>
            <p class="mt-0.5 text-sm text-teal-100/70">Kelola instansi untuk peta sebaran publik.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.instansi.import') }}" class="btn-ghost rounded-lg px-4 py-2 text-sm font-semibold">
                ↑ Import CSV
            </a>
            <a href="{{ route('admin.instansi.create') }}" class="btn-primary rounded-lg px-4 py-2 text-sm font-semibold">
                + Instansi Baru
            </a>
        </div>
    </div>

    @if (session('importErrors') && count(session('importErrors')))
        <div class="mb-4 rounded-lg border border-amber-400/30 bg-amber-500/10 p-3 text-xs text-amber-100">
            <div class="font-semibold">Beberapa baris di-skip:</div>
            <ul class="mt-1 list-inside list-disc space-y-0.5">
                @foreach (session('importErrors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass overflow-hidden rounded-2xl">
        <table class="w-full text-left text-sm">
            <thead class="bg-white/5 text-[11px] uppercase tracking-wider text-teal-200/70">
                <tr>
                    <th class="px-4 py-3">Nama Instansi</th>
                    <th class="px-4 py-3">Witel</th>
                    <th class="px-4 py-3">Account Manager</th>
                    <th class="px-4 py-3">Sektor</th>
                    <th class="px-4 py-3">Telpon</th>
                    <th class="px-4 py-3">Publikasi</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($instansi as $i)
                    <tr class="hover:bg-white/5">
                        <td class="px-4 py-3">
                            <div class="font-medium text-white">{{ $i->nama_instansi }}</div>
                            <div class="text-[11px] text-teal-200/60">{{ \Illuminate\Support\Str::limit($i->alamat_instansi, 60) }}</div>
                        </td>
                        <td class="px-4 py-3 text-teal-100/85">{{ $i->witel?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-teal-100/85">{{ $i->accountManager?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="badge-soft" style="color: {{ $i->sector?->color }};">
                                <span class="h-1.5 w-1.5 rounded-full" style="background: {{ $i->sector?->color }};"></span>
                                {{ $i->sector?->name }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-teal-100/85">{{ $i->telpon_instansi ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($i->is_public)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-2.5 py-1 text-[11px] font-semibold text-emerald-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span> Published
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-500/20 px-2.5 py-1 text-[11px] font-semibold text-slate-200">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.instansi.edit', $i) }}"
                               class="btn-ghost rounded-lg px-3 py-1.5 text-xs">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-teal-200/60">
                            Belum ada instansi. Klik <span class="text-white">+ Instansi Baru</span>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $instansi->links() }}
    </div>
@endsection
