# 🚦 CHECKPOINT — Funneling External

> Catatan kemajuan kerja. Dipakai untuk melanjutkan di session/AI run berikutnya.

---

## 📍 Status: Phase 1–4 selesai (vertical slice end-to-end), Phase 5–6 belum

Last update: 2026-05-01 (Phase 1–4 dipangkas, vertical slice MVP jalan)

---

## ✅ Sudah Selesai

### 1. Setup Project
- Laravel 13.7.0 + PHP 8.3, Pest 4.6.
- MySQL via `compose.yaml` (port host **3308**, db `funneling_external`, user `funneling`).
- `.env` sesuai (DB_PORT=3308).

### 2. Migrations (5 file — semua jalan)

| File | Tabel |
|------|-------|
| `2026_05_01_073626_create_sectors_table.php`            | `sectors` |
| `2026_05_01_073626_create_city_references_table.php`    | `city_references` |
| `2026_05_01_073627_create_projects_table.php`           | `projects` |
| `2026_05_01_073628_create_project_locations_table.php`  | `project_locations` |
| `2026_05_01_073629_add_nip_and_role_to_users_table.php` | `users.nip` (unique) + `users.role` (enum) |

### 3. Enums
- `App\Enums\PublicStatus` — `Berjalan`, `Selesai` (+ `label()`, `options()`).
- `App\Enums\UserRole` — `Sales`, `ManajerSales`, `Superadmin` (+ `canPublish()`).

### 4. Models + Relasi
- `Sector` → hasMany Project.
- `CityReference` (read-only seed table).
- `Project` → belongsTo Sector, hasMany ProjectLocation, hasOne `primaryLocation`, belongsTo User (`publisher`). Scope `published()`.
- `ProjectLocation` → belongsTo Project. Casts decimal:7.
- `User` — pakai PHP attribute `#[Fillable(['nip','name','email','role','password'])]`. Cast role ke `UserRole`. Method `canPublishProjects()`.

### 5. Factories
- `UserFactory` — default Sales; states `manajerSales()`, `superadmin()`, `unverified()`.
- `SectorFactory`, `CityReferenceFactory`, `ProjectFactory` (states `berjalan/selesai/published`), `ProjectLocationFactory`.

### 6. Seeders
- `SectorSeeder` — 3 sektor v1: **Imigrasi** `#1d4ed8`, **Polda** `#dc2626`, **Kemkes** `#16a34a`.
- `CityReferenceSeeder` — 46 kota: 22 Kalimantan (Kalbar/Kalteng/Kalsel/Kaltim/Kaltara) + 24 ibukota provinsi luar Kalimantan, dengan koordinat akurat.
- `UserSeeder` — 3 user demo (NIP/role):
  - `198001012005011001` — Superadmin Demo (`superadmin@funneling.test`)
  - `198505152010012002` — Manajer Sales Demo (`manajer@funneling.test`)
  - `199203202015031003` — Sales Rep Demo (`sales@funneling.test`)
  - Password semua: `password`.
- `ProjectSeeder` — 20 project (18 published, 2 draft), tersebar di Kalimantan + sample Jakarta/Surabaya/Makassar. Sector mix 3-3-3 + variasi status.
- `DatabaseSeeder` memanggil 4 seeder berurutan.

### 7. Authentication
- `App\Http\Controllers\Auth\LoginController` — login pakai **NIP + password** (bukan email), pakai `Auth::attempt(['nip'=>..., 'password'=>...])`.
- Routes: `GET/POST /login` (guest), `POST /logout` (auth), redirect `/admin` → `admin.projects.index`.
- Login view sudah ada (`resources/views/auth/login.blade.php`) — UI dark-teal sesuai mockup, bagian peta SVG masih placeholder kasar (TBD: pakai TopoJSON Indonesia kalau mau presisi).

### 8. Public Map (Phase 2 + 3 + 4 — vertical slice)
- Route `GET /` → `PublicMapController@index` view `public.map`.
- Route `GET /api/public/projects` → JSON resource yang **hanya** mengirim field aman PRD §6.4 (id, name, sector{name,slug,color}, city, province, lat, lng, year, status, summary). **Tidak ada** revenue/customer/published_by di response. Cache 5 menit (`Cache::remember 'public.projects'`).
- View `public/map.blade.php`:
  - Leaflet 1.9.4 + MarkerCluster 1.5.3 (CDN, sementara — nanti ganti Google Maps API).
  - Tile basemap: CartoDB Dark Matter (dark monochrome).
  - Default view: Kalimantan (`[-0.5, 114.0]`, zoom 6).
  - Marker bendera SVG pakai `L.divIcon` — solid (Selesai) / striped + waving CSS animation (Berjalan), warna sesuai sektor.
  - Popup detail aman (badge sektor, lokasi, tahun, status, summary).
  - Panel filter glassmorphic kiri-atas: sektor (multi), status (radio), provinsi (select), tahun from–to, reset.
  - Counter live: total tampil, breakdown per sektor, top 5 provinsi.
  - Empty state, mode presentasi (toggle filter panel), CTA "Diskusi project di wilayah Anda".

### 9. Admin Internal
- Layout dark-teal `layouts/admin.blade.php` (sidebar + header glass + logout).
- `Admin\ProjectController`:
  - `index` — paginate 15.
  - `create/store/edit/update` — termasuk lokasi primary (auto-create lewat relation).
  - `publish` — gated dengan `User::canPublishProjects()` (hanya Manajer Sales + Superadmin), set/clear `is_public`/`published_by`/`published_at`, invalidate cache.
