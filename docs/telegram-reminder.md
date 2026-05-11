# Telegram Bot — Stale Project Alert untuk AM

Bot Telegram yang DM tiap **Account Manager (AM)** ketika ada project pipeline-nya **tidak ada perubahan progress selama ≥7 hari**. Tujuannya: nudge supaya tidak ada project yang "diam" terlalu lama tanpa follow-up.

---

## Trigger Logic

| Kriteria                        | Detail                                                                              |
|---------------------------------|-------------------------------------------------------------------------------------|
| **Stage yang dimonitor**        | `qualified` & `submit` (project aktif). `win` & `lost` di-skip (sudah final).        |
| **Threshold stale**             | `updated_at < now() − 7 hari`                                                       |
| **Re-alert window**             | 7 hari setelah alert pertama, kalau project masih stale → alert lagi                |
| **Schedule polling**            | Setiap pagi 08:00 WIB (cron). Cek semua project, kirim hanya kalau ada yang stale.   |
| **AM tanpa project stale**      | Tidak dapat pesan apapun (tidak ada digest harian)                                  |

Setelah alert sukses dikirim:
- Field `projects.last_stale_alert_at` di-set ke `now()`.
- Field `projects.updated_at` **TIDAK** di-touch (pakai `Model::withoutTimestamps()`), supaya sistem tidak menganggap project sudah ada progress padahal belum.
- Field `account_managers.last_reminded_at` di-set untuk audit trail.

---

## Apa yang AM Terima

Pesan fokus pada project yang stagnant:

```
🚨 Heads up, Budi Santoso

Ada 2 project tanpa progress >7 hari. Mohon di-update statusnya:

1. Pengadaan Internet Pusat
   • Stage: Submit (15 hari diam)
   • Instansi: Kanim Pontianak
   • Revenue: Rp 850.000.000
2. SD-WAN Cabang
   • Stage: Qualified (9 hari diam)
   • Instansi: RSUD Ulin
   • Revenue: Rp 1.200.000.000

🔗 Buka pipeline
```

Project dengan progress baru (Sales update field apapun di project) otomatis hilang dari alert berikutnya karena `updated_at` ter-refresh ke kondisi tidak stale.

---

## Setup (Sekali per Lingkungan)

### 1. Buat bot Telegram

