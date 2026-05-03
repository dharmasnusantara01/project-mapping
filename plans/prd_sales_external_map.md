# 📄 Product Requirement Document (PRD)
## Funneling DSN — Sales External Map

| Item            | Detail                                                  |
|-----------------|---------------------------------------------------------|
| **Document**    | PRD-FNL-002                                             |
| **Version**     | 1.1                                                     |
| **Date**        | 2026-04-30                                              |
| **Owner**       | Product Team — Funneling DSN                            |
| **Status**      | Approved for build                                      |
| **Audience**    | External (prospek, mitra, stakeholder publik)           |

---

## 1. Executive Summary
Funneling DSN — Sales External Map adalah **portofolio interaktif berbasis peta Indonesia** yang dipakai tim Sales untuk memperlihatkan jejak project perusahaan kepada **calon pelanggan dan mitra eksternal**. Setiap project ditandai sebagai *bendera tancap* di lokasi geografisnya, dikelompokkan berdasarkan sektor strategis: **Imigrasi, Polda, dan Kemkes**.

Tujuan utamanya adalah memperkuat *trust* prospek dengan bukti coverage geografis dan sektor pemerintahan yang sudah dilayani — tanpa menampilkan data sensitif internal (revenue, kontrak, PIC).

---

## 2. Problem Statement
1. Saat presentasi ke calon pelanggan, Sales kesulitan menampilkan **gambaran portofolio** secara visual dan ringkas.
2. **Bukti track record** (project apa, di mana, untuk sektor apa) saat ini hanya berupa daftar di slide statis.
3. Tidak ada cara mudah untuk **memperlihatkan coverage regional** (mis. seberapa kuat di Kalimantan, di Sumatera, di sektor Imigrasi) saat pitch atau due-diligence.
4. Materi presentasi cepat kadaluwarsa karena update manual setiap kali ada project baru.

---

## 3. Goals & Non-Goals

### 3.1 Goals
- G1. Menyediakan **peta Indonesia interaktif** dengan marker project (default zoom: Kalimantan).
- G2. Mengelompokkan project berdasar **sektor strategis**: Imigrasi, Polda, Kemkes (extendable).
- G3. Menampilkan **detail aman untuk konsumsi eksternal** (nama project, lokasi, tahun, sektor) — tanpa data sensitif.
- G4. Menyediakan **filter sektor & wilayah** sehingga Sales bisa men-tailor narasi per prospek.
- G5. **Auto-update** — peta langsung memantulkan project baru yang sudah disetujui untuk publik.

### 3.2 Non-Goals (Out of Scope v1)
- Menampilkan revenue, profit, kontrak, PIC, atau data finansial.
- Manajemen project (CRUD project ada di modul Sales internal, terpisah).
- Login eksternal — peta diakses publik tanpa user account.
- **Shareable link / link generator** — diakses lewat URL umum saja, tanpa state-filter sharing.
- Multi-bahasa (ID + EN) — direncanakan di **v2**.
- Routing / direksi geografis.
- Heatmap real-time / streaming.

---

## 4. Personas

| Persona         | Konteks                                                    | Kebutuhan                                          |
|-----------------|-----------------------------------------------------------|----------------------------------------------------|
| Sales Rep       | Pitch / proposal ke calon pelanggan                       | Tampilkan portofolio interaktif sebagai bukti      |
| Manajer Sales   | Mengelola publikasi project ke peta eksternal             | Persetujuan toggle `is_public` per project         |
| Prospek / Mitra | Mengevaluasi kapabilitas perusahaan                       | Lihat sebaran project di wilayah & sektor relevan  |
| Stakeholder Pub.| Browsing publik (referensi umum)                          | Gambaran umum jangkauan perusahaan                 |

---

## 5. User Stories

