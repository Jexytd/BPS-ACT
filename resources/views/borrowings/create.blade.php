@extends('layouts.app')

@section('title', 'Ajukan Peminjaman Aset — BPS ACT')
@section('header_title', 'Formulir Pengajuan Peminjaman Aset & Fasilitas')

@section('content')
<div x-data="borrowingWizard" x-init="initWizard" class="max-w-4xl mx-auto py-6 px-4 sm:px-6">

    <!-- Progress Steps Header -->
    <div class="mb-14 sm:mb-16">
        <div class="relative">

            <!-- Background Line -->
            <div
                class="absolute h-1 bg-gray-200 z-0 rounded-full"
                style="top: 1.5rem; transform: translateY(50%); left: 2.5rem; right: 2.5rem;">
            </div>

            <!-- Active Progress Line -->
            <div
                class="absolute h-1 bg-bps-blue z-0 rounded-full transition-all duration-500 ease-in-out"
                :style="`
                    top: 1.5rem;
                    transform: translateY(50%);
                    left: 2.5rem;
                    width: calc(${((step - 1) / 2) * 100}% - ${((step - 1) / 2) * 5}rem);
                `">
            </div>

            <!-- Steps -->
            <div class="relative z-10 flex items-start justify-between">
                <template x-for="i in 3" :key="i">
                    <div class="flex flex-col items-center cursor-pointer" @click="if (i < step) step = i">

                        <!-- Number -->
                        <div
                            :class="step >= i
                                ? 'bg-bps-blue text-white border-bps-blue shadow-md'
                                : 'bg-white text-gray-400 border-gray-200'"
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

    <!-- Wizard Form Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mt-6">
        <form novalidate @submit.prevent="submitWizard" class="p-5 sm:p-8">

            <!-- Step 1: Pemilihan Aset & Tujuan -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-6">Pilih Aset & Tujuan Penggunaan</h2>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Aset / Fasilitas yang Dipinjam *</label>
                        <select x-model="form.asset_id" @change="onAssetChange" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition bg-white">
                            <option value="">-- Pilih Aset / Fasilitas --</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}" {{ ($selectedAssetId == $asset->id) ? 'selected' : '' }} {{ $asset->status !== 'tersedia' ? 'disabled' : '' }}>
                                    {{ $asset->name }} ({{ strtoupper($asset->category) }}) — Status: {{ strtoupper($asset->status) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hanya aset dengan status <strong>TERSEDIA</strong> yang dapat diajukan untuk peminjaman.</p>
                    </div>

                    <!-- Selected Asset Preview Card -->
                    <template x-if="selectedAsset">
                        <div class="p-4 bg-blue-50/60 rounded-lg border border-blue-100 flex items-start gap-4">
                            <div class="w-16 h-16 rounded-md bg-white border border-blue-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                <img x-show="selectedAsset.photo" :src="selectedAsset.photo" class="w-full h-full object-cover">
                                <svg x-show="!selectedAsset.photo" class="w-8 h-8 text-bps-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 text-sm" x-text="selectedAsset.name"></h4>
                                <p class="text-xs text-gray-600 mt-0.5" x-text="selectedAsset.description || 'Tidak ada deskripsi spesifik.'"></p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800">TERSEDIA</span>
                                    <span class="text-xs text-gray-500 uppercase font-semibold" x-text="selectedAsset.category"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tujuan Penggunaan *</label>
                        <input type="text" x-model="form.purpose" required
                               placeholder="Contoh: Operasional Survei Angkatan Kerja Nasional ke Kecamatan X"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Catatan Tambahan (Opsional)</label>
                        <textarea x-model="form.notes" rows="3"
                                  placeholder="Sebutkan detail kebutuhan khusus, rute perjalanan, atau perlengkapan pendukung..."
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition resize-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- Step 2: Jadwal Peminjaman -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-6">Jadwal Peminjaman & Pengembalian</h2>

                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Mulai Pinjam *</label>
                            <input type="date" x-model="form.borrow_date" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Rencana Kembali *</label>
                            <input type="date" x-model="form.return_date" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bps-blue focus:border-bps-blue outline-none transition bg-white">
                        </div>
                    </div>

                    <div x-show="form.borrow_date && form.return_date" class="p-4 bg-blue-50/70 border border-blue-100 rounded-lg text-blue-900 text-sm flex items-center gap-3">
                        <svg class="w-5 h-5 text-bps-blue flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <span>Estimasi durasi peminjaman: <strong x-text="calculateDuration()"></strong></span>
                            <p class="text-xs text-blue-700 mt-0.5">Pastikan aset dikembalikan tepat waktu sebelum batas akhir jadwal.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Konfirmasi & Persetujuan -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-6">Konfirmasi Pengajuan Peminjaman</h2>

                <div class="space-y-5">
                    <div class="bg-gray-50 p-4 sm:p-5 rounded-lg border border-gray-200 text-sm divide-y divide-gray-200">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 py-3 first:pt-0">
                            <span class="text-gray-500 font-semibold">Nama Aset / Fasilitas</span>
                            <span class="sm:col-span-2 font-bold text-gray-900" x-text="selectedAsset ? selectedAsset.name : '-'"></span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 py-3">
                            <span class="text-gray-500 font-semibold">Kategori</span>
                            <span class="sm:col-span-2 text-gray-900 uppercase font-medium" x-text="selectedAsset ? selectedAsset.category : '-'"></span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 py-3">
                            <span class="text-gray-500 font-semibold">Tujuan Penggunaan</span>
                            <span class="sm:col-span-2 text-gray-900 font-medium" x-text="form.purpose || '-'"></span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 py-3">
                            <span class="text-gray-500 font-semibold">Jadwal Peminjaman</span>
                            <span class="sm:col-span-2 font-bold text-bps-blue" x-text="formatDate(form.borrow_date) + ' s.d ' + formatDate(form.return_date) + ' (' + calculateDuration() + ')'"></span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 py-3 last:pb-0" x-show="form.notes">
                            <span class="text-gray-500 font-semibold">Catatan</span>
                            <span class="sm:col-span-2 text-gray-900" x-text="form.notes"></span>
                        </div>
                    </div>

                    <!-- Ketentuan & Komitmen -->
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg text-amber-900 text-xs flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="font-bold text-sm mb-1">Ketentuan Peminjaman BMN / Fasilitas Kantor:</p>
                            <ul class="list-disc list-inside space-y-1 text-amber-800">
                                <li>Peminjam bertanggung jawab penuh atas kebersihan, kelengkapan, dan keutuhan aset.</li>
                                <li>Wajib segera mengembalikan aset tepat waktu agar tidak mengganggu kegiatan pegawai lain.</li>
                                <li>Pengajuan akan diverifikasi dan disetujui oleh Tim Umum / Tata Usaha (TU).</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wizard Controls -->
            <div class="mt-8 pt-5 border-t border-gray-200 flex items-center justify-between gap-3">
                <button type="button" @click="prevStep()" x-show="step > 1"
                        class="px-5 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition cursor-pointer">
                    &larr; Kembali
                </button>

                <div class="flex-1 flex justify-end">
                    <button type="button" @click="nextStep()" x-show="step < 3"
                            class="px-5 py-2.5 bg-bps-blue text-white font-semibold rounded-lg hover:bg-bps-teal transition shadow-xs cursor-pointer">
                        Selanjutnya &rarr;
                    </button>

                    <button type="submit" x-show="step === 3" :disabled="isSubmitting"
                            class="px-6 py-2.5 bg-bps-green text-white font-semibold rounded-lg hover:bg-green-600 transition shadow-xs flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Kirim Pengajuan Peminjaman</span>
                        <span x-show="isSubmitting">Mengirimkan...</span>
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
        Alpine.data('borrowingWizard', () => ({
            step: 1,
            stepLabels: ['Aset & Tujuan', 'Jadwal', 'Konfirmasi'],
            isSubmitting: false,
            allAssets: @json($assets),
            selectedAsset: null,
            form: {
                asset_id: "{{ $selectedAssetId ?? '' }}",
                purpose: '',
                notes: '',
                borrow_date: '',
                return_date: ''
            },

            initWizard() {
                const today = new Date().toISOString().split('T')[0];
                this.form.borrow_date = today;
                this.form.return_date = today;

                if (this.form.asset_id) {
                    this.onAssetChange();
                }
            },

            onAssetChange() {
                this.selectedAsset = this.allAssets.find(a => a.id == this.form.asset_id) || null;
            },

            calculateDuration() {
                if (!this.form.borrow_date || !this.form.return_date) return '-';
                const start = new Date(this.form.borrow_date);
                const end = new Date(this.form.return_date);
                const diffTime = end - start;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                return diffDays > 0 ? `${diffDays} Hari` : '1 Hari';
            },

            formatDate(dateStr) {
                if (!dateStr) return '-';
                return new Date(dateStr).toLocaleDateString('id-ID', {
                    day: 'numeric', month: 'short', year: 'numeric'
                });
            },

            validateStep() {
                if (this.step === 1) {
                    if (!this.form.asset_id) {
                        alert('Mohon pilih aset / fasilitas yang ingin dipinjam.');
                        return false;
                    }
                    if (!this.form.purpose) {
                        alert('Mohon isi tujuan penggunaan aset.');
                        return false;
                    }
                } else if (this.step === 2) {
                    if (!this.form.borrow_date || !this.form.return_date) {
                        alert('Mohon isi tanggal pinjam dan tanggal rencana kembali.');
                        return false;
                    }
                    if (new Date(this.form.return_date) < new Date(this.form.borrow_date)) {
                        alert('Tanggal kembali tidak boleh lebih awal dari tanggal pinjam.');
                        return false;
                    }
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

            submitWizard() {
                if (!this.validateStep()) return;

                this.isSubmitting = true;

                fetch('/api/borrowings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || data.error || 'Gagal mengajukan peminjaman.');
                    }
                    return data;
                })
                .then(data => {
                    window.location.href = "{{ route('borrowings.index') }}";
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
