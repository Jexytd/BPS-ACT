<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BPS ACT — Activity Tracker & Team Planner')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* FullCalendar BPS Brand Customization */
        .fc {
            --fc-border-color: #e5e7eb;
            --fc-button-bg-color: var(--color-bps-primary);
            --fc-button-border-color: var(--color-bps-primary);
            --fc-button-hover-bg-color: var(--color-bps-secondary);
            --fc-button-hover-border-color: var(--color-bps-secondary);
            --fc-button-active-bg-color: var(--color-bps-secondary);
            --fc-button-active-border-color: var(--color-bps-secondary);
            --fc-today-bg-color: #fefce8;
            font-family: var(--font-sans);
        }
        .fc .fc-toolbar-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
        }
        .fc .fc-button {
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen flex flex-col">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation (Fixed w-64) -->
        <aside class="w-64 bg-bps-blue text-white flex flex-col flex-shrink-0 shadow-lg">
            <!-- Brand Header -->
            <div class="px-5 py-4 flex items-center gap-3 border-b border-white/10 bg-black/10">
                <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center font-bold text-white tracking-wider text-sm border border-white/20">
                    BPS
                </div>
                <div>
                    <h1 class="font-bold text-base tracking-tight leading-none text-white">BPS ACT</h1>
                    <p class="text-xs text-blue-100/70 font-normal mt-0.5">Activity & Planner</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-sm">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md font-medium transition {{ request()->is('dashboard') || request()->is('/') ? 'bg-white/20 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard Executive
                </a>

                <a href="{{ route('activities.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-md font-medium transition {{ request()->is('activities') ? 'bg-white/20 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Kalender & Team Planner
                </a>
            </nav>

            <!-- User Profile & Session Footer -->
            @if(session()->has('user'))
            @php $u = session('user'); @endphp
            <div class="p-3 border-t border-white/10 bg-black/10">
                <div class="flex items-center justify-between gap-2 p-2 rounded-md bg-white/5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <img src="{{ $u['photo'] ?? 'https://ui-avatars.com/api/?name='.urlencode($u['name']) }}" alt="{{ $u['name'] }}" class="w-8 h-8 rounded-full border border-white/30 flex-shrink-0">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-white truncate">{{ $u['name'] }}</p>
                            <span class="inline-block text-[10px] uppercase font-bold px-1.5 py-0.2 rounded bg-bps-amber text-black">
                                {{ strtoupper($u['role']) }}
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar" class="p-1.5 text-blue-200 hover:text-white hover:bg-white/10 rounded transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </aside>

        <!-- Main Content Viewport -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-50">
            <!-- Top Header Bar -->
            <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10">
                <div>
                    <h2 class="text-lg font-bold text-gray-800 tracking-tight">@yield('header_title', 'Badan Pusat Statistik Activity Tracker')</h2>
                    <p class="text-xs text-gray-500">Single Source of Truth Kegiatan Tim BPS</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-bps-blue border border-blue-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-bps-green mr-1.5 animate-pulse"></span>
                        Cloud Firestore Connected
                    </span>
                </div>
            </header>

            <!-- Alerts / Notifications -->
            @if(session('success'))
            <div class="mx-6 mt-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-md flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="mx-6 mt-4 p-3 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-md flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            <!-- Main Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>
    @yield('scripts')
</body>
</html>