- View `admin/projects/index.blade.php` — table dengan badge sektor + status + draft/published.
- View `admin/projects/form.blade.php` — form lengkap dengan **mini-map Leaflet draggable** untuk override koordinat. Dropdown city picker auto-isi city/province/lat/lng dari `city_references`. Override manual otomatis aktif kalau pin digeser atau lat/lng diedit.

### 10. Front-end Build
- `npm run build` sukses (Vite 8 + Tailwind 4 — sudah terpasang dari starter).

---

## 🧪 Smoke Test (sudah lewat)

```
GET  /                           200
GET  /login                      200
POST /login (NIP manajer)        302 → /admin/projects
GET  /admin (no auth)            302 → /login
GET  /admin/projects             200
GET  /admin/projects/create      200
GET  /admin/projects/{id}/edit   200
GET  /api/public/projects        200 (JSON, hanya field aman)
```

DB after seed:
```
sectors=3  cities=46  users=3  projects=20  published=18  locations=20
```

---

## 🔜 Berikutnya

### A. Polish UI Login (Phase 5 partial)
- Peta SVG di login masih bentuk acak. Pilih salah satu:
  - Pakai TopoJSON outline Indonesia + d3 untuk hasil presisi, atau
  - Background image SVG yang sudah dibuat designer.
- Logo lembaga (KEMENKES/IMIGRASI/POLDA) saat ini SVG placeholder — minta file logo resmi kalau ada.

### B. Migrasi Leaflet → Google Maps API (saat token tersedia)
Titik integrasi yang perlu diganti:
- `resources/views/public/map.blade.php` — ganti `L.tileLayer` + `L.marker` dengan `google.maps.Map` + `AdvancedMarkerElement`. Cluster: pakai `MarkerClusterer`.
- `resources/views/admin/projects/form.blade.php` — mini-map: ganti dengan `google.maps.Map` + draggable Marker.
- API publik `/api/public/projects` tidak perlu berubah (data shape sama).
- Marker bendera (solid/striped + waving) → bisa di-port jadi custom HTML marker via `AdvancedMarkerElement` + DOM yang sama.

### C. Phase 5 — Polish penuh (PRD §13)
- Branding (warna sektor sudah konsisten, logo perusahaan TBD).
- Mode presentasi (sekarang hanya toggle filter; bisa tambah fullscreen API & sembunyikan header).
- Aksesibilitas: navigasi marker via keyboard (Leaflet plugin atau custom), fallback table view (PRD §6.7).
- Multi-bahasa siap-siap (v2).

### D. Phase 6 — QA
- Test privasi: assert response `/api/public/projects` tidak mengandung `customer_name`, `published_by`, dll.
- Test gating: assert non-Manajer Sales tidak bisa POST `/admin/projects/{id}/publish` (403).
- Load test ringan.

### E. Out-of-scope tapi sering ditanya
- Master sectors/users CRUD UI — belum ada, sementara seed-only.
- Audit log perubahan project — belum ada.
- Notifikasi "project menunggu publikasi" untuk Manajer Sales — belum.

---

## 📁 Pointer File Kunci (terupdate)

```
~/projects/funneling-external/
├── compose.yaml
├── .env (DB_PORT=3308)
├── plans/
│   ├── CHECKPOINT.md                         # FILE INI
│   └── prd_sales_external_map.md             # PRD utama
├── app/
│   ├── Enums/{PublicStatus,UserRole}.php
│   ├── Models/{Sector,CityReference,Project,ProjectLocation,User}.php
│   └── Http/Controllers/
│       ├── Auth/LoginController.php
│       ├── PublicMapController.php
│       └── Admin/ProjectController.php
├── database/
│   ├── migrations/                           # 5 file termasuk add_nip_and_role
│   ├── factories/                            # 5 factory
│   └── seeders/                              # SectorSeeder, CityReferenceSeeder, UserSeeder, ProjectSeeder + DatabaseSeeder
├── resources/views/
│   ├── auth/login.blade.php                  # dark-teal, glassmorphic
│   ├── layouts/admin.blade.php               # shared admin shell
│   ├── public/map.blade.php                  # Leaflet + filter panel
│   └── admin/projects/{index,form}.blade.php
└── routes/web.php
```

---

## ▶️ Resume Instructions

1. `docker compose up -d` (MySQL).
2. `php artisan migrate:fresh --seed` (idempotent — seeders pakai `updateOrCreate`).
3. `npm install && npm run build` (atau `npm run dev` saat coding).
4. `php artisan serve`.
5. Login di `/login` dengan NIP `198505152010012002` / password `password` (Manajer Sales) untuk dapat akses publish.
6. Buka `/` untuk peta publik.

---

## 📌 Konteks Penting (tidak berubah)

- Modul **Sales External Map** (PRD-FNL-002). Audiens eksternal, tanpa login.
- Sektor inti v1: Imigrasi (biru), Polda (merah), Kemkes (hijau). Default zoom: Kalimantan.
- Marker bendera: solid = Selesai, striped (waving) = Berjalan.
- Input lokasi hybrid (auto-geocode dari `city_references` + override manual).
- Publikasi Opsi B (`is_public` toggle hanya Manajer Sales / Superadmin).
- Filter tahun: semua tahun (tanpa default 5 tahun).
- API publik HARUS hanya expose field PRD §6.4 — sudah ditegakkan di `PublicMapController@projects`.