- **US-1** Sebagai Sales, saya ingin membuka peta Indonesia dengan zoom default ke Kalimantan supaya saat pitch ke prospek di sana langsung terlihat coverage kami.
- **US-2** Sebagai Sales, saya ingin men-filter peta berdasar sektor (Imigrasi / Polda / Kemkes) supaya bisa fokus ke sektor yang relevan dengan prospek.
- **US-3** Sebagai Sales, saya ingin meng-klik bendera project untuk menampilkan detail singkat (nama project, kota, tahun, sektor, status).
- **US-4** Sebagai Prospek, saya ingin melihat berapa banyak project yang sudah dikerjakan di provinsi/kota saya.
- **US-5** Sebagai Prospek, saya ingin melihat ringkasan total per sektor (mis. "Imigrasi: 12 project di 8 provinsi").
- **US-6** Sebagai Sales, saya ingin mengisi lokasi project cukup dengan **nama kota & provinsi**, dan sistem otomatis menebak koordinatnya — tetapi saya bisa **override manual** kalau butuh titik spesifik.
- **US-7** Sebagai Manajer Sales, saya ingin **mengontrol publikasi** project ke peta eksternal — Sales rep biasa tidak boleh menerbitkan sendiri.

---

## 6. Functional Requirements

### 6.1 Peta Indonesia
- Peta interaktif (zoom, pan, klik) menampilkan seluruh wilayah Indonesia.
- **Default view**: zoom ke Kalimantan (Kalbar, Kalteng, Kalsel, Kaltim, Kaltara).
- Tile peta minimalis (style monochrome / muted) agar marker menonjol.
- Kontrol: zoom in/out, reset view, fullscreen.

### 6.2 Marker "Bendera Project"
- Setiap project ditampilkan sebagai marker bendera di koordinat lokasi.
- **Warna bendera per sektor**:
  - 🟦 Imigrasi — biru
  - 🟥 Polda — merah
  - 🟩 Kemkes — hijau
  - ⚪ Sektor lain — abu-abu (bila ditambah ke depan)
- **Pembedaan visual status**:
  - **Selesai** → bendera **solid** (warna penuh).
  - **Berjalan** → bendera **bergaris-garis (striped)** dengan animasi halus *waving*.
  - Legenda visual ditampilkan di panel filter.
- **Cluster** otomatis saat zoom-out (mis. "12" di tengah pulau, pecah saat di-zoom).
- Hover marker → tooltip ringkas (nama project + kota).
- Klik marker → popup detail (lihat 6.4).

### 6.3 Panel Filter & Statistik
Panel mengambang (kiri / atas) berisi:
- **Filter sektor** (multi-select): Imigrasi, Polda, Kemkes.
- **Filter wilayah** (provinsi): default semua, opsional pilih satu/lebih.
- **Filter status**: Semua / Selesai / Berjalan.
- **Filter tahun**: range (tahun mulai — tahun akhir). **Default menampilkan semua tahun** sejak project pertama tercatat (tanpa batas atas/bawah). User bebas mempersempit.
- **Counter ringkasan**:
  - Total project tampil
  - Pecahan per sektor
  - Pecahan per provinsi (top 5)

### 6.4 Popup Detail Project (External-safe)
Field yang ditampilkan:
- Nama Project
- Sektor (badge berwarna)
- Lokasi (Kota, Provinsi)
- Tahun pelaksanaan
- Status: `Selesai` / `Berjalan`
- Deskripsi singkat (1–2 kalimat, opsional, sudah disetujui untuk publik)

> Field yang **TIDAK** ditampilkan: revenue, profit, nama PIC, nomor kontak, dokumen kontrak, BAST/BAUT, invoice.

### 6.5 Input Lokasi (Hybrid Geocoding)
- Sales mengisi project dengan **Kota** + **Provinsi** sebagai input wajib.
- Sistem otomatis melakukan **lookup koordinat** dari tabel referensi kota/kabupaten Indonesia (geocoding internal).
- Jika hasil lookup tidak ditemukan atau tidak akurat, Sales bisa **override manual** dengan klik titik di peta atau mengisi `latitude` & `longitude` langsung.
- Field hasil:
  - `geocoded_at` — timestamp lookup terakhir.
  - `is_manual_override` — boolean, true bila Sales mengubah koordinat manual.
