@extends('layouts.admin')

@section('title', 'Witel')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-white">Daftar Witel</h2>
            <p class="mt-0.5 text-sm text-teal-100/70">Kelola Wilayah Telekomunikasi (Witel).</p>
        </div>
        <a href="{{ route('admin.witel.create') }}" class="btn-primary rounded-lg px-4 py-2 text-sm font-semibold">
            + Witel Baru
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-2 text-sm text-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="glass overflow-hidden rounded-2xl">
        <table class="w-full text-left text-sm">
            <thead class="bg-white/5 text-[11px] uppercase tracking-wider text-teal-200/70">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Jumlah Instansi</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($witels as $w)
                    <tr class="hover:bg-white/5">
                        <td class="px-4 py-3 font-medium text-white">{{ $w->name }}</td>
                        <td class="px-4 py-3 text-teal-100/85">{{ $w->code ?? '—' }}</td>
                        <td class="px-4 py-3 text-teal-100/85">{{ $w->instansi_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.witel.edit', $w) }}" class="btn-ghost rounded-lg px-3 py-1.5 text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.witel.destroy', $w) }}" class="ml-1 inline-block"
                                  onsubmit="return confirm('Hapus Witel ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-ghost rounded-lg px-3 py-1.5 text-xs text-red-200">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-teal-200/60">
                            Belum ada Witel. Klik <span class="text-white">+ Witel Baru</span>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $witels->links() }}</div>
@endsection
