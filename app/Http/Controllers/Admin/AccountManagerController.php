<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountManager;
use App\Services\TelegramNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class AccountManagerController extends Controller
{
    public function index()
    {
        $accountManagers = AccountManager::withCount('instansi')->orderBy('name')->paginate(20);

        return view('admin.account_managers.index', ['accountManagers' => $accountManagers]);
    }

    public function create()
    {
        return view('admin.account_managers.form', ['accountManager' => new AccountManager()]);
    }

    public function store(Request $request)
    {
        AccountManager::create($this->validated($request));
        Cache::forget('public.instansi');

        return redirect()->route('admin.account_managers.index')->with('status', 'Account Manager tersimpan.');
    }

    public function edit(AccountManager $accountManager)
    {
        return view('admin.account_managers.form', ['accountManager' => $accountManager]);
    }

    public function update(Request $request, AccountManager $accountManager)
    {
        $accountManager->update($this->validated($request, $accountManager->id));
        Cache::forget('public.instansi');

        return redirect()->route('admin.account_managers.index')->with('status', 'Account Manager diperbarui.');
    }

    public function destroy(AccountManager $accountManager)
    {
        if ($accountManager->instansi()->exists()) {
            return back()->withErrors(['account_manager' => 'AM tidak bisa dihapus karena masih dipakai instansi.']);
        }

        $accountManager->delete();

        return redirect()->route('admin.account_managers.index')->with('status', 'Account Manager dihapus.');
    }

    public function testTelegram(AccountManager $accountManager, TelegramNotifier $telegram)
    {
        if (! $accountManager->telegram_chat_id) {
            return back()->withErrors(['telegram' => 'Chat ID Telegram belum diisi untuk AM ini.']);
        }
        if (! $telegram->isConfigured()) {
            return back()->withErrors(['telegram' => 'TELEGRAM_BOT_TOKEN belum diset di .env.']);
        }

        $text = "🔔 Test pesan dari GridCore\n\n"
            . "Halo <b>" . e($accountManager->name) . "</b>! Reminder pipeline akan dikirim setiap pagi pukul 08:00 WIB.";

        $ok = $telegram->sendMessage($accountManager->telegram_chat_id, $text);

        return back()->with('status',
            $ok
                ? 'Test pesan terkirim. Cek Telegram AM.'
                : 'Gagal kirim test pesan — cek log Laravel.'
        );
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'             => ['required', 'string', 'max:200'],
            'nik'              => ['nullable', 'string', 'max:50', Rule::unique('account_managers', 'nik')->ignore($ignoreId)],
            'email'            => ['nullable', 'email', 'max:200'],
            'phone'            => ['nullable', 'string', 'max:30'],
            'telegram_chat_id' => ['nullable', 'string', 'max:50', 'regex:/^-?\d+$/'],
        ]);
    }
}