- Validasi: lat/lng harus dalam bounding box wilayah Indonesia.

### 6.6 Publikasi (Approval Ringan)
- Project tidak otomatis muncul di peta eksternal saat dibuat.
- Field `is_public` (boolean) menentukan tampil/tidak.
- **Hanya role Manajer Sales / Superadmin** yang bisa men-toggle `is_public` ke `true`.
- Sales rep biasa hanya bisa mengisi `public_summary` (draft); penerbitan dilakukan atasannya.
- UI: badge `Draft` / `Published` di list project; tombol "Publikasikan ke peta" hanya muncul untuk role berwenang.
- Audit: pencatatan siapa & kapan men-publish (`published_by`, `published_at`).

### 6.7 Empty & Error State
- Bila tidak ada project setelah filter → tampilkan ilustrasi "Belum ada project di kombinasi filter ini".
- Bila gagal load peta → fallback list project dalam tabel sederhana.

---

## 7. Data Model (High Level)

> Detail kolom ada di lampiran A.

- **sectors** — master sektor strategis (Imigrasi, Polda, Kemkes, …) dengan warna.
- **project_locations** — koordinat & metadata lokasi per project (kota, provinsi, lat/lng, override flag).
- **projects** — ditambah relasi ke `sector_id`, field `year`, `public_summary`, `is_public`, `public_status`, `published_by`, `published_at`.
- **city_references** — tabel referensi kota/kabupaten Indonesia + koordinat default (untuk auto-geocoding).
- **public_project_view** — view atau resource yang hanya mengekspos field aman untuk publik (lihat 6.4).

---

## 8. Non-Functional Requirements

| Kategori        | Requirement                                                                 |
|-----------------|------------------------------------------------------------------------------|
| Privasi         | Hanya field yang disetujui publik yang dikirim ke client. Data internal (revenue, PIC, dll.) **tidak boleh** ada di response API publik. |
| Otorisasi       | Toggle `is_public` hanya untuk role Manajer Sales / Superadmin.              |
| Performance     | Initial load peta < 2 detik (≤ 500 marker tampil sekaligus berkat clustering). |
| Caching         | Data project & lokasi di-cache (server side ≥ 5 menit) karena update jarang. |
| Browser support | Chrome, Edge, Safari, Firefox modern (desktop & tablet).                     |
| Aksesibilitas   | Marker dapat dinavigasi dengan keyboard; alternatif tampilan tabel tersedia. |
| Bahasa          | Bahasa Indonesia (UI). Multi-bahasa direncanakan di v2.                      |
| Branding        | Tampilan peta merefleksikan brand (warna, logo) untuk kebutuhan eksternal.   |

---

## 9. UX & UI Notes
- **Layout**: peta full-screen + panel filter mengambang (collapsible di mobile).
- **Bendera**:
  - Solid → status `Selesai`.
  - Striped + animasi waving halus → status `Berjalan`.
  - Warna sesuai sektor; ada efek "drop" saat marker dimuat.
- **Popup**: card minimalis dengan badge sektor, lokasi, tahun, status.
- **Empty state**: ilustrasi peta kosong + CTA "Hubungi Sales kami".
- **CTA eksternal**: tombol "Diskusi project di wilayah Anda" → mengarah ke kontak Sales.
- **Mode presentasi**: opsi sembunyikan panel filter agar peta full untuk slide demo.
- **Form input lokasi (internal)**: dropdown kota → preview marker di mini-map, dengan tombol "Geser titik" untuk override manual.

---

## 10. Acceptance Criteria

