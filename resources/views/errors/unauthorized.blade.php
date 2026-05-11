<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal System — Authorized Access Only</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }

        :root {
            --bg-0: #05080d;
            --bg-1: #0a1320;
            --line:  rgba(94, 191, 232, 0.18);
            --line-strong: rgba(94, 191, 232, 0.45);
            --cyan:  #5ec5f0;
            --cyan-glow: rgba(94, 197, 240, 0.55);
            --text-hi:  #f4f8fb;
            --text-mid: rgba(196, 214, 230, 0.78);
            --text-lo:  rgba(167, 188, 207, 0.55);
            --ok:    #4ade80;
        }

        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto,
                         "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            color: var(--text-hi);
            background:
                radial-gradient(ellipse 80% 60% at 50% 50%, #0e1b2c 0%, #05080d 65%, #02050a 100%);
            overflow: hidden;
        }

        /* ── Circuit lines / decorative SVG layer ───────────── */
        .bg-deco {
            position: fixed; inset: 0; z-index: 0;
            pointer-events: none;
            opacity: 0.85;
        }

        /* Decorative corner grids */
        .grid-dots {
            position: fixed; z-index: 1;
            width: 90px; height: 90px;
            background-image: radial-gradient(rgba(94, 191, 232, 0.35) 1.2px, transparent 1.4px);
            background-size: 18px 18px;
            opacity: 0.55;
        }
        .grid-dots.left  { top: 50%; left: 8%;  transform: translateY(-50%); }
        .grid-dots.right { top: 50%; right: 8%; transform: translateY(-50%); }

        /* ── Main centered stack ───────────────────────────── */
        .stage {
            position: relative; z-index: 2;
            min-height: 100%;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 48px 24px;
            text-align: center;
        }

        /* ── Padlock ──────────────────────────────────────── */
        .lock-wrap {
            position: relative;
            width: 220px; height: 220px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 40px;
        }
        .lock-ring {
            position: absolute; inset: 0;
            border-radius: 50%;
            border: 2px solid var(--cyan);
            box-shadow:
                0 0 38px 6px var(--cyan-glow),
                inset 0 0 38px 4px rgba(94, 197, 240, 0.18);
            animation: ringPulse 3.6s ease-in-out infinite;
        }
        .lock-ring::after {
            content: '';
            position: absolute; inset: -14px;
            border-radius: 50%;
            border: 1px solid rgba(94, 197, 240, 0.18);
        }
        .lock-svg {
            width: 96px; height: 96px;
            color: #ffffff;
            filter: drop-shadow(0 0 14px rgba(255,255,255,0.35));
            position: relative; z-index: 2;
        }

        @keyframes ringPulse {
            0%, 100% { box-shadow: 0 0 30px 4px var(--cyan-glow), inset 0 0 30px 3px rgba(94,197,240,0.15); }
            50%      { box-shadow: 0 0 48px 10px var(--cyan-glow), inset 0 0 44px 6px rgba(94,197,240,0.22); }
        }

        /* ── Text ────────────────────────────────────────── */
        h1 {
            margin: 0;
            font-size: clamp(28px, 4.6vw, 52px);
            font-weight: 800;
            letter-spacing: .04em;
            color: var(--text-hi);
            text-shadow: 0 0 18px rgba(94,197,240,0.22);
        }
        .subtitle {
            margin-top: 10px;
            font-size: clamp(11px, 1.4vw, 14px);
            font-weight: 500;
            letter-spacing: .42em;
            color: var(--text-mid);
            text-transform: uppercase;
        }
        .desc {
            margin-top: 26px;
            max-width: 520px;
            font-size: 12.5px;
            line-height: 1.7;
            letter-spacing: .14em;
            color: var(--text-lo);
            text-transform: uppercase;
        }

        .actions {
            margin-top: 32px;
            display: flex; gap: 14px; flex-wrap: wrap; justify-content: center;
        }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 22px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--text-hi);
            background: rgba(94, 197, 240, 0.10);
            border: 1px solid rgba(94, 197, 240, 0.45);
            border-radius: 999px;
            text-decoration: none;
            transition: background .18s, border-color .18s, box-shadow .18s, transform .1s;
        }
        .btn:hover {
            background: rgba(94, 197, 240, 0.22);
            border-color: var(--cyan);
            box-shadow: 0 0 24px -4px var(--cyan-glow);
        }
        .btn:active { transform: translateY(1px); }
        .btn svg { width: 13px; height: 13px; }

        /* ── Footer status bar ───────────────────────────── */
        .status {
            position: fixed; left: 28px; bottom: 22px; z-index: 3;
            display: flex; align-items: center; gap: 10px;
            font-size: 11px;
            letter-spacing: .22em;
            color: var(--text-mid);
            text-transform: uppercase;
        }
        .status-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--ok);
            box-shadow: 0 0 10px rgba(74, 222, 128, 0.85);
            animation: blink 1.8s ease-in-out infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%      { opacity: 0.35; }
        }

        .copyright {
            position: fixed; left: 50%; bottom: 22px; z-index: 3;
            transform: translateX(-50%);
            font-size: 11px;
            letter-spacing: .22em;
            color: var(--text-lo);
        }
        .corner-mark {
            position: fixed; right: 26px; bottom: 22px; z-index: 3;
            color: var(--cyan);
            opacity: 0.7;
        }

        /* ── Entry animation ─────────────────────────────── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .stage > * { animation: fadeUp .7s cubic-bezier(.22,1,.36,1) both; }
        .stage > *:nth-child(2) { animation-delay: .08s; }
        .stage > *:nth-child(3) { animation-delay: .16s; }
        .stage > *:nth-child(4) { animation-delay: .24s; }
        .stage > *:nth-child(5) { animation-delay: .32s; }

        @media (max-width: 540px) {
            .grid-dots { display: none; }
            .status, .copyright, .corner-mark { font-size: 10px; }
            .copyright { letter-spacing: .18em; }
        }
    </style>
</head>
<body>

    {{-- Circuit-line backdrop --}}
    <svg class="bg-deco" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="ln" x1="0" y1="0" x2="1" y2="0">
                <stop offset="0%"   stop-color="rgba(94,191,232,0)"/>
                <stop offset="50%"  stop-color="rgba(94,191,232,0.55)"/>
                <stop offset="100%" stop-color="rgba(94,191,232,0)"/>
            </linearGradient>
            <linearGradient id="ln2" x1="0" y1="0" x2="1" y2="0">
                <stop offset="0%"   stop-color="rgba(94,191,232,0)"/>
                <stop offset="50%"  stop-color="rgba(94,191,232,0.35)"/>
                <stop offset="100%" stop-color="rgba(94,191,232,0)"/>
            </linearGradient>
        </defs>

        {{-- Left side branches feeding toward the padlock --}}
        <g stroke="url(#ln)" stroke-width="1.2" fill="none">
            <path d="M0 450 H 480 L 540 390 H 640"/>
            <path d="M0 520 H 380 L 440 580 H 620"/>
            <path d="M0 380 H 300 L 360 320 H 560"/>
            <path d="M0 600 H 260 L 320 660 H 540"/>
        </g>
        <g stroke="url(#ln2)" stroke-width="0.9" fill="none">
            <path d="M0 200 H 220 L 260 240 H 380"/>
            <path d="M0 720 H 200 L 240 680 H 360"/>
        </g>

        {{-- Right side branches --}}
        <g stroke="url(#ln)" stroke-width="1.2" fill="none">
            <path d="M1440 450 H 960 L 900 390 H 800"/>
            <path d="M1440 520 H 1060 L 1000 580 H 820"/>
            <path d="M1440 380 H 1140 L 1080 320 H 880"/>
            <path d="M1440 600 H 1180 L 1120 660 H 900"/>
        </g>
        <g stroke="url(#ln2)" stroke-width="0.9" fill="none">
            <path d="M1440 200 H 1220 L 1180 240 H 1060"/>
            <path d="M1440 720 H 1240 L 1200 680 H 1080"/>
        </g>

        {{-- Tiny node dots along the lines --}}
        <g fill="rgba(94,191,232,0.55)">
            <circle cx="540" cy="390" r="2"/>
            <circle cx="640" cy="390" r="2"/>
            <circle cx="440" cy="580" r="2"/>
            <circle cx="620" cy="580" r="2"/>
            <circle cx="900" cy="390" r="2"/>
            <circle cx="800" cy="390" r="2"/>
            <circle cx="1000" cy="580" r="2"/>
            <circle cx="820" cy="580" r="2"/>
        </g>
    </svg>

    <div class="grid-dots left"></div>
    <div class="grid-dots right"></div>

    <div class="stage">

        <div class="lock-wrap">
            <div class="lock-ring"></div>
            <svg class="lock-svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 1.5A5.25 5.25 0 0 0 6.75 6.75v3H6A2.25 2.25 0 0 0 3.75 12v8.25A2.25 2.25 0 0 0 6 22.5h12a2.25 2.25 0 0 0 2.25-2.25V12A2.25 2.25 0 0 0 18 9.75h-.75v-3A5.25 5.25 0 0 0 12 1.5Zm-3.75 5.25a3.75 3.75 0 0 1 7.5 0v3h-7.5v-3ZM12 14a1.6 1.6 0 0 1 .8 2.99v1.76a.8.8 0 1 1-1.6 0v-1.76A1.6 1.6 0 0 1 12 14Z"/>
            </svg>
        </div>

        <h1>INTERNAL SYSTEM</h1>
        <div class="subtitle">Authorized Access Only</div>

        <p class="desc">
            Login memerlukan kredensial internal.<br>
            Harap gunakan subdomain yang sesuai.
        </p>

        <div class="actions">
            <a href="{{ url('/login/dashboard-vertikal') }}" class="btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Masuk ke Dashboard
            </a>
        </div>
    </div>

    <div class="status">
        <span class="status-dot"></span>
        Status: Online
    </div>

    <div class="copyright">© {{ date('Y') }} GDEBGT</div>

    <svg class="corner-mark" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M12 3 13.9 9.1 20 11l-6.1 1.9L12 19l-1.9-6.1L4 11l6.1-1.9L12 3Z"/>
    </svg>

</body>
</html>
