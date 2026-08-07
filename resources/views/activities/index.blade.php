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
                Resource Timeline
            </button>
            <button @click="switchView('dayGridMonth')" 
                    :class="currentView === 'dayGridMonth' ? 'bg-bps-blue text-white shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                    class="px-3 py-1.5 rounded-md text-xs transition cursor-pointer flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Kalender Bulanan
            </button>
            <button @click="switchView('timeGridWeek')" 
                    :class="currentView === 'timeGridWeek' ? 'bg-bps-blue text-white shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                    class="px-3 py-1.5 rounded-md text-xs transition cursor-pointer flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Kalender Mingguan
            </button>
        </div>

        <!-- Right Side: New Activity Action -->
        <div class="flex items-center gap-3">
            <button @click="openModal()" class="px-4 py-2 bg-bps-blue hover:bg-bps-teal text-white font-semibold text-xs rounded-lg shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Kegiatan Baru
            </button>
        </div>
    </div>

    <!-- Calendar & Timeline Container -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-2xs p-4 relative min-h-[600px]">
        <!-- Loading Skeleton -->
        <div x-show="loading" class="absolute inset-0 bg-white/80 z-20 flex items-center justify-center backdrop-blur-xs">
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-lg">
                <svg class="w-5 h-5 text-bps-blue animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-xs font-semibold text-gray-700">Memuat Jadwal Kegiatan Firestore...</span>
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
                    <h3 class="text-base font-bold text-gray-900" x-text="form.id ? 'Edit Kegiatan BPS #' + form.id : 'Input Kegiatan / Aktivitas Tim Baru'"></h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Real-Time Conflict Warning Banner -->
                <div x-show="conflictWarning" class="mt-4 p-3 bg-amber-50 border border-amber-300 rounded-lg text-amber-800 text-xs flex items-start gap-2">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="font-bold">Peringatan Bentrok Jadwal!</p>
                        <p class="mt-0.5" x-text="conflictMessage"></p>
                    </div>
                </div>

                <form @submit.prevent="submitForm()" class="mt-4 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Judul Kegiatan / Work Package *</label>
                        <input type="text" x-model="form.title" required placeholder="Contoh: Survei Angkatan Kerja Nasional" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-bps-blue focus:border-bps-blue">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Waktu Mulai *</label>
                            <input type="datetime-local" x-model="form.start" @change="checkConflicts()" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-bps-blue focus:border-bps-blue">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Batas Waktu Selesai *</label>
                            <input type="datetime-local" x-model="form.end" @change="checkConflicts()" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-bps-blue focus:border-bps-blue">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Penanggung Jawab / Assignees *</label>
                        <select multiple x-model="form.assignees" @change="checkConflicts()" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-bps-blue focus:border-bps-blue min-h-[90px]">
                            @foreach($users as $u)
                            <option value="{{ $u['id'] }}">{{ $u['name'] }} ({{ strtoupper($u['role']) }})</option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-gray-400">Tekan Ctrl/Cmd untuk memilih lebih dari 1 anggota tim.</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Status Kegiatan *</label>
                            <select x-model="form.status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-bps-blue focus:border-bps-blue">
                                <option value="planned">Planned / In specification</option>
                                <option value="ongoing">Ongoing / In progress</option>
                                <option value="done">Done / Confirmed</option>
                                <option value="cancelled">Cancelled / Closed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Kategori Activity</label>
                            <select x-model="form.category" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-bps-blue focus:border-bps-blue">
                                <option value="Survei">Survei</option>
                                <option value="Sensus">Sensus</option>
                                <option value="Pengolahan">Pengolahan</option>
                                <option value="Rapat">Rapat / Koordinasi</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Lokasi / Ruang / Platform</label>
                        <input type="text" x-model="form.location" placeholder="Contoh: Ruang Rapat 302 / Zoom BPS" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-bps-blue focus:border-bps-blue">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Deskripsi Catatan Kegiatan</label>
                        <textarea x-model="form.description" rows="3" placeholder="Rincian agenda atau catatan penting..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-bps-blue focus:border-bps-blue"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" @click="closeModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-bps-blue hover:bg-bps-teal text-white text-xs font-semibold rounded-lg shadow-xs transition">Simpan Kegiatan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Activity Detail Slide-Over Drawer -->
    <div x-show="showDrawer" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
        <div class="absolute inset-0 bg-gray-900/30" @click="showDrawer = false"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white border-l border-gray-200 shadow-2xl flex flex-col">
                <!-- Drawer Header -->
                <div class="p-6 bg-bps-blue text-white flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase tracking-widest font-bold bg-white/20 px-2 py-0.5 rounded text-white" x-text="'ID #' + selectedEvent?.id"></span>
                        <h2 class="text-base font-bold mt-1 text-white" x-text="selectedEvent?.title"></h2>
                    </div>
                    <button @click="showDrawer = false" class="text-white/80 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Drawer Content Details -->
                <div class="flex-1 p-6 space-y-5 overflow-y-auto">
                    <div>
                        <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Status Kegiatan</label>
                        <div class="mt-1">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full text-white" 
                                  :style="'background-color:' + selectedEvent?.backgroundColor"
                                  x-text="selectedEvent?.extendedProps?.status"></span>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Rentang Waktu</label>
                        <p class="text-xs font-medium text-gray-800 mt-1" x-text="formatDateRange(selectedEvent?.start, selectedEvent?.end)"></p>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Lokasi / Platform</label>
                        <p class="text-xs text-gray-700 mt-1" x-text="selectedEvent?.extendedProps?.location || '-'"></p>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Catatan Deskripsi</label>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100" x-text="selectedEvent?.extendedProps?.description || 'Tidak ada catatan.'"></p>
                    </div>
                </div>

                <!-- Drawer Footer Actions -->
                <div class="p-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                    <button @click="deleteEvent(selectedEvent?.id)" class="px-3 py-2 bg-rose-50 text-rose-600 hover:bg-rose-100 font-semibold text-xs rounded-lg transition border border-rose-200">
                        Hapus Kegiatan
                    </button>
                    <button @click="editCurrentEvent()" class="px-4 py-2 bg-bps-blue text-white hover:bg-bps-teal font-semibold text-xs rounded-lg transition shadow-xs">
                        Edit Informasi
                    </button>
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

            initPlanner() {
                this.$nextTick(() => {
                    const calendarEl = document.getElementById('calendar');
                    this.calendar = new window.FullCalendar.Calendar(calendarEl, {
                        plugins: [
                            window.FullCalendar.dayGridPlugin,
                            window.FullCalendar.timeGridPlugin,
                            window.FullCalendar.interactionPlugin,
                            window.FullCalendar.resourceTimelinePlugin
                        ],
                        initialView: 'resourceTimelineWeek',
                        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'resourceTimelineWeek,dayGridMonth,timeGridWeek'
                        },
                        editable: true,
                        selectable: true,
                        resourceGroupField: 'group',
                        resources: '/api/resources',
                        events: '/api/events',
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
                    const now = new Date();
                    const end = new Date(now.getTime() + 2 * 3600 * 1000);
                    this.form = {
                        id: null,
                        title: '',
                        start: now.toISOString().slice(0, 16),
                        end: end.toISOString().slice(0, 16),
                        assignees: ['usr_catherine'],
                        status: 'planned',
                        category: 'Survei',
                        location: 'BPS HQ',
                        description: ''
                    };
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
                        this.conflictMessage = `Petugas terpilih sudah memiliki agenda lain (${data.conflicts[0].activity_title}) pada rentang waktu yang sama!`;
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
                        this.calendar.refetchEvents();
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
                        this.showDrawer = false;
                        this.calendar.refetchEvents();
                    } else {
                        const err = await res.json();
                        alert(err.message || 'Gagal menghapus kegiatan.');
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
            }
        }
    }
</script>
@endsection
