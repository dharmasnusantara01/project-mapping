<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --f900: #020c06;
            --f800: #061410;
            --f700: #09201a;
            --f600: #0e2d22;
            --accent: #34d399;
            --accent-border: rgba(52, 211, 153, 0.18);
            --accent-border-md: rgba(52, 211, 153, 0.22);
            --text-hi:  #ecfdf5;
            --text-mid: rgba(167, 243, 208, 0.75);
            --text-lo:  rgba(167, 243, 208, 0.45);
        }
        body { font-family: 'Sora', ui-sans-serif, system-ui, sans-serif; }
        .bg-stage {
            background:
                radial-gradient(900px 700px at 80% 10%, rgba(5, 40, 22, 0.5), transparent 60%),
                radial-gradient(700px 600px at 10% 90%, rgba(3, 25, 13, 0.7), transparent 60%),
                linear-gradient(135deg, var(--f900) 0%, var(--f800) 45%, var(--f900) 100%);
        }
        .glass {
            background: linear-gradient(160deg, rgba(9, 30, 18, 0.60) 0%, rgba(4, 18, 10, 0.70) 100%);
            backdrop-filter: blur(14px) saturate(140%);
            -webkit-backdrop-filter: blur(14px) saturate(140%);
            border: 1px solid var(--accent-border);
        }
        .input {
            background: rgba(3, 14, 8, 0.55);
            border: 1px solid var(--accent-border-md);
            color: var(--text-hi);
        }
        .input::placeholder { color: var(--text-lo); }
        .input:focus {
            outline: none;
            border-color: rgba(52, 211, 153, 0.65);
            box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.10);
            background: rgba(3, 14, 8, 0.75);
        }
        .btn-primary {
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
            box-shadow: 0 10px 22px -10px rgba(16, 185, 129, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.15);
            color: white;
        }
        .btn-primary:hover { filter: brightness(1.08); }
        .btn-ghost {
            background: rgba(9, 30, 18, 0.5);
            border: 1px solid var(--accent-border-md);
            color: var(--text-hi);
        }
        .btn-ghost:hover { background: rgba(9, 30, 18, 0.8); }
        .nav-link {
            color: var(--text-mid);
            border-left: 2px solid transparent;
        }
        .nav-link:hover { color: #fff; background: rgba(52, 211, 153, 0.07); }
        .nav-link.active {
            color: #fff;
            background: rgba(52, 211, 153, 0.10);
            border-left-color: var(--accent);
        }
        .badge-soft {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid currentColor;
        }
    </style>
</head>
<body class="bg-stage min-h-screen text-slate-100">

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="hidden w-64 shrink-0 border-r border-white/5 bg-[rgba(2,10,5,0.70)] lg:block">
            <div class="flex h-16 items-center gap-2 border-b border-white/5 px-5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 text-xs font-bold text-slate-900">
                    GK
                </div>
                <div>
                    <div class="text-sm font-semibold leading-tight" style="color:var(--text-hi)">GeoKarya</div>
                    <div class="text-[10px] uppercase tracking-wider" style="color:var(--text-lo)">Admin Panel</div>
                </div>
            </div>
            <nav class="mt-4 space-y-1 px-2 text-sm">

                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link flex items-center gap-3 rounded-md px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a1 1 0 011-1h5a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 10a1 1 0 011-1h5a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2zm9-10a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1V4zm0 7a1 1 0 011-1h3a1 1 0 011 1v5a1 1 0 01-1 1h-3a1 1 0 01-1-1v-5z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.instansi.index') }}"
                   class="nav-link flex items-center gap-3 rounded-md px-3 py-2 {{ request()->routeIs('admin.instansi.*') ? 'active' : '' }}">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2H3V4zm0 4h14v8a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm3 2v2h2v-2H6zm0 4v2h2v-2H6z"/></svg>
                    Instansi
                </a>
                <a href="{{ route('admin.witel.index') }}"
                   class="nav-link flex items-center gap-3 rounded-md px-3 py-2 {{ request()->routeIs('admin.witel.*') ? 'active' : '' }}">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3 2v2h2V7H5zm4 0v2h2V7H9zm4 0v2h2V7h-2zM5 11v2h2v-2H5zm4 0v2h2v-2H9zm4 0v2h2v-2h-2z"/></svg>
                    Witel
                </a>
                <a href="{{ route('admin.account_managers.index') }}"
                   class="nav-link flex items-center gap-3 rounded-md px-3 py-2 {{ request()->routeIs('admin.account_managers.*') ? 'active' : '' }}">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
                    Account Manager
                </a>
                <a href="{{ route('public.map') }}" target="_blank"
                   class="nav-link flex items-center gap-3 rounded-md px-3 py-2">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6c0 4.5 6 10 6 10s6-5.5 6-10a6 6 0 00-6-6zm0 8a2 2 0 110-4 2 2 0 010 4z"/></svg>
                    Peta Publik
                    <svg class="ml-auto h-3 w-3 opacity-60" viewBox="0 0 20 20" fill="currentColor"><path d="M11 3a1 1 0 100 2h2.586l-5.293 5.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"/></svg>
                </a>
            </nav>
        </aside>

        {{-- Main --}}
        <div class="flex w-full flex-col">
            <header class="glass sticky top-0 z-20 flex h-16 items-center justify-between border-b border-white/5 px-6">
                <h1 class="text-base font-semibold text-white">@yield('title')</h1>
                <div class="flex items-center gap-3 text-sm">
                    @auth
                        <div class="text-right leading-tight">
                            <div class="font-medium text-white">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] uppercase tracking-wider" style="color:var(--text-lo)">
                                {{ auth()->user()->role->label() }}
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn-ghost rounded-lg px-3 py-1.5 text-xs font-medium">Logout</button>
                        </form>
                    @endauth
                </div>
            </header>

            <main class="flex-1 px-6 py-6">
                @if (session('status'))
                    <div class="mb-4 rounded-lg border border-emerald-400/30 bg-emerald-500/10 px-4 py-2 text-sm text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
