@extends('layouts.admin')

@section('title', 'Project')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-white">Daftar Project</h2>
            <p class="mt-0.5 text-sm text-teal-100/70">Kelola project untuk peta sebaran publik.</p>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="btn-primary rounded-lg px-4 py-2 text-sm font-semibold">
            + Project Baru
        </a>
    </div>

    <div class="glass overflow-hidden rounded-2xl">
        <table class="w-full text-left text-sm">
            <thead class="bg-white/5 text-[11px] uppercase tracking-wider text-teal-200/70">
                <tr>
                    <th class="px-4 py-3">Project</th>
                    <th class="px-4 py-3">Sektor</th>
                    <th class="px-4 py-3">Lokasi</th>
                    <th class="px-4 py-3">Tahun</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Publikasi</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($projects as $p)
                    <tr class="hover:bg-white/5">
                        <td class="px-4 py-3">
                            <div class="font-medium text-white">{{ $p->name }}</div>
                            <div class="text-[11px] text-teal-200/60">{{ $p->customer_name }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge-soft" style="color: {{ $p->sector?->color }};">
                                <span class="h-1.5 w-1.5 rounded-full" style="background: {{ $p->sector?->color }};"></span>
                                {{ $p->sector?->name }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-teal-100/85">
                            {{ $p->primaryLocation?->city ?? '—' }}
                            <div class="text-[11px] text-teal-200/60">{{ $p->primaryLocation?->province }}</div>
                        </td>
                        <td class="px-4 py-3 text-teal-100/85">{{ $p->year }}</td>
                        <td class="px-4 py-3">
                            @php
                                $isSelesai = $p->public_status->value === 'selesai';
                                $statusColor = $isSelesai ? '#86efac' : '#fde68a';
                            @endphp
                            <span class="badge-soft" style="color: {{ $statusColor }};">
                                {{ $p->public_status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($p->is_public)
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
                            <a href="{{ route('admin.projects.edit', $p) }}"
                               class="btn-ghost rounded-lg px-3 py-1.5 text-xs">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-teal-200/60">
                            Belum ada project. Klik <span class="text-white">+ Project Baru</span>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $projects->links() }}
    </div>
@endsection
