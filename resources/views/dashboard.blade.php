@extends('layouts.app')

@section('title', 'Pusat Kegiatan & Dashboard — BPS ACT')
@section('header_title', 'Pusat Kegiatan & Dashboard Tim BPS')

@section('header_actions')
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-bps-blue hover:bg-gray-100 rounded-full transition cursor-pointer">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        @if($unreadCount > 0)
        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white animate-pulse"></span>
        @endif
    </button>
    <div x-show="open" @click.outside="open = false" x-cloak 
         class="absolute right-0 mt-2 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden transform origin-top-right transition"
         style="width: min(420px, calc(100vw - 32px));">
        <div class="bg-bps-blue px-5 py-3 text-white flex justify-between items-center whitespace-nowrap">
            <h3 class="font-bold text-sm truncate">Notifikasi Kegiatan Tim</h3>
            <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full flex-shrink-0 ml-3 font-medium">{{ $unreadCount }} Baru</span>
        </div>
        <div class="bg-white overflow-y-auto" style="max-height: min(600px, 70vh); scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;">
            @if($recentTeamFeed->isNotEmpty())
                <ul class="divide-y divide-gray-100">
                @foreach($recentTeamFeed as $feed)
                @php 
                    $creatorId = $feed['created_by'] ?? ($feed['createdBy'] ?? null);
                    $creator = collect($users)->firstWhere('id', $creatorId);
                    $readList = $feed['read_by'] ?? ($feed['readBy'] ?? []);
                    $isRead = in_array($user['id'] ?? '', is_array($readList) ? $readList : []);
                @endphp
                <li id="notif-{{ $feed['id'] }}" class="relative group px-5 py-4 transition {{ $isRead ? 'bg-white opacity-75' : 'bg-blue-50/40' }}">
                    <div class="flex justify-between items-start gap-3">
                        <a href="javascript:void(0)" @click="readNotification('{{ $feed['id'] }}')" class="flex-1 block cursor-pointer min-w-0">
                            <div class="text-[13.5px] text-gray-800 leading-tight">
                                <span class="font-bold text-bps-blue block mb-0.5 truncate">{{ $creator['name'] ?? 'Sistem' }}</span>
                                <span class="text-gray-500">Menambahkan kegiatan:</span>
                                <span class="font-bold text-gray-900 block mt-0.5 leading-snug break-words">"{{ $feed['title'] ?? $feed['subject'] }}"</span>
                            </div>
                            <span class="text-xs {{ $isRead ? 'text-gray-400' : 'text-bps-blue font-semibold' }} mt-1.5 block">Tanggal: {{ \Carbon\Carbon::parse($feed['start'] ?? $feed['start_date'])->format('d M Y') }}</span>
                        </a>
                        <button @click.stop="deleteNotificationItem('{{ $feed['id'] }}')" class="opacity-0 group-hover:opacity-100 p-1.5 text-gray-400 hover:text-rose-500 transition rounded-full hover:bg-rose-50 cursor-pointer flex-shrink-0 mt-0.5" title="Hapus Notifikasi">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </li>
                @endforeach
                </ul>
                <div class="bg-gray-50 border-t border-gray-100">
                    <a href="{{ route('notifications.index') }}" class="block w-full text-center px-4 py-3 text-sm font-bold text-bps-blue hover:bg-gray-200 transition">
                        Lihat Semua Notifikasi
                    </a>
                </div>
            @else
                <div class="p-8 text-center text-sm text-gray-500">
                    Tidak ada notifikasi baru.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('content')
