@extends('layouts.admin')

@section('title', $project->exists ? 'Edit Project' : 'Project Baru')

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush

@section('content')
    @php
        $loc = $project->primaryLocation;
        $defaultLat = old('location.latitude', $loc?->latitude  ?? -0.5);
        $defaultLng = old('location.longitude', $loc?->longitude ?? 114.0);
    @endphp

    <form method="POST" action="{{ $project->exists ? route('admin.projects.update', $project) : route('admin.projects.store') }}"
          class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        @csrf
        @if ($project->exists) @method('PUT') @endif

        {{-- Left: project info --}}
        <div class="glass space-y-4 rounded-2xl p-5 lg:col-span-2">
            <h3 class="text-sm font-semibold text-white">Informasi Project</h3>

            <div>
                <label class="text-xs font-medium text-teal-200/80">Nama Project</label>
                <input name="name" value="{{ old('name', $project->name) }}" required
                       class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                @error('name') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-xs font-medium text-teal-200/80">Customer</label>
                    <input name="customer_name" value="{{ old('customer_name', $project->customer_name) }}" required
                           class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                    @error('customer_name') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-teal-200/80">Sektor</label>
                    <select name="sector_id" required class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @foreach ($sectors as $s)
                            <option value="{{ $s->id }}" @selected(old('sector_id', $project->sector_id) == $s->id)>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('sector_id') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-xs font-medium text-teal-200/80">Tahun Pelaksanaan</label>
                    <input name="year" type="number" min="2000" max="{{ date('Y') + 1 }}"
                           value="{{ old('year', $project->year ?? date('Y')) }}" required
                           class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                    @error('year') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-teal-200/80">Status Publik</label>
                    <select name="public_status" required class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @foreach ($statuses as $val => $label)
                            <option value="{{ $val }}"
                                @selected(old('public_status', $project->public_status?->value ?? 'berjalan') === $val)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-xs font-medium text-teal-200/80">Ringkasan Publik (max 500 karakter)</label>
                <textarea name="public_summary" rows="3" maxlength="500"
                          class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">{{ old('public_summary', $project->public_summary) }}</textarea>
                <p class="mt-1 text-[11px] text-teal-200/60">Hanya tampil di peta publik. Hindari data sensitif.</p>
                @error('public_summary') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Right: publish + actions --}}
        <div class="space-y-4">
            <div class="glass rounded-2xl p-5">
                <h3 class="text-sm font-semibold text-white">Publikasi</h3>

                @if ($project->exists)
                    <div class="mt-3 text-xs text-teal-100/80">
                        Status saat ini:
                        @if ($project->is_public)
                            <span class="ml-1 inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-2.5 py-1 font-semibold text-emerald-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span> Published
                            </span>
                            @if ($project->publisher)
                                <p class="mt-2 text-[11px] text-teal-200/60">
                                    Oleh {{ $project->publisher->name }} · {{ $project->published_at?->format('d M Y H:i') }}
                                </p>
                            @endif
                        @else
                            <span class="ml-1 inline-flex items-center gap-1.5 rounded-full bg-slate-500/20 px-2.5 py-1 font-semibold text-slate-200">Draft</span>
                        @endif
                    </div>
                @else
                    <p class="mt-2 text-[11px] text-teal-200/60">Project baru tersimpan sebagai Draft. Publikasi tersedia setelah disimpan.</p>
                @endif

                @if ($project->exists && auth()->user()->canPublishProjects())
                    <form method="POST" action="{{ route('admin.projects.publish', $project) }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="publish" value="{{ $project->is_public ? 0 : 1 }}">
                        <button class="{{ $project->is_public ? 'btn-ghost' : 'btn-primary' }} w-full rounded-lg px-3 py-2 text-xs font-semibold">
                            {{ $project->is_public ? 'Tarik dari Publikasi' : 'Publikasikan ke Peta' }}
                        </button>
                    </form>
                @elseif ($project->exists)
                    <p class="mt-3 rounded-lg border border-amber-400/20 bg-amber-500/10 px-3 py-2 text-[11px] text-amber-200">
                        Hanya Manajer Sales / Superadmin yang bisa mempublikasikan project.
                    </p>
                @endif
            </div>

            <div class="glass rounded-2xl p-5">
                <button type="submit" class="btn-primary w-full rounded-lg px-3 py-2.5 text-sm font-semibold">
                    {{ $project->exists ? 'Simpan Perubahan' : 'Simpan Project' }}
                </button>
                <a href="{{ route('admin.projects.index') }}" class="btn-ghost mt-2 block rounded-lg px-3 py-2 text-center text-xs font-medium">
                    Batal
                </a>
            </div>
        </div>

        {{-- Bottom: location with map --}}
        <div class="glass space-y-4 rounded-2xl p-5 lg:col-span-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-white">Lokasi (Hybrid Geocoding)</h3>
                <span class="text-[11px] text-teal-200/60">Pilih kota → koordinat otomatis. Geser pin untuk override manual.</span>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="md:col-span-1 space-y-3">
                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Cari Kota Referensi</label>
                        <select id="city-picker" class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                            <option value="">— Pilih kota —</option>
                            @foreach ($cities as $c)
                                <option value="{{ $c->id }}"
                                        data-name="{{ $c->name }}"
                                        data-province="{{ $c->province }}"
                                        data-lat="{{ $c->latitude }}"
                                        data-lng="{{ $c->longitude }}">
                                    {{ $c->name }} — {{ $c->province }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Kota</label>
                        <input id="city-input" name="location[city]" value="{{ old('location.city', $loc?->city) }}" required
                               class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @error('location.city') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-medium text-teal-200/80">Provinsi</label>
                        <input id="province-input" name="location[province]" value="{{ old('location.province', $loc?->province) }}" required
                               class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @error('location.province') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs font-medium text-teal-200/80">Latitude</label>
                            <input id="lat-input" name="location[latitude]" type="number" step="0.0000001" required
                                   value="{{ $defaultLat }}"
                                   class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                            @error('location.latitude') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-teal-200/80">Longitude</label>
                            <input id="lng-input" name="location[longitude]" type="number" step="0.0000001" required
                                   value="{{ $defaultLng }}"
                                   class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                            @error('location.longitude') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-xs text-teal-100/80">
                        <input id="manual-flag" type="checkbox" name="location[is_manual_override]" value="1"
                               class="accent-teal-400"
                               @checked(old('location.is_manual_override', $loc?->is_manual_override))>
                        Override manual (geser pin / edit lat/lng)
                    </label>
                </div>

                <div class="md:col-span-2">
                    <div id="form-map" class="h-80 w-full overflow-hidden rounded-xl border border-teal-400/15"></div>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const map = L.map('form-map').setView([{{ $defaultLat }}, {{ $defaultLng }}], 6);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 18, subdomains: 'abcd',
            }).addTo(map);

            const marker = L.marker([{{ $defaultLat }}, {{ $defaultLng }}], { draggable: true }).addTo(map);

            const $lat = document.getElementById('lat-input');
            const $lng = document.getElementById('lng-input');
            const $city = document.getElementById('city-input');
            const $prov = document.getElementById('province-input');
            const $manual = document.getElementById('manual-flag');
            const $picker = document.getElementById('city-picker');

            marker.on('dragend', () => {
                const ll = marker.getLatLng();
                $lat.value = ll.lat.toFixed(7);
                $lng.value = ll.lng.toFixed(7);
                $manual.checked = true;
            });

            $picker.addEventListener('change', e => {
                const opt = e.target.selectedOptions[0];
                if (!opt || !opt.dataset.lat) return;
                $city.value = opt.dataset.name;
                $prov.value = opt.dataset.province;
                $lat.value  = opt.dataset.lat;
                $lng.value  = opt.dataset.lng;
                $manual.checked = false;
                const ll = [parseFloat(opt.dataset.lat), parseFloat(opt.dataset.lng)];
                marker.setLatLng(ll);
                map.setView(ll, 9);
            });

            [$lat, $lng].forEach(el => {
                el.addEventListener('change', () => {
                    const lat = parseFloat($lat.value), lng = parseFloat($lng.value);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        marker.setLatLng([lat, lng]);
                        map.panTo([lat, lng]);
                        $manual.checked = true;
                    }
                });
            });
        });
    </script>
@endsection
