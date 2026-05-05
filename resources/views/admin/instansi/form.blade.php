@extends('layouts.admin')

@section('title', $instansi->exists ? 'Edit Instansi' : 'Instansi Baru')

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush

@section('content')
    @php
        $defaultLat = old('latitude',  $instansi->latitude  ?? -0.5);
        $defaultLng = old('longitude', $instansi->longitude ?? 114.0);
        $defaultZoom = $instansi->exists ? 14 : 6;
    @endphp

    <form method="POST"
          action="{{ $instansi->exists ? route('admin.instansi.update', $instansi) : route('admin.instansi.store') }}"
          class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        @csrf
        @if ($instansi->exists) @method('PUT') @endif

        {{-- Left: instansi info --}}
        <div class="glass space-y-4 rounded-2xl p-5 lg:col-span-2">
            <h3 class="text-sm font-semibold text-white">Informasi Instansi</h3>

            <div>
                <label class="text-xs font-medium text-teal-200/80">Nama Instansi</label>
                <input name="nama_instansi" value="{{ old('nama_instansi', $instansi->nama_instansi) }}" required
                       class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                @error('nama_instansi') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-xs font-medium text-teal-200/80">Witel</label>
                    <select name="witel_id" required class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        <option value="">— Pilih Witel —</option>
                        @foreach ($witels as $w)
                            <option value="{{ $w->id }}" @selected(old('witel_id', $instansi->witel_id) == $w->id)>
                                {{ $w->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('witel_id') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-teal-200/80">Account Manager</label>
                    <select name="account_manager_id" required class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        <option value="">— Pilih AM —</option>
                        @foreach ($accountManagers as $am)
                            <option value="{{ $am->id }}" @selected(old('account_manager_id', $instansi->account_manager_id) == $am->id)>
                                {{ $am->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('account_manager_id') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-xs font-medium text-teal-200/80">Sektor</label>
                    <select name="sector_id" required class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                        @foreach ($sectors as $s)
                            <option value="{{ $s->id }}" @selected(old('sector_id', $instansi->sector_id) == $s->id)>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('sector_id') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-teal-200/80">Telpon Instansi</label>
                    <input name="telpon_instansi" value="{{ old('telpon_instansi', $instansi->telpon_instansi) }}"
                           class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                    @error('telpon_instansi') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="text-xs font-medium text-teal-200/80">Alamat Instansi</label>
                <textarea name="alamat_instansi" rows="3" maxlength="1000"
                          class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">{{ old('alamat_instansi', $instansi->alamat_instansi) }}</textarea>
                @error('alamat_instansi') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-medium text-teal-200/80">Ringkasan Publik (max 500 karakter)</label>
                <textarea name="public_summary" rows="3" maxlength="500"
                          class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">{{ old('public_summary', $instansi->public_summary) }}</textarea>
                <p class="mt-1 text-[11px] text-teal-200/60">Hanya tampil di peta publik. Hindari data sensitif.</p>
                @error('public_summary') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Right: publish + actions --}}
        <div class="space-y-4">
            <div class="glass rounded-2xl p-5">
                <h3 class="text-sm font-semibold text-white">Publikasi</h3>

                @if ($instansi->exists)
                    <div class="mt-3 text-xs text-teal-100/80">
                        Status saat ini:
                        @if ($instansi->is_public)
                            <span class="ml-1 inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-2.5 py-1 font-semibold text-emerald-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span> Published
                            </span>
                            @if ($instansi->publisher)
                                <p class="mt-2 text-[11px] text-teal-200/60">
                                    Oleh {{ $instansi->publisher->name }} · {{ $instansi->published_at?->format('d M Y H:i') }}
                                </p>
                            @endif
                        @else
                            <span class="ml-1 inline-flex items-center gap-1.5 rounded-full bg-slate-500/20 px-2.5 py-1 font-semibold text-slate-200">Draft</span>
                        @endif
                    </div>
                @else
                    <p class="mt-2 text-[11px] text-teal-200/60">Instansi baru tersimpan sebagai Draft. Publikasi tersedia setelah disimpan.</p>
                @endif

                @if ($instansi->exists && auth()->user()->canPublishInstansi())
                    <form method="POST" action="{{ route('admin.instansi.publish', $instansi) }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="publish" value="{{ $instansi->is_public ? 0 : 1 }}">
                        <button class="{{ $instansi->is_public ? 'btn-ghost' : 'btn-primary' }} w-full rounded-lg px-3 py-2 text-xs font-semibold">
                            {{ $instansi->is_public ? 'Tarik dari Publikasi' : 'Publikasikan ke Peta' }}
                        </button>
                    </form>
                @elseif ($instansi->exists)
                    <p class="mt-3 rounded-lg border border-amber-400/20 bg-amber-500/10 px-3 py-2 text-[11px] text-amber-200">
                        Hanya Manajer Sales / Superadmin yang bisa mempublikasikan instansi.
                    </p>
                @endif
            </div>

            <div class="glass rounded-2xl p-5">
                <button type="submit" class="btn-primary w-full rounded-lg px-3 py-2.5 text-sm font-semibold">
                    {{ $instansi->exists ? 'Simpan Perubahan' : 'Simpan Instansi' }}
                </button>
                <a href="{{ route('admin.instansi.index') }}" class="btn-ghost mt-2 block rounded-lg px-3 py-2 text-center text-xs font-medium">
                    Batal
                </a>
            </div>
        </div>

        {{-- Bottom: location with map --}}
        <div class="glass space-y-4 rounded-2xl p-5 lg:col-span-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-white">Koordinat Lokasi</h3>
                <span class="text-[11px] text-teal-200/60">Geser pin pada peta untuk menyetel lat/long.</span>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="md:col-span-1 space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs font-medium text-teal-200/80">Latitude</label>
                            <input id="lat-input" name="latitude" type="text" inputmode="decimal"
                                   pattern="-?\d+(\.\d+)?" required
                                   value="{{ $defaultLat }}"
                                   class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                            @error('latitude') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-teal-200/80">Longitude</label>
                            <input id="lng-input" name="longitude" type="text" inputmode="decimal"
                                   pattern="-?\d+(\.\d+)?" required
                                   value="{{ $defaultLng }}"
                                   class="input mt-1 w-full rounded-lg px-3 py-2 text-sm">
                            @error('longitude') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <p class="text-[11px] text-teal-200/60">Rentang Indonesia: lat -11..6, long 95..141.</p>
                </div>

                <div class="md:col-span-2">
                    <div id="form-map" class="h-80 w-full overflow-hidden rounded-xl border border-teal-400/15"></div>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const map = L.map('form-map').setView([{{ $defaultLat }}, {{ $defaultLng }}], {{ $defaultZoom }});
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 18, subdomains: 'abcd',
            }).addTo(map);

            const marker = L.marker([{{ $defaultLat }}, {{ $defaultLng }}], { draggable: true }).addTo(map);

            const $lat = document.getElementById('lat-input');
            const $lng = document.getElementById('lng-input');

            marker.on('dragend', () => {
                const ll = marker.getLatLng();
                $lat.value = ll.lat.toFixed(7);
                $lng.value = ll.lng.toFixed(7);
            });

            [$lat, $lng].forEach(el => {
                el.addEventListener('change', () => {
                    const lat = parseFloat($lat.value), lng = parseFloat($lng.value);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        marker.setLatLng([lat, lng]);
                        map.panTo([lat, lng]);
                    }
                });
            });
        });
    </script>
@endsection
