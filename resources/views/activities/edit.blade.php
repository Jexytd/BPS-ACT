@extends('layouts.app')

@section('title', 'Edit Kegiatan — BPS ACT')
@section('header_title', 'Edit Kegiatan / Aktivitas Tim')

@section('content')
<div x-data="activityWizard" x-init="initWizard" class="max-w-4xl mx-auto py-6 px-4 sm:px-6">

    <div class="mb-14 sm:mb-16">
        <div class="relative">

            <!-- Background Line -->
            <div
                class="absolute h-1 bg-gray-200 z-0 rounded-full"
                style="top: 1.5rem; transform: translateY(50%); left: 1.5rem; right: 1.5rem;">
            </div>

            <!-- Active Progress Line -->
            <div
                class="absolute h-1 bg-bps-blue z-0 rounded-full transition-all duration-500 ease-in-out"
                :style="`
                    top: 1.5rem;
                    transform: translateY(50%);
                    left: 1.5rem;
                    width: calc(${((step - 1) / 3) * 100}% - ${((step - 1) / 3) * 3}rem);
                `">
            </div>

            <!-- Steps -->
            <div class="relative z-10 flex items-start justify-between">
                <template x-for="i in 4" :key="i">

                    <div class="flex flex-col items-center cursor-pointer group" @click="step = i">

                        <!-- Number -->
                        <div
                            :class="step >= i
                                ? 'bg-bps-blue text-white border-bps-blue shadow-md group-hover:bg-bps-teal'
                                : 'bg-white text-gray-400 border-gray-200 group-hover:border-bps-blue group-hover:text-bps-blue'"
                            class="w-12 h-12 rounded-full
                                flex items-center justify-center
                                font-bold border-2
                                transition-colors duration-300">
                            <span x-text="i"></span>
                        </div>

                        <!-- Label -->
                        <span
                            class="text-xs font-semibold mt-3 whitespace-nowrap"
                            :class="step >= i
                                ? 'text-bps-blue font-bold'
                                : 'text-gray-400'"
                            x-text="stepLabels[i-1]">
                        </span>

                    </div>

                </template>
            </div>
        </div>
    </div>

    <!-- Wizard Form -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mt-6">
        <form novalidate @submit.prevent="submitWizard" class="p-5 sm:p-8">

            <!-- Step 1: Informasi Dasar -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-6">Informasi Dasar Kegiatan</h2>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Judul Kegiatan / Work Package *</label>
                        <input type="text" x-model="form.title" required
                               placeholder="Contoh: Survei Angkatan Kerja Nasional (SAKERNAS)"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kategori Kegiatan *</label>
                        <select x-model="form.category" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition bg-white">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Sensus">Sensus</option>
                            <option value="Survei Rutin">Survei Rutin</option>
                            <option value="Survei Khusus">Survei Khusus / Ad-Hoc</option>
                            <option value="Rapat Koordinasi">Rapat Koordinasi</option>
                            <option value="FGD / Pelatihan">FGD / Pelatihan / Bimtek</option>
                            <option value="Administrasi">Administrasi / Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi & Tujuan</label>
                        <textarea x-model="form.description" rows="3"
                                  placeholder="Jelaskan secara singkat tujuan kegiatan ini..."
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition resize-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- Step 2: Jadwal & Lokasi -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-6">Jadwal & Lokasi Pelaksanaan</h2>

                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Waktu Mulai *</label>
                            <input type="datetime-local" x-model="form.start" required @change="validateWeekend($event, 'start')"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Batas Waktu Selesai *</label>
                            <input type="datetime-local" x-model="form.end" required @change="validateWeekend($event, 'end')"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Lokasi Pelaksanaan *</label>
                        <input type="text" x-model="form.location" required
                               placeholder="Contoh: BPS HQ / Ruang Rapat 302 / Zoom Meeting"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition">
                    </div>

                    <label for="allDay" class="flex items-center gap-2 mt-2 cursor-pointer w-fit">
                        <input type="checkbox" id="allDay" x-model="form.allDay"
                               class="w-4 h-4 text-bps-blue focus:ring-bps-blue border-gray-300 rounded">
                        <span class="text-sm text-gray-700 font-medium">Kegiatan Sepanjang Hari (All Day)</span>
                    </label>
                </div>
            </div>

            <!-- Step 3: Tim & Kelengkapan Dokumen -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-6">Tim Petugas & Kelengkapan BPS</h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Penanggung Jawab / Anggota Tim *</label>
                        <p class="text-xs text-gray-500 mb-2">Tahan tombol Ctrl/Cmd untuk memilih lebih dari satu.</p>
                        <select multiple x-model="form.assignees" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition min-h-[140px]">
                            @foreach($users as $u)
                                <option value="{{ $u['id'] }}">{{ $u['name'] }} ({{ strtoupper($u['role']) }} - {{ $divisions->get($u['division_id'] ?? '')['name'] ?? 'Umum' }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            <span x-text="form.assignees.length"></span> orang dipilih
                        </p>
                    </div>

                    <div class="bg-blue-50/60 p-4 sm:p-5 rounded-lg border border-blue-100">
                        <h3 class="font-bold text-gray-800 mb-4 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-bps-blue flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Kelengkapan Administrasi Kegiatan
                        </h3>

                        <div class="space-y-4">
                            <template x-for="(label, key) in documentTypes" :key="key">
                                <div class="flex items-start gap-3 bg-white/60 rounded-lg p-3 border border-blue-100/70">
                                    <input type="checkbox" :id="'doc_'+key" x-model="form.documents[key]"
                                           class="w-4 h-4 mt-1 text-bps-blue focus:ring-bps-blue border-gray-300 rounded flex-shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <label :for="'doc_'+key" class="text-sm font-medium text-gray-800 block cursor-pointer" x-text="label"></label>
                                        <input x-show="form.documents[key]" type="url" x-model="form.documents_links[key]"
                                               :placeholder="'Tautan ' + label + ' (Opsional, Google Drive/Doc)'"
                                               class="mt-2 w-full text-xs px-3 py-1.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Konfirmasi -->
            <div x-show="step === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-6">Konfirmasi Perubahan Kegiatan</h2>

                <div class="bg-gray-50 p-4 sm:p-5 rounded-lg border border-gray-200 text-sm divide-y divide-gray-200">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 py-3 first:pt-0">
                        <span class="text-gray-500 font-semibold">Judul Kegiatan</span>
                        <span class="sm:col-span-2 font-bold text-gray-900" x-text="form.title || '-'"></span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 py-3">
                        <span class="text-gray-500 font-semibold">Kategori</span>
                        <span class="sm:col-span-2 text-gray-900" x-text="form.category || '-'"></span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 py-3">
                        <span class="text-gray-500 font-semibold">Waktu</span>
                        <span class="sm:col-span-2 text-gray-900" x-text="formatDate(form.start) + ' s.d ' + formatDate(form.end)"></span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 py-3">
                        <span class="text-gray-500 font-semibold">Lokasi</span>
                        <span class="sm:col-span-2 text-gray-900" x-text="form.location || '-'"></span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 py-3 last:pb-0">
                        <span class="text-gray-500 font-semibold">Jumlah Tim</span>
                        <span class="sm:col-span-2 text-gray-900" x-text="form.assignees.length + ' Pegawai'"></span>
                    </div>

                    <template x-if="conflictWarning">
                        <div class="pt-4">
                            <div class="p-3 bg-amber-50 border border-amber-300 rounded-lg text-amber-800 text-xs flex items-start gap-2">
                                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div>
                                    <p class="font-bold">Peringatan Bentrok Jadwal!</p>
                                    <p class="mt-0.5" x-text="conflictMessage"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Wizard Controls -->
            <div class="mt-8 pt-5 border-t border-gray-200 flex items-center justify-between gap-3">
                <button type="button" @click="prevStep()" x-show="step > 1"
                        class="px-5 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition cursor-pointer">
                    &larr; Kembali
                </button>

                <div class="flex-1 flex justify-end">
                    <button type="button" @click="nextStep()" x-show="step < 4"
                            class="px-5 py-2.5 bg-bps-blue text-white font-semibold rounded-lg hover:bg-bps-teal transition shadow-xs cursor-pointer">
                        Selanjutnya &rarr;
                    </button>

                    <button type="submit" x-show="step === 4" :disabled="isSubmitting"
                            class="px-6 py-2.5 bg-bps-green text-white font-semibold rounded-lg hover:bg-green-600 transition shadow-xs flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Simpan Perubahan</span>
                        <span x-show="isSubmitting">Menyimpan...</span>
                        <svg x-show="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('activityWizard', () => ({
            step: 1,
            stepLabels: ['Info Dasar', 'Jadwal & Lokasi', 'Tim & Dokumen', 'Konfirmasi'],
            isSubmitting: false,
            conflictWarning: false,
            conflictMessage: '',
            documentTypes: {
                surat_tugas: 'Surat Tugas',
                undangan: 'Surat Undangan',
                kuesioner: 'Kuesioner / Instrumen',
                berita_acara: 'Berita Acara',
                laporan: 'Laporan / Dokumentasi'
            },
            form: {
                id: {!! json_encode((string)($activity['id'] ?? '')) !!},
                title: {!! json_encode($activity['title'] ?? ($activity['subject'] ?? '')) !!},
                category: {!! json_encode($activity['category'] ?? '') !!},
                description: {!! json_encode($activity['description'] ?? '') !!},
                start: {!! json_encode($activity['start'] ?? ($activity['start_date'] ?? '')) !!},
                end: {!! json_encode($activity['end'] ?? ($activity['due_date'] ?? '')) !!},
                location: {!! json_encode($activity['location'] ?? '') !!},
                allDay: {{ !empty($activity['all_day']) || !empty($activity['allDay']) ? 'true' : 'false' }},
                status: {!! json_encode($activity['status'] ?? 'planned') !!},
                assignees: {!! json_encode(array_values(array_filter($activity['assignees'] ?? [$activity['assignee_id'] ?? null]))) !!},
                documents: {
                    surat_tugas: {{ !empty($activity['documents_links']['surat_tugas']) || !empty($activity['documents']['surat_tugas']) ? 'true' : 'false' }},
                    undangan: {{ !empty($activity['documents_links']['undangan']) || !empty($activity['documents']['undangan']) ? 'true' : 'false' }},
                    kuesioner: {{ !empty($activity['documents_links']['kuesioner']) || !empty($activity['documents']['kuesioner']) ? 'true' : 'false' }},
                    berita_acara: {{ !empty($activity['documents_links']['berita_acara']) || !empty($activity['documents']['berita_acara']) ? 'true' : 'false' }},
                    laporan: {{ !empty($activity['documents_links']['laporan']) || !empty($activity['documents']['laporan']) ? 'true' : 'false' }}
                },
                documents_links: {!! json_encode($activity['documents_links'] ?? [
                    'surat_tugas' => $activity['documents']['surat_tugas'] ?? '',
                    'undangan' => $activity['documents']['undangan'] ?? '',
                    'kuesioner' => $activity['documents']['kuesioner'] ?? '',
                    'berita_acara' => $activity['documents']['berita_acara'] ?? '',
                    'laporan' => $activity['documents']['laporan'] ?? ''
                ]) !!}
            },
            initWizard() {
                // For edit mode, we don't automatically set default dates as they come from data
            },
            validateStep() {
                if (this.step === 1) {
                    if (!this.form.title || !this.form.category) {
                        alert('Mohon isi Judul Kegiatan dan Kategori.');
                        return false;
                    }
                } else if (this.step === 2) {
                    if (!this.form.start || !this.form.end || !this.form.location) {
                        alert('Mohon isi Waktu Mulai, Waktu Selesai, dan Lokasi.');
                        return false;
                    }
                    if (new Date(this.form.end) <= new Date(this.form.start)) {
                        alert('Waktu selesai harus lebih dari waktu mulai.');
                        return false;
                    }
                    this.checkConflicts();
                } else if (this.step === 3) {
                    if (this.form.assignees.length === 0) {
                        alert('Mohon pilih minimal 1 anggota tim / penanggung jawab.');
                        return false;
                    }
                    this.checkConflicts();
                }
                return true;
            },
            nextStep() {
                if (this.validateStep()) {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    this.step++;
                }
            },
            prevStep() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
                this.step--;
            },
            validateWeekend(event, field) {
                if (!event.target.value) return;
                const d = new Date(event.target.value);
                if (d.getDay() === 0 || d.getDay() === 6) {
                    alert('Hari Sabtu dan Minggu merupakan hari libur dan tidak dapat dipilih.');
                    event.target.value = '';
                    this.form[field] = '';
                }
            },
            formatDate(datetime) {
                if (!datetime) return '-';
                return new Date(datetime).toLocaleString('id-ID', {
                    day: 'numeric', month: 'short', year: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });
            },
            checkConflicts() {
                if (!this.form.start || !this.form.end || this.form.assignees.length === 0) return;

                fetch('/api/check-conflicts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        start: this.form.start,
                        end: this.form.end,
                        assignees: this.form.assignees,
                        activity_id: this.form.id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.conflictWarning = data.has_conflict;
                    if (data.has_conflict) {
                        let msgs = data.conflicts.map(c => `"${c.activity_title}"`);
                        this.conflictMessage = `Ada jadwal yang bentrok dengan kegiatan: ${msgs.join(', ')}.`;
                    }
                })
                .catch(err => console.error(err));
            },
            validateAll() {
                if (!this.form.title || !this.form.category) {
                    alert('Mohon isi Judul Kegiatan dan Kategori pada Step 1.');
                    this.step = 1;
                    return false;
                }
                if (!this.form.start || !this.form.end || !this.form.location) {
                    alert('Mohon isi Waktu Mulai, Waktu Selesai, dan Lokasi pada Step 2.');
                    this.step = 2;
                    return false;
                }
                if (new Date(this.form.end) <= new Date(this.form.start)) {
                    alert('Waktu selesai harus lebih dari waktu mulai pada Step 2.');
                    this.step = 2;
                    return false;
                }
                if (!this.form.assignees || this.form.assignees.length === 0) {
                    alert('Mohon pilih minimal 1 anggota tim / penanggung jawab pada Step 3.');
                    this.step = 3;
                    return false;
                }
                return true;
            },
            submitWizard() {
                if (!this.validateAll()) return;

                this.isSubmitting = true;

                // Clean empty URLs to null to satisfy validation
                const cleanDocsLinks = {};
                if (this.form.documents_links && typeof this.form.documents_links === 'object') {
                    for (const key in this.form.documents_links) {
                        const val = this.form.documents_links[key];
                        if (this.form.documents && this.form.documents[key] && typeof val === 'string' && val.trim() !== '') {
                            cleanDocsLinks[key] = val.trim();
                        }
                    }
                }

                const payload = {
                    ...this.form,
                    documents_links: Object.keys(cleanDocsLinks).length > 0 ? cleanDocsLinks : null
                };

                fetch(`/api/activities/${this.form.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Gagal menyimpan perubahan');
                    return data;
                })
                .then(data => {
                    window.location.href = "{{ route('activities.index') }}";
                })
                .catch(err => {
                    alert('Terjadi kesalahan: ' + err.message);
                    this.isSubmitting = false;
                });
            }
        }));
    });
</script>
@endsection