### AC-1 Peta & Default View
- Saat halaman dibuka, peta langsung zoom ke Kalimantan.
- Marker project Kalimantan terlihat tanpa scroll/zoom tambahan.

### AC-2 Filter Sektor & Status
- Pilih "Imigrasi" → hanya marker biru tampil.
- Pilih kombinasi multi-sektor → marker sesuai kombinasi.
- Pilih "Berjalan" → hanya bendera striped tampil.

### AC-3 Marker Detail
- Klik marker → popup tampil dengan field aman (tidak ada revenue/PIC).
- Cluster pecah saat zoom-in mendekat ke kota.

### AC-4 Statistik
- Counter di panel filter selalu konsisten dengan marker yang tampil.
- Top-5 provinsi terupdate saat filter berubah.

### AC-5 Geocoding
- Memilih kota dari dropdown → koordinat default terisi otomatis.
- Override manual menggeser titik & set `is_manual_override = true`.
- Validasi menolak lat/lng di luar bounding box Indonesia.

### AC-6 Publikasi
- Sales rep tidak melihat tombol "Publikasikan".
- Manajer Sales / Superadmin bisa toggle `is_public`; project muncul di peta segera setelah cache invalidate.
- Setiap publikasi tercatat (`published_by`, `published_at`).

### AC-7 Privasi
- Inspect network → response API publik tidak mengandung field sensitif.

---

## 11. Confirmed Decisions

| # | Topik                       | Keputusan                                                              |
|---|-----------------------------|------------------------------------------------------------------------|
| 1 | Audiens utama               | **External** (prospek, mitra)                                          |
| 2 | Role internal pemakai utama | **Sales** (untuk pitch); publikasi dikontrol Manajer Sales            |
| 3 | Default fokus geografis     | **Kalimantan**, peta tetap mencakup seluruh Indonesia                  |
| 4 | Sektor inti v1              | **Imigrasi, Polda, Kemkes** (extendable via master `sectors`)          |
| 5 | Akses                       | **Public** — tanpa login eksternal, tanpa shareable link generator     |
| 6 | Data sensitif               | **Tidak ditampilkan** di view eksternal                                |
| 7 | Input koordinat             | **Hybrid** — auto-geocode dari kota + opsi override manual             |
| 8 | Pembedaan status visual     | **Ya** — Selesai = solid, Berjalan = striped (waving)                  |
| 9 | Multi-bahasa                | **v2** (v1 hanya Bahasa Indonesia)                                     |
| 10| Approval publikasi          | **Opsi B (ringan)** — toggle `is_public` hanya untuk Manajer Sales / Superadmin |
| 11| Range tahun di filter       | **Semua tahun** sejak project pertama (tanpa default 5 tahun terakhir) |

---

## 12. Risks & Mitigations

| Risiko                                                            | Mitigasi                                              |
|-------------------------------------------------------------------|-------------------------------------------------------|
| Bocor data sensitif lewat API publik.                             | Pakai resource/view khusus public; tes inspect network di QA. |
| Koordinat hasil auto-geocoding tidak akurat.                      | Sediakan override manual + indikator `is_manual_override`. |
| Manajer Sales lupa publikasi project baru.                        | Notifikasi internal "project menunggu publikasi" di dashboard. |
| Tampilan peta lambat di koneksi lemah.                            | Tile minimalis + clustering + cache agresif.          |
| Sektor selain Imigrasi/Polda/Kemkes muncul ke depan.              | Master `sectors` extendable; warna baru otomatis dialokasikan. |
| Kesalahan tampil project yang seharusnya draft.                   | Filter `is_public = true` di query publik, plus test otomatis. |

---

## 13. Roadmap (Indikatif)

