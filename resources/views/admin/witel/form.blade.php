@extends('layouts.admin')

@section('title', $witel->exists ? 'Edit Witel' : 'Witel Baru')

@section('content')
    <form method="POST"
          action="{{ $witel->exists ? route('admin.witel.update', $witel) : route('admin.witel.store') }}"
          class="max-w-xl">
        @csrf
        @if ($witel->exists) @method('PUT') @endif

        <div class="glass space-y-4 rounded-2xl p-5">
            <h3 class="text-sm font-semibold text-white">Informasi Witel</h3>

            <div>
                <label class="text-xs font-medium text-teal-200/80">Nama Witel</label>
                <input name="name" value="{{ old('name', $witel->name) }}" required
                       class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                @error('name') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-medium text-teal-200/80">Kode (opsional)</label>
                <input name="code" value="{{ old('code', $witel->code) }}"
                       class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                @error('code') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn-primary rounded-lg px-4 py-2 text-sm font-semibold">
                    {{ $witel->exists ? 'Simpan Perubahan' : 'Simpan Witel' }}
                </button>
                <a href="{{ route('admin.witel.index') }}" class="btn-ghost rounded-lg px-4 py-2 text-sm font-medium">
                    Batal
                </a>
            </div>
        </div>
    </form>
@endsection
