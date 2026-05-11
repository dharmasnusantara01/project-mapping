@extends('layouts.admin')

@section('title', $accountManager->exists ? 'Edit Account Manager' : 'Account Manager Baru')

@section('content')
    {{-- Standalone form for "test telegram" — placed outside main form to avoid nested-form bug --}}
    @if ($accountManager->exists && $accountManager->telegram_chat_id)
        <form id="test-telegram-form" method="POST"
              action="{{ route('admin.account_managers.test_telegram', $accountManager) }}"
              class="hidden">
            @csrf
        </form>
    @endif

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
        </div>

        <div class="glass mt-4 space-y-3 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-white">Reminder Telegram</h3>
                @if ($accountManager->exists && $accountManager->last_reminded_at)
                    <span class="text-[11px] text-teal-200/60">
                        Terakhir kirim: {{ $accountManager->last_reminded_at->diffForHumans() }}
                    </span>
                @endif
            </div>

            <div>
                <label class="text-xs font-medium text-teal-200/80">Telegram Chat ID</label>
                <input name="telegram_chat_id" value="{{ old('telegram_chat_id', $accountManager->telegram_chat_id) }}"
                       inputmode="numeric" pattern="-?\d+"
                       placeholder="contoh: 123456789"
                       class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                <p class="mt-1 text-[11px] text-teal-200/60">
                    Cara dapat: AM chat ke <code class="rounded bg-black/30 px-1">@userinfobot</code> di Telegram, copy chat ID, tempel di sini.
                    AM juga harus chat <code class="rounded bg-black/30 px-1">/start</code> ke bot kita supaya bot bisa kirim DM.
                </p>
                @error('telegram_chat_id') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
            </div>

            @if ($accountManager->exists && $accountManager->telegram_chat_id)
                <button type="submit" form="test-telegram-form"
                        class="btn-ghost rounded-lg px-3 py-2 text-xs font-semibold">
                    🔔 Kirim Test Pesan
                </button>
            @endif
        </div>

        <div class="mt-4 flex gap-2">
            <button type="submit" class="btn-primary rounded-lg px-4 py-2 text-sm font-semibold">
                {{ $accountManager->exists ? 'Simpan Perubahan' : 'Simpan AM' }}
            </button>
            <a href="{{ route('admin.account_managers.index') }}" class="btn-ghost rounded-lg px-4 py-2 text-sm font-medium">
                Batal
            </a>
        </div>
    </form>
@endsection