| Phase | Lingkup                                                                | Estimasi |
|------:|------------------------------------------------------------------------|----------|
| 1     | Master `sectors`, `city_references`, relasi project + UI input lokasi (hybrid geocoding) | 3 hari |
| 2     | Peta Indonesia + marker bendera (solid/striped) + clustering            | 3 hari   |
| 3     | Panel filter (sektor, wilayah, status, tahun) + statistik               | 2 hari   |
| 4     | Popup detail aman + flow publikasi (toggle `is_public` + audit)         | 2 hari   |
| 5     | Polish (branding, mode presentasi, CTA, aksesibilitas)                  | 2 hari   |
| 6     | QA privasi + load test                                                  | 1 hari   |

---

## 14. Appendix A — Detail Skema Database

### A.1 `sectors`
| Kolom      | Tipe         | Keterangan                  |
|------------|--------------|-----------------------------|
| id         | bigInt PK    |                             |
| name       | string       | Imigrasi / Polda / Kemkes   |
| slug       | string unique|                             |
| color      | string       | hex untuk warna bendera     |
| order      | unsignedTinyInt | urut tampilan di filter   |
| timestamps |              |                             |

Seed awal:
| order | name      | slug      | color    |
|------:|-----------|-----------|----------|
| 1     | Imigrasi  | imigrasi  | #1d4ed8  |
| 2     | Polda     | polda     | #dc2626  |
| 3     | Kemkes    | kemkes    | #16a34a  |

### A.2 `city_references`
Master kota/kabupaten Indonesia untuk auto-geocoding.

| Kolom      | Tipe                |
|------------|---------------------|
| id         | bigInt PK           |
| name       | string              |
| province   | string              |
| latitude   | decimal(10,7)       |
| longitude  | decimal(10,7)       |
| timestamps |                     |

Index: `(province, name)`.

### A.3 `project_locations`
| Kolom               | Tipe                                | Keterangan                            |
|---------------------|-------------------------------------|---------------------------------------|
| id                  | bigInt PK                           |                                       |
| project_id          | FK projects.id cascadeOnDelete      |                                       |
| city                | string                              | Kota / Kabupaten                      |
| province            | string                              | Provinsi                              |
| latitude            | decimal(10,7)                       | hasil geocode atau override           |
| longitude           | decimal(10,7)                       |                                       |
| is_manual_override  | boolean default false               | true bila Sales menggeser titik manual|
| geocoded_at         | timestamp nullable                  | timestamp lookup terakhir             |
| is_primary          | boolean default true                | jika project di banyak lokasi         |
| timestamps          |                                     |                                       |

Index: `(project_id)`, `(province)`.

### A.4 `projects` (penambahan untuk modul ini)
| Kolom               | Tipe                          | Keterangan                                     |
|---------------------|-------------------------------|------------------------------------------------|
| sector_id           | FK sectors.id                 | sektor strategis project                       |
| year                | unsignedSmallInt              | tahun pelaksanaan (atau pakai `won_at`)        |
| public_summary      | string nullable               | deskripsi singkat aman untuk publik (draft)    |
| is_public           | boolean default false         | flag tampil di peta eksternal                  |
| public_status       | enum(`berjalan`,`selesai`)    | status sederhana untuk eksternal               |
| published_by        | FK users.id nullable          | siapa yang men-publish                         |
| published_at        | timestamp nullable            | kapan dipublish                                |

### A.5 Public API Resource
Hanya field berikut yang dikirim ke client publik:
```
{
  "id": "...",
  "name": "...",
  "sector": { "name": "Imigrasi", "color": "#1d4ed8" },
  "city": "Samarinda",
  "province": "Kalimantan Timur",
  "latitude": 0.5023,
  "longitude": 117.1536,
  "year": 2024,
  "status": "selesai",
  "summary": "..."
}
```

---

## 15. Approval

| Nama / Role        | Tanda Tangan | Tanggal     |
|--------------------|--------------|-------------|
| Product Owner      |              |             |
| Manajer Sales      |              |             |
| Marketing / Brand  |              |             |
| Compliance / Legal |              |             |