1. Buka Telegram, chat ke [`@BotFather`](https://t.me/BotFather).
2. Kirim `/newbot` → ikuti prompt (nama display + username harus diakhiri `bot`, mis. `gridcore_alert_bot`).
3. BotFather balas dengan **HTTP API token** (format: `1234567890:ABCdef-ghi-...`). **Simpan**.

### 2. Konfigurasi `.env`

Tambahkan ke `.env`:

```env
TELEGRAM_BOT_TOKEN=1234567890:ABCdef-ghi-...
APP_TIMEZONE=Asia/Jakarta
```

Kosongkan cache config:

```bash
php artisan config:clear
```

### 3. Cron untuk scheduler (di server production)

Pasang **sekali** di crontab user yang menjalankan app:

```cron
* * * * * cd /path/ke/app && php artisan schedule:run >> /dev/null 2>&1
```

Verify schedule terdaftar:

```bash
php artisan schedule:list
# harus muncul: am:remind  →  daily 08:00  Asia/Jakarta
```

---

## Onboarding AM Baru

### 1. AM dapatkan chat ID-nya

1. AM buka Telegram, search `@userinfobot`, klik **Start**.
2. Bot reply dengan info user, termasuk angka **Id** (mis. `123456789`).
3. AM copy angka tersebut, kirim ke admin (via WA/email/Slack).

### 2. AM aktifkan chat dengan bot kita

> **PENTING**: Telegram tidak izinkan bot inisiasi DM ke user. AM **WAJIB** chat `/start` ke bot kita sekali, supaya bot punya izin kirim DM.

1. AM search username bot kita di Telegram (mis. `@gridcore_alert_bot`).
2. Klik **Start** atau kirim `/start`.
3. Bot tidak perlu reply — yang penting AM sudah inisiasi chat.

### 3. Admin paste chat ID

1. Login admin → menu **Account Manager** → pilih AM → **Edit**.
2. Section **Reminder Telegram** → paste chat ID di field **Telegram Chat ID**.
3. **Simpan Perubahan**.
4. Klik **🔔 Kirim Test Pesan** → kalau Telegram AM dapat pesan test, setup OK.

Kalau test gagal:
- Cek `storage/logs/laravel.log` baris terakhir.
- Pastikan AM sudah `/start` dengan bot.
- Pastikan chat ID benar (angka, bisa negatif untuk grup).

---

## Operasional

Sistem otomatis polling tiap pagi 08:00 WIB. Tidak ada aksi manual yang diperlukan. AM hanya dapat DM **kalau ada project stale** — kalau pipeline-nya rapi, mereka tidak diganggu.

Audit:
- `account_managers.last_reminded_at` — terakhir kali AM dapat alert (apapun isinya).
- `projects.last_stale_alert_at` — per project, terakhir kali project ini di-alert sebagai stale.

---

## Command Reference

```bash
# Kirim sekarang ke semua AM yang punya stale projects
php artisan am:remind

# Kirim hanya untuk AM tertentu (debug)
php artisan am:remind --am=5

# Preview pesan ke console tanpa kirim & tanpa update last_stale_alert_at
php artisan am:remind --dry-run

# Kombinasi
php artisan am:remind --am=5 --dry-run

# Cek schedule
php artisan schedule:list
```

Exit code:
- `0` — sukses (atau dry-run selesai)
- `1` — token tidak diset, atau ≥1 AM gagal kirim

---

## Tuning Konstanta

Threshold stale & re-alert window di-define sebagai konstanta di [`app/Services/AmReminderBuilder.php`](../app/Services/AmReminderBuilder.php):

```php
public const STALE_DAYS = 7;     // umur diam minimal sebelum dianggap stale
public const REALERT_DAYS = 7;   // jeda antar re-alert kalau masih stale
```

Edit nilai, clear cache, selesai. Tidak butuh migration.

---

## Troubleshooting

| Gejala                                            | Penyebab                                                                | Solusi                                                                              |
|---------------------------------------------------|-------------------------------------------------------------------------|-------------------------------------------------------------------------------------|
| `TELEGRAM_BOT_TOKEN tidak diset di .env`          | Env belum di-set atau cache stale                                       | Set di `.env`, lalu `php artisan config:clear`                                       |
| Test send → "Gagal kirim test pesan"              | Token salah, atau AM belum `/start` dengan bot                          | Cek `storage/logs/laravel.log` baris terakhir. Validasi token via cURL (lihat bawah) |
| Telegram return 403 "bot was blocked by user"     | AM block bot, atau belum pernah `/start`                                | AM unblock + `/start` ulang                                                          |
| Telegram return 400 "chat not found"              | Chat ID salah                                                            | AM ulang via `@userinfobot`, paste ulang ke form AM                                  |
| Schedule list tidak menampilkan `am:remind`       | `routes/console.php` tidak ter-load (cache)                             | `php artisan optimize:clear`                                                         |
| AM dapat alert tapi project sudah di-update       | `last_stale_alert_at` tidak ter-update setelah alert sukses             | Cek log; verify command pakai `Project::withoutTimestamps()` saat update `last_stale_alert_at` |
| AM dapat alert berulang harian (spam)             | `last_stale_alert_at` ter-touch oleh proses lain                        | Pastikan code lain juga pakai `withoutTimestamps()` kalau update kolom itu          |

### Validasi token via cURL

```bash
curl -s "https://api.telegram.org/bot${TOKEN}/getMe"
# harus reply: {"ok":true,"result":{"id":...,"is_bot":true,"first_name":"..."}}
```

### Validasi bisa kirim ke chat ID tertentu

```bash
curl -s -X POST "https://api.telegram.org/bot${TOKEN}/sendMessage" \
  -d "chat_id=${CHAT_ID}" -d "text=test"
# kalau sukses: {"ok":true,...}
# kalau "chat not found" → AM belum /start dengan bot
```

---

## Arsitektur Internal

| Komponen           | Lokasi                                                                                  | Tanggung Jawab                                              |
|--------------------|-----------------------------------------------------------------------------------------|-------------------------------------------------------------|
| Schedule           | [routes/console.php](../routes/console.php)                                             | Trigger `am:remind` setiap hari 08:00 WIB                   |
| Command            | [app/Console/Commands/SendAmReminder.php](../app/Console/Commands/SendAmReminder.php)   | Iterasi AM, kirim alert hanya kalau ada stale projects, update `last_stale_alert_at` per project (tanpa touch `updated_at`) |
| Builder            | [app/Services/AmReminderBuilder.php](../app/Services/AmReminderBuilder.php)             | Query stale projects per AM (dengan re-alert window) + format pesan HTML |
| Notifier           | [app/Services/TelegramNotifier.php](../app/Services/TelegramNotifier.php)               | Wrapper Telegram Bot API (`sendMessage`)                    |
| Migrations         | [database/migrations/2026_05_06_000002_add_telegram_to_account_managers.php](../database/migrations/2026_05_06_000002_add_telegram_to_account_managers.php) | Kolom Telegram di `account_managers`                        |
|                    | [database/migrations/2026_05_06_000003_add_last_stale_alert_at_to_projects.php](../database/migrations/2026_05_06_000003_add_last_stale_alert_at_to_projects.php) | Kolom `last_stale_alert_at` di `projects`                   |
| Model AM           | [app/Models/AccountManager.php](../app/Models/AccountManager.php)                       | Fillable + cast datetime untuk `last_reminded_at`           |
| Model Project      | [app/Models/Project.php](../app/Models/Project.php)                                     | Fillable + cast datetime untuk `last_stale_alert_at`        |
| Controller (test)  | [app/Http/Controllers/Admin/AccountManagerController.php](../app/Http/Controllers/Admin/AccountManagerController.php) `testTelegram()` | Tombol Test Send dari form edit AM                          |
| View (form AM)     | [resources/views/admin/account_managers/form.blade.php](../resources/views/admin/account_managers/form.blade.php) | Field `telegram_chat_id` + tombol Test                      |
| Config             | [config/services.php](../config/services.php) `telegram`                                | Bot token + API base                                         |

---

## SQL Query Inti (referensi)

Query yang menentukan project mana yang masuk alert (di-build via Eloquent di `AmReminderBuilder::staleProjects`):

```sql
SELECT p.*
FROM projects p
JOIN instansi i ON i.id = p.instansi_id
WHERE i.account_manager_id = :am_id
  AND p.stage IN ('qualified', 'submit')
  AND p.updated_at < NOW() - INTERVAL 7 DAY
  AND (
    p.last_stale_alert_at IS NULL
    OR p.last_stale_alert_at < NOW() - INTERVAL 7 DAY
  )
ORDER BY p.updated_at ASC;
```

---

## Roadmap (Belum Diimplementasi)

- **Auto-link via `/start <token>`** — AM kirim `/start <kode-unik>` ke bot kita, webhook auto-bind chat ID ke AM. Hilangkan kebutuhan paste manual. Butuh setup webhook + HTTPS publik.
- **Per-stage threshold** — Qualified bisa lebih ketat (5 hari) dari Submit (10 hari). Threshold per stage di tabel sectors atau config.
- **Escalation ke manajer** — kalau project stale >14 hari, kirim juga ke manajer sales (bukan cuma AM).
- **Bot 2-way** — AM kirim `/snooze <project_id> 7d` ke bot untuk skip alert sementara.
- **Notifikasi event-driven lain** — kirim DM ke AM ketika project-nya di-publish ke peta, atau ada update dari manajer.
