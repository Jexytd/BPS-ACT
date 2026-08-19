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
                    $creator = collect($users)->firstWhere('id', $feed['createdBy']);
                    $isRead = in_array($user['id'], $feed['readBy'] ?? []);
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
        <!-- Team Scope Filters -->
        <div class="flex items-center gap-2 bg-gray-100 p-1.5 rounded-lg">
            <button @click="setTeamScope('{{ $user['division_id'] ?? '' }}')" 
                    :class="teamScope === '{{ $user['division_id'] ?? '' }}' ? 'bg-bps-blue text-white shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                    class="px-4 py-2 rounded-md text-sm transition cursor-pointer flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Kegiatan Tim Saya
            </button>
            <button @click="setTeamScope('all')" 
                    :class="teamScope === 'all' ? 'bg-bps-blue text-white shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                    class="px-4 py-2 rounded-md text-sm transition cursor-pointer flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                Semua Tim BPS
            </button>
        </div>

    </div>

    <!-- Calendar Container -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-2xs p-4 relative min-h-[500px]">
        <!-- Loading Skeleton -->
        <div x-show="loading" class="absolute inset-0 bg-white/80 z-20 flex items-center justify-center backdrop-blur-xs">
            <div class="flex items-center gap-3 px-5 py-4 bg-white border border-gray-200 rounded-xl shadow-xl">
                <svg class="w-6 h-6 text-bps-blue animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-sm font-bold text-gray-800">Memuat Jadwal Tim...</span>
            </div>
        </div>

        <!-- FullCalendar Mounting Point -->
        <div id="calendar" class="w-full"></div>
    </div>

    <!-- Activity Creation / Editing Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/50" @click="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-2xl border border-gray-200 sm:my-8">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900" x-text="'Edit Kegiatan BPS #' + form.id"></h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Real-Time Conflict Warning Banner -->
                <div x-show="conflictWarning" class="mt-4 p-3 bg-amber-50 border border-amber-300 rounded-lg text-amber-900 text-sm flex items-start gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="font-bold">Peringatan Bentrok Jadwal!</p>
                        <p class="mt-0.5" x-text="conflictMessage"></p>
                    </div>
                </div>

                <form @submit.prevent="submitForm()" class="mt-4 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Nama Kegiatan *</label>
                        <input type="text" x-model="form.title" required placeholder="Contoh: Rapat Koordinasi Tim" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Waktu Mulai *</label>
                            <input type="datetime-local" x-model="form.start" @change="checkConflicts()" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Waktu Selesai *</label>
                            <input type="datetime-local" x-model="form.end" @change="checkConflicts()" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Penanggung Jawab / Anggota *</label>
                        <select multiple x-model="form.assignees" @change="checkConflicts()" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm min-h-[100px]">
                            @foreach($users as $u)
                            <option value="{{ $u['id'] }}">{{ $u['name'] }} ({{ strtoupper($u['role']) }})</option>
                            @endforeach
                        </select>
                        <span class="text-xs text-gray-500 font-medium inline-block mt-1">Gunakan Ctrl/Cmd untuk memilih lebih dari 1 anggota tim.</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Status</label>
                            <select x-model="form.status" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm">
                                <option value="planned">Direncanakan</option>
                                <option value="ongoing">Sedang Berjalan</option>
                                <option value="done">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Kategori</label>
                            <select x-model="form.category" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm">
                                <option value="Survei">Survei</option>
                                <option value="Sensus">Sensus</option>
                                <option value="Pengolahan">Pengolahan</option>
                                <option value="Rapat">Rapat / Koordinasi</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Lokasi / Platform</label>
                        <input type="text" x-model="form.location" placeholder="Contoh: Ruang Rapat 302 / Zoom" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Deskripsi / Catatan Tambahan</label>
                        <textarea x-model="form.description" rows="3" placeholder="Rincian agenda..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-gray-200">
                        <button type="button" @click="closeModal()" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-bold rounded-lg transition">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-bps-blue hover:bg-bps-teal text-white text-sm font-bold rounded-lg shadow-md transition">Simpan Kegiatan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Activity Detail Slide-Over Drawer -->
    <div x-show="showDrawer" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="showDrawer = false"></div>
        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white border-l border-gray-200 shadow-2xl flex flex-col">
                <div class="p-6 bg-bps-blue text-white flex items-center justify-between">
                    <div>
                        <span class="text-xs uppercase tracking-widest font-bold bg-white/20 px-2.5 py-1 rounded-md text-white" x-text="'ID #' + selectedEvent?.id"></span>
                        <h2 class="text-xl font-bold mt-2 text-white leading-tight" x-text="selectedEvent?.title"></h2>
                    </div>
                    <button @click="showDrawer = false" class="text-white/80 hover:text-white p-1 rounded-full hover:bg-white/10 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="flex-1 p-6 space-y-6 overflow-y-auto">
                    <div>
                        <label class="text-xs uppercase font-bold text-gray-500 tracking-wider">Status Kegiatan</label>
                        <div class="mt-2">
                            <span class="px-3 py-1.5 text-sm font-bold rounded-full text-white shadow-sm" 
                                  :style="'background-color:' + selectedEvent?.backgroundColor"
                                  x-text="selectedEvent?.extendedProps?.status"></span>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs uppercase font-bold text-gray-500 tracking-wider">Rentang Waktu</label>
                        <p class="text-sm font-bold text-gray-900 mt-1" x-text="formatDateRange(selectedEvent?.start, selectedEvent?.end)"></p>
                    </div>

                    <div>
                        <label class="text-xs uppercase font-bold text-gray-500 tracking-wider">Lokasi / Platform</label>
                        <p class="text-sm font-semibold text-gray-800 mt-1" x-text="selectedEvent?.extendedProps?.location || '-'"></p>
                    </div>

                    <div>
                        <label class="text-xs uppercase font-bold text-gray-500 tracking-wider">Pembuat & Tim Asal</label>
                        <p class="text-sm font-bold text-gray-900 mt-1">
                            <span x-text="selectedEvent?.extendedProps?.creator_name"></span> 
                            <span class="text-bps-blue font-semibold" x-show="selectedEvent?.extendedProps?.creator_team">
                                (<span x-text="selectedEvent?.extendedProps?.creator_team"></span>)
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="text-xs uppercase font-bold text-gray-500 tracking-wider">Anggota Tim Bertugas</label>
                        <div class="mt-1 flex flex-wrap gap-2">
                            <template x-for="name in (selectedEvent?.extendedProps?.assignees_names || [])" :key="name">
                                <span class="px-2.5 py-1 bg-gray-100 border border-gray-200 text-gray-800 text-xs font-semibold rounded-md shadow-sm" x-text="'👤 ' + name"></span>
                            </template>
                            <span x-show="!(selectedEvent?.extendedProps?.assignees_names?.length)" class="text-sm text-gray-500 italic">Belum ada anggota yang ditugaskan.</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs uppercase font-bold text-gray-500 tracking-wider">Catatan Tambahan</label>
                        <p class="text-sm text-gray-700 mt-1 leading-relaxed bg-blue-50/50 p-4 rounded-xl border border-blue-100" x-text="selectedEvent?.extendedProps?.description || 'Tidak ada catatan.'"></p>
                    </div>
                </div>

                <div class="p-5 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-3">
                    <button @click="deleteEvent(selectedEvent?.id)" class="flex-1 py-3 bg-white text-rose-600 hover:bg-rose-50 font-bold text-sm rounded-xl transition border border-rose-200 shadow-sm">
                        Hapus
                    </button>
                    <button @click="editCurrentEvent()" class="flex-1 py-3 bg-bps-blue text-white hover:bg-bps-teal font-bold text-sm rounded-xl transition shadow-md">
                        Ubah Info
                    </button>
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
            currentView: 'dayGridMonth',
            teamScope: '{{ $user['division_id'] ?? '' }}',
            calendar: null,
            showModal: false,
            showDrawer: false,
            conflictWarning: false,
            conflictMessage: '',
            selectedEvent: null,
            form: {
                id: null,
                title: '',
                start: '',
                end: '',
                assignees: [],
                status: 'planned',
                category: 'Survei',
                location: '',
                description: ''
            },

            initDashboard() {
                this.$nextTick(() => {
                    const calendarEl = document.getElementById('calendar');
                    this.calendar = new window.FullCalendar.Calendar(calendarEl, {
                        plugins: [
                            window.FullCalendar.dayGridPlugin,
                            window.FullCalendar.timeGridPlugin,
                            window.FullCalendar.interactionPlugin,
                            window.FullCalendar.resourceTimelinePlugin
                        ],
                        initialView: this.currentView,
                        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: ''
                        },
                        dayMaxEvents: 2,
                        eventContent: function(arg) {
                            let wrapper = document.createElement('div');
                            wrapper.classList.add('p-1', 'text-xs', 'leading-tight', 'w-full', 'overflow-hidden');
                            
                            let title = document.createElement('div');
                            title.classList.add('font-bold', 'truncate');
                            title.innerText = arg.event.title;
                            wrapper.appendChild(title);

                            let assignees = arg.event.extendedProps.assignees_names;
                            let personName = (assignees && assignees.length > 0) ? assignees[0] : arg.event.extendedProps.creator_name;
                            
                            if (personName) {
                                let person = document.createElement('div');
                                person.classList.add('truncate', 'mt-0.5', 'text-[10px]', 'opacity-90');
                                person.innerText = '👤 ' + personName;
                                wrapper.appendChild(person);
                            }
                            return { domNodes: [wrapper] };
                        },
                        editable: true,
                        selectable: true,
                        resourceGroupField: 'group',
                        resources: '/api/resources',
                        events: this.getEventsUrl(),
                        loading: (isLoading) => {
                            this.loading = isLoading;
                        },
                        eventClick: (info) => {
                            this.selectedEvent = info.event;
                            this.showDrawer = true;
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

            setTeamScope(scope) {
                this.teamScope = scope;
                if (this.calendar) {
                    const eventSource = this.calendar.getEventSources()[0];
                    if (eventSource) eventSource.remove();
                    this.calendar.addEventSource(this.getEventsUrl());
                }
            },

            switchView(viewName) {
                this.currentView = viewName;
                if (this.calendar) {
                    this.calendar.changeView(viewName);
                }
            },

            openModal(data = null) {
                if (data) {
                    this.form = { ...data };
                } else {
                    window.location.href = "{{ route('activities.create') }}";
                    return;
                }
                this.conflictWarning = false;
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
            },

            async checkConflicts() {
                if (!this.form.start || !this.form.end || !this.form.assignees.length) return;
                try {
                    const res = await fetch('/api/check-conflicts', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            start: this.form.start,
                            end: this.form.end,
                            assignees: this.form.assignees,
                            exclude_id: this.form.id
                        })
                    });
                    const data = await res.json();
                    if (data.has_conflict) {
                        this.conflictWarning = true;
                        this.conflictMessage = `Anggota terpilih bentrok dengan: ${data.conflicts[0].activity_title}`;
                    } else {
                        this.conflictWarning = false;
                    }
                } catch (e) {
                    console.error(e);
                }
            },

            async submitForm() {
                const url = this.form.id ? `/api/activities/${this.form.id}` : '/api/activities';
                const method = this.form.id ? 'PATCH' : 'POST';

                try {
                    const res = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.form)
                    });
                    if (res.ok) {
                        this.closeModal();
                        window.location.reload(); // Refresh to update feed
                    } else {
                        const err = await res.json();
                        alert(err.message || 'Gagal menyimpan kegiatan.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan jaringan.');
                }
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
                    window.location.reload();
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
                        this.showDrawer = false;
                        window.location.reload();
                    } else {
                        const err = await res.json();
                        alert(err.message || 'Gagal menghapus.');
                    }
                } catch (e) {
                    alert('Gagal menghapus kegiatan.');
                }
            },

            editCurrentEvent() {
                if (!this.selectedEvent) return;
                const e = this.selectedEvent;
                this.form = {
                    id: e.id,
                    title: e.title,
                    start: e.start.toISOString().slice(0, 16),
                    end: e.end ? e.end.toISOString().slice(0, 16) : e.start.toISOString().slice(0, 16),
                    assignees: e.extendedProps.assignees || [],
                    status: e.extendedProps.status || 'planned',
                    category: e.extendedProps.category || 'Survei',
                    location: e.extendedProps.location || '',
                    description: e.extendedProps.description || ''
                };
                this.showDrawer = false;
                this.showModal = true;
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
