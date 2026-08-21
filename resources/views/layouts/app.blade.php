<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BPS ACT — Activity Tracker & Team Planner')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
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
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.4); border-radius: 10px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.7); }
    </style>

    <script>
        // Prevent FOUC for sidebar state
        if (localStorage.getItem('sidebarMinimized') === 'true') {
            document.documentElement.classList.add('sidebar-minimized-fouc');
        } else {
            document.documentElement.classList.add('sidebar-expanded-fouc');
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        
        /* FOUC Prevention */
        html.sidebar-minimized-fouc #sidebar { width: 5rem !important; }
        html.sidebar-minimized-fouc #sidebar [x-show="!sidebarMinimized"] { display: none !important; }
        
        html.sidebar-expanded-fouc #sidebar { width: 16rem !important; }
    </style>
</head>

@php
    $isSidebarMinimized = isset($_COOKIE['sidebarMinimized']) && $_COOKIE['sidebarMinimized'] === 'true';
@endphp
<body x-data="{ 
    sidebarMinimized: {{ $isSidebarMinimized ? 'true' : 'false' }},
    toggleSidebar() {
        this.sidebarMinimized = !this.sidebarMinimized;
        localStorage.setItem('sidebarMinimized', this.sidebarMinimized);
        document.cookie = 'sidebarMinimized=' + this.sidebarMinimized + ';path=/;max-age=31536000';
    },
    init() {
        document.documentElement.classList.remove('sidebar-minimized-fouc', 'sidebar-expanded-fouc');
        if (localStorage.getItem('sidebarMinimized') && !document.cookie.includes('sidebarMinimized')) {
            this.sidebarMinimized = localStorage.getItem('sidebarMinimized') === 'true';
            document.cookie = 'sidebarMinimized=' + this.sidebarMinimized + ';path=/;max-age=31536000';
        }
    }
}" class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen flex flex-col">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation (Collapsible) -->
        <aside id="sidebar" :class="{ 'w-20': sidebarMinimized, 'w-64': !sidebarMinimized }" class="bg-bps-blue text-white flex flex-col flex-shrink-0 shadow-lg transition-all duration-300 overflow-x-hidden">
            <!-- Brand Header -->
            <div :class="{ 'justify-center px-0': sidebarMinimized, 'px-5 gap-3': !sidebarMinimized }" class="py-4 flex items-center border-b border-white/10 bg-black/10">
                <img src="{{ asset('BPS Logo.svg') }}" alt="BPS Logo" class="h-9 w-auto object-contain shrink-0">
                <div x-show="!sidebarMinimized" class="whitespace-nowrap transition-opacity duration-300">
                    <h1 class="font-bold text-base tracking-tight leading-none text-white">BPS ACT</h1>
                    <p class="text-xs text-blue-100/70 font-normal mt-0.5">Activity & Planner</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-sm custom-scrollbar">
                <p x-show="!sidebarMinimized" class="px-3 pt-1 pb-2 text-[10px] uppercase tracking-widest font-bold text-blue-200/50 whitespace-nowrap">Kegiatan</p>
                
                <a href="{{ route('dashboard') }}" title="Pusat Kegiatan & Kalender" :class="{ 'justify-center px-0': sidebarMinimized, 'gap-3 px-3': !sidebarMinimized }" class="flex items-center py-2.5 rounded-md font-medium transition {{ request()->is('dashboard') || request()->is('/') ? 'bg-white/20 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Pusat Kegiatan & Kalender</span>
                </a>
                
                <a href="{{ route('activities.list') }}" title="Daftar Kegiatan (Tabel)" :class="{ 'justify-center px-0': sidebarMinimized, 'gap-3 px-3': !sidebarMinimized }" class="flex items-center py-2.5 rounded-md font-medium transition {{ request()->routeIs('activities.list') ? 'bg-white/20 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Daftar Kegiatan (Tabel)</span>
                </a>
                
                <a href="{{ route('calendar.test') }}" title="Kalender (Test)" :class="{ 'justify-center px-0': sidebarMinimized, 'gap-3 px-3': !sidebarMinimized }" class="flex items-center py-2.5 rounded-md font-medium transition {{ request()->routeIs('calendar.test') ? 'bg-white/20 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Kalender (Test)</span>
                </a>
                
                <a href="{{ route('activities.create') }}" title="Buat Kegiatan BPS" :class="{ 'justify-center px-0': sidebarMinimized, 'gap-3 px-3': !sidebarMinimized }" class="flex items-center py-2.5 rounded-md font-medium transition {{ request()->is('activities/create') ? 'bg-white/20 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Buat Kegiatan BPS</span>
                </a>

                <div class="pt-3 pb-1">
                    <p x-show="!sidebarMinimized" class="px-3 pb-2 text-[10px] uppercase tracking-widest font-bold text-blue-200/50 whitespace-nowrap">Peminjaman Aset</p>
                </div>
                <a href="{{ route('assets.index') }}" title="Daftar Aset & Fasilitas" :class="{ 'justify-center px-0': sidebarMinimized, 'gap-3 px-3': !sidebarMinimized }" class="flex items-center py-2.5 rounded-md font-medium transition {{ request()->routeIs('assets.index') ? 'bg-white/20 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Daftar Aset & Fasilitas</span>
                </a>
                <a href="{{ route('borrowings.create') }}" title="Ajukan Peminjaman" :class="{ 'justify-center px-0': sidebarMinimized, 'gap-3 px-3': !sidebarMinimized }" class="flex items-center py-2.5 rounded-md font-medium transition {{ request()->routeIs('borrowings.create') ? 'bg-white/20 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Ajukan Peminjaman</span>
                </a>
                <a href="{{ route('borrowings.index') }}" title="Peminjaman Saya" :class="{ 'justify-center px-0': sidebarMinimized, 'gap-3 px-3': !sidebarMinimized }" class="flex items-center group py-2.5 rounded-md font-medium transition {{ request()->routeIs('borrowings.index') ? 'bg-white/20 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 opacity-80 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Peminjaman Saya</span>
                </a>

                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'lead')
                <!-- Manajemen Menu -->
                <div class="pt-4 mt-4 border-t border-gray-100">
                    <p x-show="!sidebarMinimized" class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 whitespace-nowrap">Manajemen</p>
                    
                    <a href="{{ route('admin.borrowings') }}" title="Panel TU (Peminjaman)" :class="{ 'justify-center px-0': sidebarMinimized, 'gap-3 px-3': !sidebarMinimized }" class="flex items-center group py-2.5 rounded-md font-medium transition {{ request()->routeIs('admin.borrowings') ? 'bg-white/20 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span x-show="!sidebarMinimized" class="whitespace-nowrap">Panel TU (Peminjaman)</span>
                    </a>
                    
                    <a href="{{ route('users.index') }}" title="Pengguna" :class="{ 'justify-center px-0': sidebarMinimized, 'gap-3 px-3': !sidebarMinimized }" class="flex items-center group py-2.5 rounded-md font-medium transition {{ request()->routeIs('users.index') ? 'bg-white/20 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span x-show="!sidebarMinimized" class="whitespace-nowrap">Pengguna</span>
                    </a>

                    <a href="{{ route('ai.assistant') }}" title="Asisten AI" :class="{ 'justify-center px-0': sidebarMinimized, 'gap-3 px-3': !sidebarMinimized }" class="flex items-center group py-2.5 rounded-md font-medium transition mt-1 {{ request()->routeIs('ai.assistant') ? 'bg-white/20 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <span x-show="!sidebarMinimized" class="flex-1 whitespace-nowrap">Asisten AI</span>
                        <span x-show="!sidebarMinimized" class="px-1.5 py-0.5 text-[9px] font-bold bg-white/20 text-white rounded-full shadow-sm">BETA</span>
                    </a>
                </div>
                @endif
                
                <div class="pt-4 mt-4 border-t border-gray-100">
                    <a href="{{ route('profile.index') }}" title="Profil Saya" :class="{ 'justify-center px-0': sidebarMinimized, 'gap-3 px-3': !sidebarMinimized }" class="flex items-center group py-2.5 rounded-md font-medium transition {{ request()->routeIs('profile.index') ? 'bg-white/20 text-white shadow-xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 opacity-80 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span x-show="!sidebarMinimized" class="whitespace-nowrap">Profil Saya</span>
                    </a>
                </div>
            </nav>

            <!-- User Profile & Session Footer -->
            @if(Auth::check())
            @php $u = Auth::user(); @endphp
            <div class="p-3 border-t border-white/10 bg-black/10">
                <div class="flex items-center justify-between rounded-md bg-white/5" :class="{ 'flex-col justify-center text-center p-2': sidebarMinimized, 'p-2 gap-2': !sidebarMinimized }">
                    <div class="flex items-center min-w-0" :class="{ 'justify-center': sidebarMinimized, 'gap-2.5': !sidebarMinimized }">
                        <img src="{{ $u->photo ?? 'https://ui-avatars.com/api/?name='.urlencode($u->name) }}" alt="{{ $u->name }}" class="w-8 h-8 rounded-full border border-white/30 shrink-0">
                        <div x-show="!sidebarMinimized" class="min-w-0 flex-1 transition-opacity duration-300">
                            <p class="text-xs font-semibold text-white truncate">{{ $u->name }}</p>
                            <span class="inline-block text-[10px] uppercase font-bold px-1.5 py-0.2 rounded bg-bps-amber text-black">
                                {{ strtoupper($u->role) }}
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" :class="{ 'mt-2': sidebarMinimized }">
                        @csrf
                        <button type="submit" title="Keluar" class="p-1.5 text-blue-200 hover:text-white hover:bg-white/10 rounded transition cursor-pointer">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </aside>

        <!-- Main Content Viewport -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-50">
            <!-- Top Header Bar -->
            @if(!request()->routeIs('calendar.test'))
            <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between z-10">
                <div class="flex items-center gap-4">
                    <button @click="toggleSidebar()" class="p-2 -ml-2 rounded-md hover:bg-gray-100 text-gray-500 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 tracking-tight">@yield('header_title', 'Badan Pusat Statistik Activity Tracker')</h2>
                        <p class="text-xs text-gray-500 hidden sm:block">Single Source of Truth Kegiatan Tim BPS</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                        Supabase PostgreSQL Connected
                    </span>
                    @yield('header_actions')
                </div>
            </header>
            @endif

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
            <main class="flex-1 overflow-y-auto {{ request()->routeIs('calendar.test') ? '' : 'p-6' }}">
                @yield('content')
            </main>
        </div>
    </div>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
