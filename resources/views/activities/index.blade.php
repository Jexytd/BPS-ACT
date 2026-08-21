@extends('layouts.app')

@section('title', 'Kalender & Team Planner — BPS ACT')
@section('header_title', 'Pencatatan & Visualisasi Kalender Kegiatan Tim BPS')

@section('content')
<div x-data="teamPlannerApp()" x-init="initPlanner()" class="space-y-4">
    <!-- Top Action Bar & View Switcher -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-2xs flex flex-wrap items-center justify-between gap-4">
        <!-- View Mode Switcher -->
        <div class="flex items-center gap-2 bg-gray-100 p-1 rounded-lg">
            <button @click="switchView('resourceTimelineWeek')" 
                    :class="currentView === 'resourceTimelineWeek' ? 'bg-bps-blue text-white shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                    class="px-3 py-1.5 rounded-md text-xs transition cursor-pointer flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Kalender Mingguan
            </button>
            <button @click="switchView('dayGridMonth')" :class="currentView === 'dayGridMonth' ? 'bg-bps-blue text-white shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'" class="px-3 py-1.5 rounded-md text-xs transition cursor-pointer flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Kalender Bulanan
            </button>
            <button @click="switchView('timeGridDay')" 
                    :class="currentView === 'timeGridDay' ? 'bg-bps-blue text-white shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                    class="px-3 py-1.5 rounded-md text-xs transition cursor-pointer flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Kalender Harian
            </button>
            <a href="{{ route('activities.list') }}" class="px-3 py-1.5 rounded-md text-xs transition cursor-pointer flex items-center gap-1.5 text-gray-600 hover:text-gray-900 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Daftar Kegiatan (Tabel)
            </a>
        </div>

        <!-- Right Side: New Activity Action -->
        <div class="flex items-center gap-3">
            <a href="{{ route('activities.create') }}" class="px-4 py-2 bg-bps-blue hover:bg-bps-teal text-white font-semibold text-xs rounded-lg shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Kegiatan Baru
            </a>
        </div>
    </div>

    <!-- Calendar & Timeline Container -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-2xs p-4 relative min-h-[600px]">
        <!-- Loading Skeleton -->
        <div x-show="loading" class="absolute inset-0 bg-white/80 z-20 flex items-center justify-center backdrop-blur-xs">
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-lg">
                <svg class="w-5 h-5 text-bps-blue animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-xs font-semibold text-gray-700">Memuat Jadwal Kegiatan Tim BPS...</span>
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
    function teamPlannerApp() {
        return {
            loading: true,
            currentView: 'resourceTimelineWeek',
            calendar: null,
            showDetailModal: false,
            selectedEvent: null,

            initPlanner() {
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
                        initialView: 'resourceTimelineWeek',
                        locales: [window.FullCalendar.idLocale],
                        locale: 'id',
                        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'resourceTimelineWeek,dayGridMonth,timeGridDay'
                        },
                        nowIndicator: true,
                        allDaySlot: true,
                        weekends: false,
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
                                let d = new Date(info.startStr);
                                d.setHours(d.getHours() + 1);
                                endStr = d.toISOString();
                            }
                            window.location.href = "{{ route('activities.create') }}?start=" + encodeURIComponent(info.startStr) + "&end=" + encodeURIComponent(endStr);
                        },
                        resourceGroupField: 'group',
                        resources: '/api/resources',
                        events: '/api/events',
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
                    console.error('Failed to update event dates', e);
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
                        this.calendar.refetchEvents();
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
            }
        }
    }
</script>
@endsection
