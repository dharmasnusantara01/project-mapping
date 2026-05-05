@extends('layouts.admin')

@section('title', 'Dashboard')

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
@endpush

@section('content')
    @php
        $publishedRatio = $kpi['instansi'] > 0 ? round($kpi['published'] / $kpi['instansi'] * 100) : 0;
    @endphp

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-6">
        <div class="glass rounded-xl p-4">
            <div class="text-[10px] uppercase tracking-wider text-teal-200/70">Total Instansi</div>
            <div class="mt-1 text-2xl font-bold text-white">{{ $kpi['instansi'] }}</div>
        </div>
        <div class="glass rounded-xl p-4">
            <div class="text-[10px] uppercase tracking-wider text-teal-200/70">Published</div>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-emerald-300">{{ $kpi['published'] }}</span>
                <span class="text-xs text-teal-200/60">{{ $publishedRatio }}%</span>
            </div>
        </div>
        <div class="glass rounded-xl p-4">
            <div class="text-[10px] uppercase tracking-wider text-teal-200/70">Draft</div>
            <div class="mt-1 text-2xl font-bold text-amber-200">{{ $kpi['draft'] }}</div>
        </div>
        <div class="glass rounded-xl p-4">
            <div class="text-[10px] uppercase tracking-wider text-teal-200/70">Witel</div>
            <div class="mt-1 text-2xl font-bold text-white">{{ $kpi['witel'] }}</div>
        </div>
        <div class="glass rounded-xl p-4">
            <div class="text-[10px] uppercase tracking-wider text-teal-200/70">Account Manager</div>
            <div class="mt-1 text-2xl font-bold text-white">{{ $kpi['am'] }}</div>
        </div>
        <div class="glass rounded-xl p-4">
            <div class="text-[10px] uppercase tracking-wider text-teal-200/70">Sektor</div>
            <div class="mt-1 text-2xl font-bold text-white">{{ $kpi['sector'] }}</div>
        </div>
    </div>

    {{-- Row: Sektor donut + Mini map --}}
    <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="glass rounded-2xl p-5">
            <h3 class="text-sm font-semibold text-white">Distribusi Sektor</h3>
            <p class="text-[11px] text-teal-200/60">Berdasarkan jumlah instansi</p>
            <div class="mt-3" style="height: 220px;">
                <canvas id="chart-sector"></canvas>
            </div>
            <div class="mt-3 space-y-1.5">
                @foreach ($bySector as $row)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 text-teal-100/85">
                            <span class="h-2 w-2 rounded-sm" style="background: {{ $row['color'] }};"></span>
                            {{ $row['name'] }}
                        </div>
                        <span class="font-semibold text-white">{{ $row['total'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="glass rounded-2xl p-5 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-white">Sebaran Instansi (Published)</h3>
                    <p class="text-[11px] text-teal-200/60">Marker berwarna sesuai sektor</p>
                </div>
                <a href="{{ route('public.map') }}" target="_blank"
                   class="btn-ghost rounded-lg px-3 py-1.5 text-xs font-medium">Buka Peta Publik →</a>
            </div>
            <div id="mini-map" class="mt-3 h-72 w-full overflow-hidden rounded-xl border border-teal-400/15"></div>
        </div>
    </div>

    {{-- Row: per Witel + per AM --}}
    <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
        <div class="glass rounded-2xl p-5">
            <h3 class="text-sm font-semibold text-white">Instansi per Witel</h3>
            <p class="text-[11px] text-teal-200/60">Beban kerja per wilayah</p>
            <div class="mt-3" style="height: 280px;">
                <canvas id="chart-witel"></canvas>
            </div>
        </div>

        <div class="glass rounded-2xl p-5">
            <h3 class="text-sm font-semibold text-white">Top Account Manager</h3>
            <p class="text-[11px] text-teal-200/60">Diurutkan dari jumlah instansi terbanyak</p>
            <div class="mt-3" style="height: 280px;">
                <canvas id="chart-am"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Instansi --}}
    <div class="glass mt-5 overflow-hidden rounded-2xl">
        <div class="flex items-center justify-between border-b border-white/5 px-5 py-3">
            <h3 class="text-sm font-semibold text-white">Instansi Terbaru</h3>
            <a href="{{ route('admin.instansi.index') }}" class="text-xs font-medium text-teal-300 hover:text-teal-200">Lihat semua →</a>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-white/5 text-[11px] uppercase tracking-wider text-teal-200/70">
                <tr>
                    <th class="px-5 py-2.5">Nama Instansi</th>
                    <th class="px-5 py-2.5">Witel</th>
                    <th class="px-5 py-2.5">AM</th>
                    <th class="px-5 py-2.5">Sektor</th>
                    <th class="px-5 py-2.5">Status</th>
                    <th class="px-5 py-2.5">Dibuat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse ($recent as $i)
                    <tr class="hover:bg-white/5">
                        <td class="px-5 py-2.5">
                            <a href="{{ route('admin.instansi.edit', $i) }}" class="font-medium text-white hover:text-teal-200">
                                {{ $i->nama_instansi }}
                            </a>
                        </td>
                        <td class="px-5 py-2.5 text-teal-100/85">{{ $i->witel?->name ?? '—' }}</td>
                        <td class="px-5 py-2.5 text-teal-100/85">{{ $i->accountManager?->name ?? '—' }}</td>
                        <td class="px-5 py-2.5">
                            <span class="badge-soft" style="color: {{ $i->sector?->color }};">
                                <span class="h-1.5 w-1.5 rounded-full" style="background: {{ $i->sector?->color }};"></span>
                                {{ $i->sector?->name }}
                            </span>
                        </td>
                        <td class="px-5 py-2.5">
                            @if ($i->is_public)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-2 py-0.5 text-[10.5px] font-semibold text-emerald-200">Published</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-500/20 px-2 py-0.5 text-[10.5px] font-semibold text-slate-200">Draft</span>
                            @endif
                        </td>
                        <td class="px-5 py-2.5 text-[11px] text-teal-200/60">{{ $i->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-sm text-teal-200/60">
                            Belum ada instansi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @php
        $dashData = [
            'byWitel'  => $byWitel,
            'byAm'     => $byAccountManager,
            'bySector' => $bySector,
            'points'   => $mapPoints,
        ];
    @endphp
    <script id="dashboard-data" type="application/json">{!! json_encode($dashData) !!}</script>
@endsection

@push('scripts')
    <script>
        const DASH = JSON.parse(document.getElementById('dashboard-data').textContent);

        Chart.defaults.color = 'rgba(167, 243, 208, 0.75)';
        Chart.defaults.borderColor = 'rgba(52, 211, 153, 0.12)';
        Chart.defaults.font.family = 'Sora, ui-sans-serif, system-ui, sans-serif';

        // ── Sektor donut ──────────────────────────────────────────
        new Chart(document.getElementById('chart-sector'), {
            type: 'doughnut',
            data: {
                labels: DASH.bySector.map(r => r.name),
                datasets: [{
                    data: DASH.bySector.map(r => r.total),
                    backgroundColor: DASH.bySector.map(r => r.color),
                    borderColor: 'rgba(2, 12, 6, 0.6)',
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: { legend: { display: false } },
            },
        });

        // ── Per Witel horizontal bar ──────────────────────────────
        new Chart(document.getElementById('chart-witel'), {
            type: 'bar',
            data: {
                labels: DASH.byWitel.map(r => r.name),
                datasets: [{
                    data: DASH.byWitel.map(r => r.total),
                    backgroundColor: 'rgba(52, 211, 153, 0.55)',
                    borderColor: '#34d399',
                    borderWidth: 1,
                    borderRadius: 4,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: 'rgba(52, 211, 153, 0.08)' }, ticks: { precision: 0 } },
                    y: { grid: { display: false } },
                },
            },
        });

        // ── Top AM horizontal bar ────────────────────────────────
        new Chart(document.getElementById('chart-am'), {
            type: 'bar',
            data: {
                labels: DASH.byAm.map(r => r.name),
                datasets: [{
                    data: DASH.byAm.map(r => r.total),
                    backgroundColor: 'rgba(125, 211, 252, 0.55)',
                    borderColor: '#7dd3fc',
                    borderWidth: 1,
                    borderRadius: 4,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: 'rgba(125, 211, 252, 0.08)' }, ticks: { precision: 0 } },
                    y: { grid: { display: false } },
                },
            },
        });

        // ── Mini map ──────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            const map = L.map('mini-map', {
                zoomControl: true,
                attributionControl: false,
                scrollWheelZoom: false,
            }).setView([-2.0, 117.0], 4);

            L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
                maxZoom: 20,
            }).addTo(map);

            DASH.points.forEach(p => {
                L.circleMarker([p.lat, p.lng], {
                    radius: 5,
                    fillColor: p.color,
                    color: '#0f172a',
                    weight: 1,
                    fillOpacity: 0.9,
                }).bindTooltip(p.name, { direction: 'top' }).addTo(map);
            });
        });
    </script>
@endpush
