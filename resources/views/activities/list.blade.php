@extends('layouts.app')

@section('title', 'Daftar Kegiatan — BPS ACT')
@section('header_title', 'Daftar Semua Kegiatan Tim BPS')

@section('content')
<div x-data="activityListApp()" class="space-y-4">
    <!-- Top Action Bar & Filters -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-2xs flex flex-wrap items-center justify-between gap-4">
        <!-- Filters -->
        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Team Filter -->
            <div class="relative w-full md:w-64">
                <select x-model="filters.team" class="w-full pl-3 pr-10 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-bps-blue/20 focus:border-bps-blue appearance-none transition shadow-xs text-gray-700 font-medium cursor-pointer"
                        style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22currentColor%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.25em 1.25em;">
                    <option value="all" class="text-gray-900 bg-white font-semibold">🌍 Semua Tim (Keseluruhan)</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div['id'] }}" class="text-gray-900 bg-white">{{ $div['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Status Filter -->
            <div class="relative w-full md:w-48">
                <select x-model="filters.status" class="w-full pl-3 pr-10 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-bps-blue/20 focus:border-bps-blue appearance-none transition shadow-xs text-gray-700 font-medium cursor-pointer"
                        style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22currentColor%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.293%207.293a1%201%200%20011.414%200L10%2010.586l3.293-3.293a1%201%200%20111.414%201.414l-4%204a1%201%200%2001-1.414%200l-4-4a1%201%200%20010-1.414z%22%20clip-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.25em 1.25em;">
                    <option value="all" class="text-gray-900 bg-white font-semibold">Semua Status</option>
                    <option value="planned" class="text-gray-900 bg-white">Planned / Terjadwal</option>
                    <option value="ongoing" class="text-gray-900 bg-white">Ongoing / Sedang Berjalan</option>
                    <option value="done" class="text-gray-900 bg-white">Done / Selesai</option>
                </select>
            </div>
        </div>

        <!-- Right Side: New Activity Action -->
        <div class="flex items-center w-full md:w-auto">
            <a href="{{ route('activities.create') }}" class="w-full md:w-auto px-4 py-2 bg-bps-blue hover:bg-bps-teal text-white font-semibold text-xs rounded-lg shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Kegiatan Baru
            </a>
        </div>
    </div>

    <!-- Activities Card List (Unified Card View) -->
    <div class="space-y-4 my-2">
        @forelse($activities as $act)
        @php
            $actId = $act['id'] ?? '';
            $actDiv = $act['division_id'] ?? '';
            $actStatusRaw = strtolower($act['status'] ?? 'planned');
            $actStatus = match($actStatusRaw) {
                'done', 'confirmed', 'closed' => 'done',
                'in progress', 'ongoing' => 'ongoing',
                default => 'planned'
            };
            
            $isCreator = ($act['created_by'] ?? ($act['createdBy'] ?? '')) === ($user['id'] ?? '');
            $isAdmin = in_array($user['role'] ?? '', ['admin', 'lead']);
            $canEdit = $isCreator || $isAdmin;

            $startObj = \Carbon\Carbon::parse($act['start'] ?? $act['start_date']);
            $hasTime = strlen($act['start'] ?? $act['start_date']) > 10 && !str_ends_with($act['start'] ?? $act['start_date'], '00:00:00') && !str_ends_with($act['start'] ?? $act['start_date'], '00:00:00Z');
            $startFmt = $hasTime ? $startObj->translatedFormat('d M Y H:i') : $startObj->translatedFormat('d M Y');

            $endFmt = null;
            if(isset($act['end']) || isset($act['due_date'])) {
                $endObj = \Carbon\Carbon::parse($act['end'] ?? $act['due_date']);
                $endHasTime = strlen($act['end'] ?? $act['due_date']) > 10 && !str_ends_with($act['end'] ?? $act['due_date'], '00:00:00') && !str_ends_with($act['end'] ?? $act['due_date'], '00:00:00Z');
                $endFmt = $endHasTime ? $endObj->translatedFormat('d M Y H:i') : $endObj->translatedFormat('d M Y');
            }

            $actJson = json_encode($act, JSON_HEX_APOS | JSON_HEX_QUOT);
            
            $statusBg = 'bg-gray-100 text-gray-700';
            if($actStatus === 'done') $statusBg = 'bg-green-100 text-green-700';
            elseif($actStatus === 'ongoing') $statusBg = 'bg-amber-100 text-amber-700';
            elseif($actStatus === 'planned') $statusBg = 'bg-blue-100 text-blue-700';
        @endphp
        <div class="bg-white p-5 sm:p-6 rounded-xl border border-gray-200 shadow-xs space-y-4 activity-row hover:border-bps-blue/40 transition"
             x-show="matchesFilters('{{ $actDiv }}', '{{ $actStatus }}')">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded text-[10px] font-semibold border border-gray-200 uppercase tracking-wider">{{ $act['category'] ?? '-' }}</span>
                        <span class="bg-blue-50 text-bps-blue px-2.5 py-0.5 rounded text-[10px] font-semibold border border-blue-100 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ $act['creator_team'] ?? 'Tim Umum' }}
                        </span>
                    </div>
                    <h3 class="font-bold text-gray-900 text-base mt-2 leading-snug">{{ $act['title'] ?? $act['subject'] ?? 'Tanpa Judul' }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Dibuat oleh <span class="font-medium text-gray-700">{{ $act['creator_name'] ?? 'Sistem' }}</span></p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $statusBg }} shrink-0">
                    {{ $act['status'] ?? 'Planned' }}
                </span>
            </div>

            <div class="space-y-2 text-xs text-gray-600 bg-gray-50/80 p-3.5 rounded-lg border border-gray-100">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="truncate font-medium text-gray-700">{{ $act['location'] ?? '-' }}</span>
                </div>
                <div class="flex items-center gap-2 font-medium text-gray-800">
                    <svg class="w-4 h-4 text-bps-blue shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ $startFmt }} @if($endFmt) s/d {{ $endFmt }} @endif</span>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100 flex-wrap gap-3">
                <!-- Assignees preview -->
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400 font-medium hidden sm:inline">Penugasan:</span>
                    <div class="flex -space-x-2 overflow-hidden items-center">
                        @foreach(array_slice($act['assignees_rich'] ?? [], 0, 5) as $u)
                            @if($u['avatar'])
                                <img class="inline-block rounded-full ring-2 ring-white object-cover" src="{{ $u['avatar'] }}" alt="{{ $u['name'] }}" title="{{ $u['name'] }} ({{ $u['team'] }})" style="width: 28px; height: 28px; min-width: 28px; min-height: 28px; max-width: 28px; max-height: 28px; object-fit: cover;">
                            @else
                                <div class="inline-flex items-center justify-center rounded-full ring-2 ring-white bg-bps-blue text-white text-[10px] font-bold" style="width: 28px; height: 28px; min-width: 28px; min-height: 28px;" title="{{ $u['name'] }} ({{ $u['team'] }})">
                                    {{ $u['initials'] }}
                                </div>
                            @endif
                        @endforeach
                        @if(count($act['assignees_rich'] ?? []) > 5)
                            <div class="inline-flex items-center justify-center rounded-full ring-2 ring-white bg-gray-100 text-gray-600 text-xs font-bold z-10" style="width: 28px; height: 28px;">
                                +{{ count($act['assignees_rich']) - 5 }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center gap-2 ml-auto">
                    <button @click="openDetailModal({{ $actJson }})" class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-blue-50 text-bps-blue hover:bg-blue-100 text-xs font-semibold rounded-lg transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Detail
                    </button>
                    @if($canEdit)
                    <a href="{{ route('activities.edit', $actId) }}" class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-semibold rounded-lg transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit
                    </a>
                    <button @click="deleteActivity('{{ $actId }}')" class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-red-50 text-red-700 hover:bg-red-100 text-xs font-semibold rounded-lg transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white p-8 rounded-xl border border-gray-200 text-center text-gray-500 text-sm">
            Belum ada kegiatan yang terdaftar.
        </div>
        @endforelse

        <!-- Empty State for Filters -->
        <div x-show="!hasVisibleRows" x-cloak class="bg-white p-8 rounded-xl border border-gray-200 text-center text-gray-500 text-sm">
            Tidak ada kegiatan yang cocok dengan filter yang dipilih.
        </div>
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
                        <span class="text-[10px] uppercase tracking-widest font-bold bg-bps-blue/10 px-2 py-0.5 rounded text-bps-blue" x-text="'ID #' + (selectedActivity?.id || '')"></span>
                        <h2 class="text-lg font-bold mt-1 text-gray-900" x-text="selectedActivity?.title || selectedActivity?.subject || 'Detail Kegiatan'"></h2>
                        <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-500 font-medium">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span x-text="selectedActivity?.creator_team || 'Tim Umum'"></span>
                            </span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span x-text="'Dibuat oleh ' + (selectedActivity?.creator_name || 'Sistem')"></span>
                            </span>
                        </div>
                    </div>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-red-500 transition focus:outline-none p-1 rounded-full hover:bg-red-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="mt-5 space-y-5">
                    <!-- Schedule Info -->
                    <div class="bg-gray-50/50 p-4 rounded-lg border border-gray-100 flex items-start gap-3">
                        <div class="bg-white p-2 rounded shadow-sm text-bps-blue border border-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Jadwal Pelaksanaan</p>
                            <p class="text-sm font-bold text-gray-900 mt-0.5" x-text="formatDateTime(selectedActivity?.start || selectedActivity?.start_date)"></p>
                            <p class="text-xs text-gray-500 font-medium mt-0.5" x-show="selectedActivity?.end || selectedActivity?.due_date" x-text="'s/d ' + formatDateTime(selectedActivity?.end || selectedActivity?.due_date)"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                  :class="{
                                    'bg-green-100 text-green-800': ['done', 'confirmed', 'closed'].includes((selectedActivity?.status || '').toLowerCase()),
                                    'bg-yellow-100 text-yellow-800': ['ongoing', 'in progress'].includes((selectedActivity?.status || '').toLowerCase()),
                                    'bg-blue-100 text-blue-800': ['planned', 'in specification', 'new'].includes((selectedActivity?.status || '').toLowerCase()),
                                    'bg-gray-100 text-gray-800': !selectedActivity?.status
                                  }"
                                  x-text="selectedActivity?.status || 'Planned'">
                            </span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Kategori</p>
                            <p class="text-sm text-gray-900 font-medium" x-text="selectedActivity?.category || '-'"></p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Lokasi Kegiatan</p>
                            <p class="text-sm text-gray-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span x-text="selectedActivity?.location || '-'"></span>
                            </p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Deskripsi & Catatan</p>
                            <div class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3 border border-gray-100 min-h-[60px] whitespace-pre-wrap break-words" x-text="selectedActivity?.description || 'Tidak ada deskripsi yang dicantumkan.'"></div>
                        </div>

                        <!-- Documents Links -->
                        <template x-if="selectedActivity?.documents_links && selectedActivity.documents_links.length > 0">
                            <div class="col-span-2">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Dokumen / Tautan</p>
                                <div class="space-y-2">
                                    <template x-for="(doc, idx) in selectedActivity.documents_links" :key="idx">
                                        <a :href="doc" target="_blank" class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 bg-blue-50/50 hover:bg-blue-50 p-2 rounded border border-blue-100 transition truncate">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            <span x-text="doc"></span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Assignees -->
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 flex items-center justify-between">
                            <span>Tim Penugasan</span>
                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[9px]" x-text="(selectedActivity?.assignees_rich?.length || 0) + ' Orang'"></span>
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[160px] overflow-y-auto pr-2 custom-scrollbar">
                            <template x-if="!selectedActivity?.assignees_rich || selectedActivity.assignees_rich.length === 0">
                                <p class="text-xs text-gray-500 italic col-span-2">Belum ada pegawai yang ditugaskan pada kegiatan ini.</p>
                            </template>
                            <template x-for="user in selectedActivity?.assignees_rich" :key="user.name">
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
                    <template x-if="(selectedActivity?.created_by === '{{ $user['id'] ?? '' }}') || ['admin', 'lead'].includes('{{ $user['role'] ?? '' }}')">
                        <a :href="'/activities/' + selectedActivity?.id + '/edit'" class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg border border-transparent px-4 py-2 bg-bps-blue text-xs font-semibold text-white shadow-xs hover:bg-bps-teal focus:outline-none transition cursor-pointer gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Edit Kegiatan
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('activityListApp', () => ({
        filters: {
            team: 'all',
            status: 'all'
        },
        hasVisibleRows: true,
        showDetailModal: false,
        selectedActivity: null,

        init() {
            this.$watch('filters', () => {
                this.$nextTick(() => {
                    this.checkVisibleRows();
                });
            }, { deep: true });
        },

        matchesFilters(actDiv, actStatus) {
            let matchTeam = this.filters.team === 'all' || actDiv === this.filters.team;
            let matchStatus = this.filters.status === 'all' || actStatus === this.filters.status;
            return matchTeam && matchStatus;
        },

        checkVisibleRows() {
            const rows = document.querySelectorAll('.activity-row');
            let visible = false;
            rows.forEach(r => {
                if(r.style.display !== 'none') visible = true;
            });
            this.hasVisibleRows = visible;
        },

        openDetailModal(activity) {
            this.selectedActivity = activity;
            this.showDetailModal = true;
        },

        formatDateTime(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return dateStr;
            
            const formatter = new Intl.DateTimeFormat('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });
            
            let result = formatter.format(d);
            // If the time is exactly 00:00 (default for all day dates without time)
            if (dateStr.length === 10 || dateStr.endsWith('00:00:00') || dateStr.endsWith('00:00:00Z')) {
                const dateOnlyFormatter = new Intl.DateTimeFormat('id-ID', {
                    day: '2-digit', month: 'short', year: 'numeric'
                });
                return dateOnlyFormatter.format(d);
            }
            return result.replace('.', ':');
        },

        async deleteActivity(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')) return;
            try {
                const res = await fetch(`/api/activities/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if(res.ok) {
                    window.location.reload();
                } else {
                    alert('Gagal menghapus kegiatan.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
            }
        }
    }));
});
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endsection
