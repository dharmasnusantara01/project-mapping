@extends('layouts.admin')

@section('title', $accountManager->exists ? 'Edit Account Manager' : 'Account Manager Baru')

@section('content')
    <form method="POST"
          action="{{ $accountManager->exists ? route('admin.account_managers.update', $accountManager) : route('admin.account_managers.store') }}"
          class="max-w-xl">
        @csrf
        @if ($accountManager->exists) @method('PUT') @endif

        <div class="glass space-y-4 rounded-2xl p-5">
            <h3 class="text-sm font-semibold text-white">Informasi Account Manager</h3>

            <div>
                <label class="text-xs font-medium text-teal-200/80">Nama</label>
                <input name="name" value="{{ old('name', $accountManager->name) }}" required
                       class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                @error('name') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-xs font-medium text-teal-200/80">NIK (opsional)</label>
                    <input name="nik" value="{{ old('nik', $accountManager->nik) }}"
                           class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                    @error('nik') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-teal-200/80">Telpon (opsional)</label>
                    <input name="phone" value="{{ old('phone', $accountManager->phone) }}"
                           class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                    @error('phone') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="text-xs font-medium text-teal-200/80">Email (opsional)</label>
                <input name="email" type="email" value="{{ old('email', $accountManager->email) }}"
                       class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                @error('email') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn-primary rounded-lg px-4 py-2 text-sm font-semibold">
                    {{ $accountManager->exists ? 'Simpan Perubahan' : 'Simpan AM' }}
                </button>
                <a href="{{ route('admin.account_managers.index') }}" class="btn-ghost rounded-lg px-4 py-2 text-sm font-medium">
                    Batal
                </a>
            </div>
        </div>
    </form>
@endsection
