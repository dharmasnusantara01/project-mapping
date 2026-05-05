<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeoKarya — Peta Sebaran Instansi</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        html, body, #map { height: 100%; }

        .leaflet-container { background: #1a1f2e; }

        .panel {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(16px) saturate(140%);
            -webkit-backdrop-filter: blur(16px) saturate(140%);
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.12), 0 1px 4px rgba(0, 0, 0, 0.06);
            color: #0f172a;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #334155;
        }
        .input {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #0f172a;
        }
        .input::placeholder { color: #94a3b8; }
        .input:focus {
            outline: none;
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
        }
        .check {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: background .15s;
            font-size: 12.5px;
            color: #334155;
        }
        .check:hover { background: #f1f5f9; }
        .check input { accent-color: #0d9488; }
        .swatch {
            width: 11px; height: 11px; border-radius: 3px; flex-shrink: 0;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.1);
        }

        .flag-marker {
            width: 26px; height: 32px;
            transform: translate(-3px, -28px);
            filter: drop-shadow(0 3px 5px rgba(0,0,0,0.28));
        }
        .flag-marker .pole {
            position: absolute; left: 2px; top: 0;
            width: 2px; height: 30px;
            background: #334155; border-radius: 1px;
        }
        .flag-marker .cloth {
            position: absolute; left: 4px; top: 1px;
            width: 18px; height: 12px;
            border-radius: 0 2px 2px 0;
        }

        .leaflet-popup-content-wrapper {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 8px 30px -6px rgba(0,0,0,0.15);
            color: #0f172a;
            padding: 0;
        }
        .leaflet-popup-content { margin: 14px 16px; min-width: 240px; }
        .leaflet-popup-tip-container { display: none; }
        .leaflet-popup-close-button { color: #94a3b8 !important; font-size: 18px !important; top: 8px !important; right: 10px !important; }
        .leaflet-popup-close-button:hover { color: #334155 !important; }

        .marker-cluster-small,
        .marker-cluster-medium,
        .marker-cluster-large {
            background: rgba(13, 148, 136, 0.15) !important;
        }
        .marker-cluster-small div,
        .marker-cluster-medium div,
        .marker-cluster-large div {
            background: #0d9488 !important;
            color: #fff !important;
            font-weight: 700 !important;
            font-size: 12px !important;
        }
        .leaflet-control-zoom a {
            background: #fff !important;
            color: #334155 !important;
            border-color: #e2e8f0 !important;
        }
        .leaflet-control-zoom a:hover { background: #f1f5f9 !important; }
        .leaflet-control-attribution {
            background: rgba(255,255,255,0.75) !important;
            color: #94a3b8 !important;
            font-size: 10px !important;
        }

        .empty-state {
            position: absolute; inset: 0;
            display: none; align-items: center; justify-content: center;
            pointer-events: none;
        }
        .empty-state.show { display: flex; }

        .divider { border-color: #e2e8f0; }
    </style>
</head>
<body>

<div id="map" class="absolute inset-0"></div>

<div id="empty-state" class="empty-state">
    <div class="panel rounded-2xl px-6 py-5 text-center text-sm text-slate-500">
        Belum ada instansi yang cocok dengan filter ini.
    </div>
</div>

<header class="pointer-events-none absolute left-0 right-0 top-0 z-400 flex items-center justify-between p-4">
    <div class="panel pointer-events-auto flex items-center gap-3 rounded-2xl px-4 py-2.5">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-linear-to-br from-teal-400 to-emerald-500 text-xs font-bold text-white shadow-sm">
            GK
        </div>
        <div>
            <div class="text-sm font-semibold leading-tight text-slate-800">GeoKarya</div>
            <div class="text-[10px] uppercase tracking-wider text-slate-400">Peta Sebaran Instansi</div>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <button id="toggle-presentation"
                class="panel pointer-events-auto rounded-xl px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50">
            Mode Presentasi
        </button>
        <a href="{{ route('admin.instansi.index') }}"
           class="panel pointer-events-auto rounded-xl px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50">
            Masuk
        </a>
    </div>
</header>

<aside id="filter-panel"
       class="panel absolute left-4 top-18 z-400 w-72 rounded-2xl p-4 max-h-[calc(100vh-5.5rem)] overflow-y-auto">

    <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-slate-800">Filter Instansi</h2>
        <button id="reset-filter" class="text-xs text-teal-600 hover:text-teal-700 font-medium">Reset</button>
    </div>

    <div class="mt-3 rounded-xl bg-teal-50 border border-teal-100 p-3">
        <div class="text-[10px] uppercase tracking-wider text-teal-600 font-semibold">Instansi Tampil</div>
        <div class="mt-1 flex items-baseline gap-2">
            <span id="stat-count" class="text-3xl font-bold text-teal-700">0</span>
            <span id="stat-of"    class="text-xs text-slate-400">/ 0 total</span>
        </div>
        <div id="stat-by-sector" class="mt-2 flex flex-wrap gap-1.5"></div>
    </div>

    <div class="mt-4">
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Sektor</div>
        <div id="sector-filters" class="mt-1.5 space-y-0.5">
            @foreach ($sectors as $s)
                <label class="check">
                    <input type="checkbox" data-filter="sector" value="{{ $s->slug }}" checked>
                    <span class="swatch" style="background: {{ $s->color }};"></span>
                    <span class="flex-1">{{ $s->name }}</span>
                    <span class="text-[10px] text-slate-400" data-count-sector="{{ $s->slug }}">0</span>
                </label>
            @endforeach
        </div>
    </div>

    <hr class="divider my-3">

    <div>
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Witel</div>
        <select id="witel-filter" data-filter="witel" class="input mt-1.5 w-full rounded-lg px-3 py-2 text-xs">
            <option value="">Semua Witel</option>
        </select>
    </div>

    <div class="mt-3">
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Account Manager</div>
        <select id="am-filter" data-filter="am" class="input mt-1.5 w-full rounded-lg px-3 py-2 text-xs">
            <option value="">Semua AM</option>
        </select>
    </div>

    <hr class="divider my-3">

    <div>
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Top 5 Witel</div>
        <ol id="top-witel" class="mt-2 space-y-1 text-xs text-slate-600"></ol>
    </div>

    <hr class="divider my-3">

    <div>
        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Legenda</div>
        <div class="mt-2 space-y-2 text-xs text-slate-600">
            <div class="flex items-center gap-2">
                <div style="position:relative; width:24px; height:18px; flex-shrink:0;">
                    <div style="position:absolute;left:2px;top:0;width:2px;height:18px;background:#334155;border-radius:1px;"></div>
                    <div style="position:absolute;left:4px;top:1px;width:18px;height:12px;border-radius:0 2px 2px 0;background:#16a34a;"></div>
                </div>
                <span>Marker instansi (warna = sektor)</span>
            </div>
        </div>
    </div>
</aside>

<div class="pointer-events-none absolute bottom-5 right-5 z-400">
    <a href="mailto:sales@funneling.test"
       class="pointer-events-auto inline-flex items-center gap-2 rounded-xl bg-linear-to-br from-teal-500 to-emerald-600 px-4 py-2.5 text-xs font-semibold text-white shadow-lg hover:brightness-110 transition-all">
        Diskusi proyek di wilayah Anda →
    </a>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
    const SECTORS = @json($sectors->keyBy('slug'));
    const DATA_URL = @json(route('public.instansi'));

    const map = L.map('map', {
        zoomControl: true,
        attributionControl: true,
    }).setView([-0.5, 114.0], 6);

    L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
        attribution: '© <a href="https://stadiamaps.com">Stadia Maps</a> © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    const cluster = L.markerClusterGroup({ disableClusteringAtZoom: 9 });
    map.addLayer(cluster);

    const state = {
        all: [],
        sectors: new Set(Object.keys(SECTORS)),
        witel: '',
        am: '',
    };

    function flagIcon(color) {
        const html = `
            <div class="flag-marker" style="position:relative;">
                <div class="pole"></div>
                <div class="cloth" style="background:${color};"></div>
            </div>`;
        return L.divIcon({ html, className: '', iconSize: [26, 32], iconAnchor: [3, 28] });
    }

    function escapeHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
        }[c]));
    }

    function popupHtml(p) {
        const phone = p.telpon_instansi
            ? `<div style="margin-top:4px;font-size:11.5px;color:#475569;">Telp: <a href="tel:${encodeURIComponent(p.telpon_instansi)}" style="color:#0d9488;font-weight:600;">${escapeHtml(p.telpon_instansi)}</a></div>`
            : '';
        const alamat = p.alamat_instansi
            ? `<div style="margin-top:4px;font-size:11.5px;color:#64748b;line-height:1.45;">${escapeHtml(p.alamat_instansi)}</div>`
            : '';
        const summary = p.summary
            ? `<p style="margin-top:8px;font-size:11.5px;color:#475569;line-height:1.5;">${escapeHtml(p.summary)}</p>`
            : '';

        return `
            <div>
                <div style="display:flex;align-items:center;gap:7px;margin-bottom:6px;">
                    <span style="display:inline-block;width:9px;height:9px;border-radius:2px;background:${p.sector.color};flex-shrink:0;"></span>
                    <span style="font-size:10px;letter-spacing:.07em;text-transform:uppercase;color:${p.sector.color};font-weight:700;">${escapeHtml(p.sector.name)}</span>
                </div>
                <div style="font-size:14px;font-weight:700;color:#0f172a;line-height:1.3;">${escapeHtml(p.nama_instansi)}</div>
                ${alamat}
                ${phone}
                <div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:6px;">
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 9px;border-radius:9999px;font-size:10.5px;font-weight:600;background:#eff6ff;color:#1d4ed8;">Witel ${escapeHtml(p.witel.name)}</span>
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 9px;border-radius:9999px;font-size:10.5px;font-weight:600;background:#f0fdf4;color:#15803d;">AM ${escapeHtml(p.account_manager.name)}</span>
                </div>
                ${summary}
            </div>`;
    }

    function applyFilters() {
        cluster.clearLayers();
        const filtered = state.all.filter(p => {
            if (!state.sectors.has(p.sector.slug)) return false;
            if (state.witel && String(p.witel.id) !== state.witel) return false;
            if (state.am && String(p.account_manager.id) !== state.am) return false;
            return true;
        });

        const markers = filtered.map(p => {
            const m = L.marker([p.latitude, p.longitude], {
                icon: flagIcon(p.sector.color),
                title: p.nama_instansi,
            });
            m.bindPopup(popupHtml(p), { maxWidth: 300 });
            return m;
        });
        cluster.addLayers(markers);

        updateStats(filtered);
        document.getElementById('empty-state').classList.toggle('show', filtered.length === 0);
    }

    function updateStats(filtered) {
        document.getElementById('stat-count').textContent = filtered.length;
        document.getElementById('stat-of').textContent = `/ ${state.all.length} total`;

        const bySector = {};
        for (const slug of Object.keys(SECTORS)) bySector[slug] = 0;
        for (const p of filtered) bySector[p.sector.slug] = (bySector[p.sector.slug] || 0) + 1;

        const wrap = document.getElementById('stat-by-sector');
        wrap.innerHTML = '';
        for (const [slug, sec] of Object.entries(SECTORS)) {
            const span = document.createElement('span');
            span.className = 'chip';
            span.innerHTML = `<span class="swatch" style="background:${sec.color};"></span>${escapeHtml(sec.name)} <strong>${bySector[slug] || 0}</strong>`;
            wrap.appendChild(span);
        }

        for (const slug of Object.keys(SECTORS)) {
            const el = document.querySelector(`[data-count-sector="${slug}"]`);
            if (el) el.textContent = bySector[slug] || 0;
        }

        const byWitel = {};
        for (const p of filtered) {
            const k = p.witel.name;
            byWitel[k] = (byWitel[k] || 0) + 1;
        }
        const top = Object.entries(byWitel).sort((a, b) => b[1] - a[1]).slice(0, 5);
        const ol = document.getElementById('top-witel');
        ol.innerHTML = '';
        if (top.length === 0) {
            ol.innerHTML = '<li class="text-slate-400">—</li>';
        } else {
            top.forEach(([name, n]) => {
                const li = document.createElement('li');
                li.className = 'flex items-center justify-between gap-2';
                li.innerHTML = `<span class="truncate text-slate-700">${escapeHtml(name)}</span><span class="font-semibold text-teal-600">${n}</span>`;
                ol.appendChild(li);
            });
        }
    }

    function buildOptions() {
        const witelMap = new Map();
        const amMap = new Map();
        for (const p of state.all) {
            witelMap.set(p.witel.id, p.witel.name);
            amMap.set(p.account_manager.id, p.account_manager.name);
        }
        const witelSel = document.getElementById('witel-filter');
        [...witelMap.entries()].sort((a, b) => a[1].localeCompare(b[1])).forEach(([id, name]) => {
            const o = document.createElement('option');
            o.value = String(id); o.textContent = name;
            witelSel.appendChild(o);
        });
        const amSel = document.getElementById('am-filter');
        [...amMap.entries()].sort((a, b) => a[1].localeCompare(b[1])).forEach(([id, name]) => {
            const o = document.createElement('option');
            o.value = String(id); o.textContent = name;
            amSel.appendChild(o);
        });
    }

    function bindFilters() {
        document.querySelectorAll('[data-filter="sector"]').forEach(cb => {
            cb.addEventListener('change', () => {
                cb.checked ? state.sectors.add(cb.value) : state.sectors.delete(cb.value);
                applyFilters();
            });
        });
        document.getElementById('witel-filter').addEventListener('change', e => {
            state.witel = e.target.value; applyFilters();
        });
        document.getElementById('am-filter').addEventListener('change', e => {
            state.am = e.target.value; applyFilters();
        });
        document.getElementById('reset-filter').addEventListener('click', () => {
            document.querySelectorAll('[data-filter="sector"]').forEach(cb => { cb.checked = true; state.sectors.add(cb.value); });
            document.getElementById('witel-filter').value = ''; state.witel = '';
            document.getElementById('am-filter').value = '';    state.am = '';
            applyFilters();
        });
        document.getElementById('toggle-presentation').addEventListener('click', () => {
            document.getElementById('filter-panel').classList.toggle('hidden');
        });
    }

    fetch(DATA_URL)
        .then(r => r.json())
        .then(data => {
            state.all = data;
            buildOptions();
            bindFilters();
            applyFilters();
        })
        .catch(err => {
            console.error(err);
            document.getElementById('empty-state').classList.add('show');
        });
</script>
</body>
</html>