<div x-data="unifiedDashboardApp()" x-init="initDashboard()" class="space-y-6">

    <!-- Top Action Bar & Filters -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-2xs flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <!-- Team Scope Filters -->
            <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg">
                <button @click="setTeamScope('{{ $user['division_id'] ?? '' }}')" 
                        :class="teamScope === '{{ $user['division_id'] ?? '' }}' ? 'bg-bps-blue text-white shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="px-3 py-1.5 rounded-md text-xs transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Kegiatan Tim Saya
                </button>
                <select x-model="teamScope" @change="setTeamScope($event.target.value)" 
                        :class="teamScope !== '{{ $user['division_id'] ?? '' }}' ? 'bg-bps-blue text-white shadow-xs font-bold border-transparent' : 'text-gray-600 bg-transparent hover:text-gray-900 font-medium border-transparent focus:border-bps-blue focus:ring-0'"
                        class="px-3 py-1.5 rounded-md text-xs transition cursor-pointer appearance-none border outline-none pr-8 relative"
                        style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22currentColor%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.25em 1.25em;">
                    <option value="all" class="text-gray-900 bg-white font-semibold">🌍 Semua Tim BPS (Keseluruhan)</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div['id'] }}" class="text-gray-900 bg-white">{{ $div['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- View Mode Switcher -->
            <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg">
                <button @click="switchView('timeGridDay')" 
                        :class="currentView === 'timeGridDay' ? 'bg-bps-blue text-white shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="px-3 py-1.5 rounded-md text-xs transition cursor-pointer flex items-center gap-1"
                        title="Kalender 1 Hari Real-time Vertikal">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Kalender Harian
                </button>
                <button @click="switchView('resourceTimelineWeek')" 
                        :class="currentView === 'resourceTimelineWeek' ? 'bg-bps-blue text-white shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="px-3 py-1.5 rounded-md text-xs transition cursor-pointer flex items-center gap-1.5"
                        title="Kalender Mingguan">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Kalender Mingguan
                </button>
                <button @click="switchView('dayGridMonth')" 
                        :class="currentView === 'dayGridMonth' ? 'bg-bps-blue text-white shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'" 
                        class="px-3 py-1.5 rounded-md text-xs transition cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Kalender Bulanan
                </button>
            </div>
        </div>

        <!-- Right Side: New Activity Action -->
        <div class="flex items-center gap-3">
            <a href="{{ route('activities.create') }}" class="px-4 py-2 bg-bps-blue hover:bg-bps-teal text-white font-semibold text-xs rounded-lg shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Kegiatan Baru
            </a>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-2xs p-4 relative min-h-[600px]">
        <!-- Loading Skeleton -->
        <div x-show="loading" class="absolute inset-0 bg-white/80 z-20 flex items-center justify-center backdrop-blur-xs">
            <div class="flex items-center gap-3 px-5 py-4 bg-white border border-gray-200 rounded-xl shadow-xl">
                <svg class="w-6 h-6 text-bps-blue animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-sm font-bold text-gray-800">Memuat Jadwal Kegiatan...</span>
            </div>
        </div>

        <!-- FullCalendar Mounting Point -->
        <div id="calendar" class="w-full"></div>
    </div>

    <!-- Activity Detail Modal -->
    <div x-show="showDetailModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/50" @click="showDetailModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-2xl border border-gray-200 sm:my-8 relative">
                <!-- Modal Header -->
                <div class="flex items-start justify-between pb-4 border-b border-gray-200">
                    <div>
                        <span class="text-[10px] uppercase tracking-widest font-bold bg-bps-blue/10 px-2 py-0.5 rounded text-bps-blue" x-text="'ID #' + (selectedEvent?.id || '')"></span>
                        <h2 class="text-lg font-bold mt-1 text-gray-900" x-text="selectedEvent?.title || 'Detail Kegiatan'"></h2>
                        <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-500 font-medium">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span x-text="selectedEvent?.extendedProps?.creator_team || 'Tim Umum'"></span>
                            </span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span x-text="'Dibuat oleh ' + (selectedEvent?.extendedProps?.creator_name || 'Sistem')"></span>
                            </span>
                        </div>
                    </div>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-red-500 transition focus:outline-none p-1 rounded-full hover:bg-red-50 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Content Details -->
                <div class="mt-5 space-y-5">
                    <!-- Schedule Info -->
                    <div class="bg-gray-50/50 p-4 rounded-lg border border-gray-100 flex items-start gap-3">
                        <div class="bg-white p-2 rounded shadow-sm text-bps-blue border border-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Jadwal Pelaksanaan</p>
                            <p class="text-sm font-bold text-gray-900 mt-0.5" x-text="formatDateRange(selectedEvent?.start, selectedEvent?.end)"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                  :class="{
                                    'bg-green-100 text-green-800': ['done', 'confirmed', 'closed'].includes((selectedEvent?.extendedProps?.status || '').toLowerCase()),
                                    'bg-yellow-100 text-yellow-800': ['ongoing', 'in progress'].includes((selectedEvent?.extendedProps?.status || '').toLowerCase()),
                                    'bg-blue-100 text-blue-800': ['planned', 'in specification', 'new'].includes((selectedEvent?.extendedProps?.status || '').toLowerCase()),
                                    'bg-gray-100 text-gray-800': !selectedEvent?.extendedProps?.status
                                  }"
                                  x-text="selectedEvent?.extendedProps?.status || 'Planned'">
                            </span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Kategori</p>
                            <p class="text-sm text-gray-900 font-medium" x-text="selectedEvent?.extendedProps?.category || '-'"></p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Lokasi Kegiatan</p>
                            <p class="text-sm text-gray-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span x-text="selectedEvent?.extendedProps?.location || '-'"></span>
                            </p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Deskripsi & Catatan</p>
                            <div class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3 border border-gray-100 min-h-[60px] whitespace-pre-wrap break-words" x-text="selectedEvent?.extendedProps?.description || 'Tidak ada deskripsi yang dicantumkan.'"></div>
                        </div>

                        <!-- Documents -->
                        <template x-if="hasDocuments()">
                            <div class="col-span-2">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Dokumen / Tautan</p>
                                <div class="space-y-2">
                                    <template x-for="key in ['surat_tugas', 'undangan', 'kuesioner', 'berita_acara', 'laporan']" :key="key">
                                        <div x-show="(selectedEvent?.extendedProps?.documents && (selectedEvent.extendedProps.documents[key] === true || selectedEvent.extendedProps.documents[key] === 'true' || selectedEvent.extendedProps.documents[key] === 1)) || (selectedEvent?.extendedProps?.documents_links && selectedEvent.extendedProps.documents_links[key])" class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 text-xs font-medium text-gray-700 bg-white">
                                            <svg class="w-4 h-4 text-bps-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <span class="capitalize flex-1" x-text="key.replace('_', ' ')"></span>
                                            <a x-show="selectedEvent?.extendedProps?.documents_links && selectedEvent.extendedProps.documents_links[key]" 
                                               :href="selectedEvent.extendedProps.documents_links[key]" 
                                               target="_blank" 
                                               class="text-bps-blue hover:text-bps-teal flex items-center gap-1 bg-blue-50 px-2 py-1 rounded transition">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                Buka
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Assignees -->
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 flex items-center justify-between">
                            <span>Tim Penugasan</span>
                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[9px]" x-text="(selectedEvent?.extendedProps?.assignees_rich?.length || 0) + ' Orang'"></span>
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[160px] overflow-y-auto pr-2 custom-scrollbar">
                            <template x-if="!selectedEvent?.extendedProps?.assignees_rich || selectedEvent.extendedProps.assignees_rich.length === 0">
                                <p class="text-xs text-gray-500 italic col-span-2">Belum ada pegawai yang ditugaskan pada kegiatan ini.</p>
                            </template>
                            <template x-for="user in selectedEvent?.extendedProps?.assignees_rich" :key="user.name">
                                <div class="flex items-center gap-2.5 p-2 rounded-lg bg-gray-50 border border-gray-100 overflow-hidden">
                                    <div class="w-8 h-8 rounded-full bg-bps-blue text-white flex items-center justify-center text-[10px] font-bold shadow-xs border border-white shrink-0 overflow-hidden" style="width: 32px; height: 32px; min-width: 32px; min-height: 32px; max-width: 32px; max-height: 32px;">
                                        <template x-if="user.avatar">
                                            <img :src="user.avatar" class="w-full h-full object-cover rounded-full" style="width: 32px; height: 32px; min-width: 32px; min-height: 32px; max-width: 32px; max-height: 32px; object-fit: cover;" alt="">
                                        </template>
                                        <template x-if="!user.avatar">
                                            <span x-text="user.initials"></span>
                                        </template>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-gray-900 truncate" x-text="user.name"></p>
                                        <p class="text-[10px] text-gray-500 truncate" x-text="(user.team || '').startsWith('Tim') ? user.team : ('Tim ' + (user.team || '-'))"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5">
                    <button type="button" @click="showDetailModal = false" class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-xs font-semibold text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none transition cursor-pointer">
                        Tutup Panel
                    </button>
                    <template x-if="(selectedEvent?.extendedProps?.created_by === '{{ $user['id'] ?? '' }}') || ['admin', 'lead'].includes('{{ $user['role'] ?? '' }}')">
                        <div class="flex flex-col sm:flex-row gap-2.5 w-full sm:w-auto">
                            <button type="button" @click="deleteEvent(selectedEvent?.id)" class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg border border-red-300 px-4 py-2 bg-white text-xs font-semibold text-red-700 shadow-xs hover:bg-red-50 focus:outline-none transition cursor-pointer">
                                Hapus
                            </button>
                            <a :href="'/activities/' + selectedEvent?.id + '/edit'" class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg border border-transparent px-4 py-2 bg-bps-blue text-xs font-semibold text-white shadow-xs hover:bg-bps-teal focus:outline-none transition cursor-pointer gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                Edit Kegiatan
                            </a>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function unifiedDashboardApp() {
        return {
            loading: true,
            currentView: 'timeGridDay',
            teamScope: '{{ $user['division_id'] ?? '' }}',
            calendar: null,
            showDetailModal: false,
            selectedEvent: null,

            initDashboard() {
                this.$nextTick(() => {
                    const calendarEl = document.getElementById('calendar');
                    this.calendar = new window.FullCalendar.Calendar(calendarEl, {
                        plugins: [
                            window.FullCalendar.dayGridPlugin,
                            window.FullCalendar.timeGridPlugin,
                            window.FullCalendar.interactionPlugin,
                            window.FullCalendar.resourceTimelinePlugin,
                            window.FullCalendar.formaThemePlugin
                        ],
                        initialView: this.currentView,
                        locales: [window.FullCalendar.idLocale],
                        locale: 'id',
                        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'timeGridDay,resourceTimelineWeek,dayGridMonth'
                        },
                        nowIndicator: true,
                        allDaySlot: true,
                        weekends: false, // Nonaktifkan Sabtu dan Minggu
                        slotMinTime: '06:00:00',
                        slotMaxTime: '22:00:00',
                        scrollTime: '08:00:00',
                        slotHeaderFormat: {
                            weekday: 'long',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        },
                        editable: true,
                        selectable: true,
                        select: function(info) {
                            let endStr = info.endStr;
                            if (!endStr) {
                                // Fallback just in case
                                let d = new Date(info.startStr);
                                d.setHours(d.getHours() + 1);
                                endStr = d.toISOString();
                            }
                            window.location.href = "{{ route('activities.create') }}?start=" + encodeURIComponent(info.startStr) + "&end=" + encodeURIComponent(endStr);
                        },
                        resourceGroupField: 'group',
                        resources: (fetchInfo, successCallback, failureCallback) => {
                            fetch(this.getResourcesUrl())
                                .then(res => res.json())
                                .then(data => successCallback(data))
                                .catch(err => failureCallback(err));
                        },
                        events: this.getEventsUrl(),
                        loading: (isLoading) => {
                            this.loading = isLoading;
                        },
                        eventClick: (info) => {
                            this.selectedEvent = info.event;
                            this.showDetailModal = true;
                        },
                        eventDrop: (info) => {
                            this.updateEventDates(info.event);
                        },
                        eventResize: (info) => {
                            this.updateEventDates(info.event);
                        }
                    });
                    this.calendar.render();
                });
            },

            getEventsUrl() {
                if (this.teamScope === 'all') return '/api/events';
                return '/api/events?team_id=' + this.teamScope;
            },

            getResourcesUrl() {
                if (this.teamScope === 'all') return '/api/resources';
                return '/api/resources?team_id=' + this.teamScope;
            },

            setTeamScope(scope) {
                this.teamScope = scope;
                if (this.calendar) {
                    const eventSource = this.calendar.getEventSources()[0];
                    if (eventSource) eventSource.remove();
                    this.calendar.addEventSource(this.getEventsUrl());
                    if (typeof this.calendar.refetchResources === 'function') {
                        this.calendar.refetchResources();
                    }
                }
            },

            switchView(viewName) {
                this.currentView = viewName;
                if (this.calendar) {
                    this.calendar.changeView(viewName);
                }
            },

            hasDocuments() {
                const docs = this.selectedEvent?.extendedProps?.documents || {};
                const links = this.selectedEvent?.extendedProps?.documents_links || {};
                const hasDoc = Object.values(docs).some(v => v === true || v === 'true' || v === 1);
                const hasLink = Object.values(links).some(link => !!link);
                return hasDoc || hasLink;
            },

            async updateEventDates(event) {
                try {
                    await fetch(`/api/activities/${event.id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            start: event.start.toISOString(),
                            end: event.end ? event.end.toISOString() : event.start.toISOString()
                        })
                    });
                } catch (e) {
                    console.error('Gagal memperbarui tanggal', e);
                }
            },

            async deleteEvent(id) {
                if (!confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')) return;
                try {
                    const res = await fetch(`/api/activities/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    if (res.ok) {
                        this.showDetailModal = false;
                        if (this.calendar) {
                            this.calendar.refetchEvents();
                        } else {
                            window.location.reload();
                        }
                    } else {
                        const err = await res.json();
                        alert(err.message || 'Gagal menghapus kegiatan.');
                    }
                } catch (e) {
                    alert('Gagal menghapus kegiatan.');
                }
            },



            formatDateRange(start, end) {
                if (!start) return '-';
                const s = new Date(start).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
                if (!end) return s;
                const e = new Date(end).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
                return `${s} s/d ${e}`;
            },

            async readNotification(id) {
                try {
                    await fetch(`/api/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    // Highlight visually
                    let el = document.getElementById('notif-' + id);
                    if(el) {
                        el.classList.remove('bg-blue-50');
                        el.classList.add('bg-white', 'opacity-70');
                    }
                    
                    // Try to open event details if calendar has it
                    if (this.calendar) {
                        const eventObj = this.calendar.getEventById(id);
                        if (eventObj) {
                            this.selectedEvent = eventObj;
                            this.showDrawer = true;
                        } else {
                            // If event not visible in current month, just switch to it or reload
                            window.location.reload();
                        }
                    } else {
                        window.location.reload();
                    }
                } catch(e) {}
            },

            async deleteNotificationItem(id) {
                if(!confirm('Hapus notifikasi ini?')) return;
                try {
                    const res = await fetch(`/api/notifications/${id}/delete`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    if(res.ok) {
                        let el = document.getElementById('notif-' + id);
                        if(el) el.remove();
                    }
                } catch(e) {}
            }
        }
    }
</script>
@endsection
