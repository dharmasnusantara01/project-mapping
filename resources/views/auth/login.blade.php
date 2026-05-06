@php $appName = 'GridCore'; @endphp
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — {{ $appName }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:300,400,600,700|dm-mono:400,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --f900: #020c06;
            --f800: #061410;
            --f700: #09201a;
            --f600: #0e2d22;
            --accent: #34d399;
            --accent-glow: rgba(52, 211, 153, 0.18);
            --text-hi:  #ecfdf5;
            --text-mid: #a7f3d0;
            --text-lo:  rgba(167, 243, 208, 0.45);
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body { font-family: 'Sora', ui-sans-serif, system-ui, sans-serif; }

        /* ── Background map ─────────────────────────────────── */
        #bg-map {
            position: fixed; inset: 0; z-index: 0;
        }
        .leaflet-container { background: var(--f900); }

        /* Shift tile hue → forest green, match card */
        .leaflet-tile-pane {
            filter: hue-rotate(285deg) saturate(2.4) brightness(0.68);
        }

        /* Unified dark-green wash over map */
        #bg-map::after {
            content: '';
            position: absolute; inset: 0; z-index: 800;
            background: linear-gradient(
                160deg,
                rgba(3, 14, 9, 0.58) 0%,
                rgba(6, 22, 14, 0.50) 100%
            );
            pointer-events: none;
        }

        .leaflet-control-zoom,
        .leaflet-control-attribution { display: none !important; }

        /* ── Card ──────────────────────────────────────────── */
        .card {
            background: linear-gradient(
                160deg,
                rgba(9, 30, 18, 0.76) 0%,
                rgba(4, 18, 10, 0.86) 100%
            );
            backdrop-filter: blur(24px) saturate(160%);
            -webkit-backdrop-filter: blur(24px) saturate(160%);
            border: 1px solid rgba(52, 211, 153, 0.14);
            box-shadow:
                0 0 0 1px rgba(52, 211, 153, 0.06),
                0 32px 64px -24px rgba(0, 0, 0, 0.7),
                inset 0 1px 0 rgba(255, 255, 255, 0.04);
        }

        /* Subtle inner top-edge highlight */
        .card::before {
            content: '';
            position: absolute; inset: 0;
            border-radius: inherit;
            background: linear-gradient(180deg, rgba(52,211,153,0.06) 0%, transparent 40%);
            pointer-events: none;
        }

        /* ── Brand mark ─────────────────────────────────────── */
        .brand-mark {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #0d9488 0%, #059669 100%);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 20px -6px rgba(13, 148, 136, 0.6);
            flex-shrink: 0;
        }

        /* ── Divider ─────────────────────────────────────────── */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(52,211,153,0.18), transparent);
        }

        /* ── Input ───────────────────────────────────────────── */
        .field {
            display: flex; align-items: center; gap: 12px;
            background: rgba(3, 14, 8, 0.60);
            border: 1px solid rgba(52, 211, 153, 0.16);
            border-radius: 12px;
            padding: 12px 16px;
            transition: border-color .18s, box-shadow .18s, background .18s;
        }
        .field:focus-within {
            border-color: rgba(52, 211, 153, 0.55);
            box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.09);
            background: rgba(3, 14, 8, 0.75);
        }
        .field input {
            flex: 1; background: transparent; outline: none; border: none;
            color: var(--text-hi);
            font-family: inherit; font-size: 13.5px;
        }
        .field input::placeholder { color: var(--text-lo); }

        /* ── Button ──────────────────────────────────────────── */
        .btn {
            width: 100%;
            padding: 13px;
            border: none; border-radius: 12px; cursor: pointer;
            font-family: 'Sora', sans-serif;
            font-size: 12.5px; font-weight: 700;
            letter-spacing: .1em; text-transform: uppercase;
            color: #fff;
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
            box-shadow:
                0 10px 28px -10px rgba(16, 185, 129, 0.55),
                inset 0 1px 0 rgba(255,255,255,0.15);
            transition: filter .15s, transform .1s;
        }
        .btn:hover  { filter: brightness(1.1); }
        .btn:active { transform: translateY(1px); filter: brightness(0.97); }

        /* ── Fade-in on load ─────────────────────────────────── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .card-wrap {
            animation: fadeUp .55s cubic-bezier(.22,1,.36,1) both;
        }
    </style>
</head>
<body class="h-full">

    <div id="bg-map"></div>

    <div class="relative z-10 flex min-h-screen items-center justify-center px-6 py-10 lg:justify-end lg:pr-[9%]">
        <div class="card-wrap w-full max-w-sm">
            <div class="card relative overflow-hidden rounded-3xl p-8">

                {{-- Brand header --}}
                <div class="flex items-center gap-3">
                    <div class="brand-mark">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="10" r="3"/>
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-base font-bold leading-tight" style="color:var(--text-hi)">GridCore</div>
                        <div class="text-[10px] font-medium uppercase tracking-[.15em]" style="color:var(--text-lo)">Monitoring System yang Kuat dan Terukur</div>
                    </div>
                </div>

                <div class="divider my-6"></div>

                {{-- Form --}}
                <form method="POST" action="{{ url('/login') }}" class="space-y-3">
                    @csrf

                    <div class="field">
                        <svg class="h-4 w-4 shrink-0" style="color:var(--text-lo)" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Email"
                               autocomplete="email"
                               required>
                    </div>

                    <div class="field">
                        <svg class="h-4 w-4 shrink-0" style="color:var(--text-lo)" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 1a4 4 0 00-4 4v3H5a2 2 0 00-2 2v7a2 2 0 002 2h10a2 2 0 002-2v-7a2 2 0 00-2-2h-1V5a4 4 0 00-4-4zm2 7V5a2 2 0 10-4 0v3h4z" clip-rule="evenodd"/>
                        </svg>
                        <input type="password"
                               name="password"
                               placeholder="Kata Sandi"
                               autocomplete="current-password"
                               required>
                        <button type="button"
                                onclick="(function(b){ const i=b.parentElement.querySelector('input'); i.type=i.type==='password'?'text':'password'; })(this)"
                                style="color:var(--text-lo); background:none; border:none; cursor:pointer; padding:0; line-height:1;"
                                aria-label="Toggle visibilitas">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.5 10S5.5 4.5 10 4.5 17.5 10 17.5 10 14.5 15.5 10 15.5 2.5 10 2.5 10z" fill="none" stroke="currentColor" stroke-width="1.4"/>
                                <circle cx="10" cy="10" r="2.4" fill="currentColor"/>
                            </svg>
                        </button>
                    </div>

                    @error('email')
                        <p class="text-xs" style="color:#fca5a5">{{ $message }}</p>
                    @enderror

                    <div class="pt-1">
                        <button type="submit" class="btn">Masuk ke Dashboard</button>
                    </div>
                </form>

                <p class="mt-5 text-center text-[11px]" style="color:var(--text-lo)">
                    Hubungi admin untuk akses atau kendala login.
                </p>

            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        L.map('bg-map', {
            zoomControl: false, dragging: false,
            scrollWheelZoom: false, doubleClickZoom: false,
            touchZoom: false, keyboard: false, attributionControl: false,
        }).setView([-0.5, 114.0], 6).addLayer(
            L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', { maxZoom: 20 })
        );
    </script>
</body>
</html>
