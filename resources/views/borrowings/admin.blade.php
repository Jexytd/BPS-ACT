@extends('layouts.app')

@section('title', 'Panel TU — Kelola Peminjaman')
@section('header_title', 'Manajemen Peminjaman Fasilitas')

@section('content')
<div x-data="adminRequestsApp()" x-init="initRequests()" class="space-y-6">

    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-2xs flex flex-wrap items-center justify-between gap-4">
        <div class="flex gap-2">
            <template x-for="s in statusFilters" :key="s.value">
                <button @click="statusFilter = s.value"
                        :class="statusFilter === s.value ? 'bg-bps-blue text-white shadow-xs font-bold' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 font-medium'"
                        class="px-4 py-2 rounded-md text-sm transition"
                        x-text="s.label"></button>
            </template>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-bold">
                    <tr>
                        <th class="px-6 py-4">Peminjam</th>
                        <th class="px-6 py-4">Aset / Fasilitas</th>
                        <th class="px-6 py-4">Tujuan</th>
                        <th class="px-6 py-4">Tgl Pinjam - Kembali</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Aksi (TU)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="req in filteredRequests" :key="req.id">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img :src="req.user?.photo || 'https://ui-avatars.com/api/?name=' + req.user?.name" class="w-8 h-8 rounded-full border border-gray-200">
                                    <div>
                                        <p class="font-bold text-gray-900 leading-none" x-text="req.user?.name"></p>
                                        <p class="text-xs text-gray-500 mt-1" x-text="req.user?.email"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-bps-blue" x-text="req.asset?.name"></p>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mt-0.5" x-text="formatCategory(req.asset?.category)"></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-800" x-text="req.purpose"></p>
                                <p class="text-xs text-gray-500 mt-0.5 truncate max-w-[150px]" x-show="req.notes" x-text="req.notes"></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-900" x-text="formatDate(req.borrow_date)"></span>
                                <span class="text-gray-500 mx-1">-</span>
                                <span class="font-semibold text-gray-900" x-text="formatDate(req.return_date)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full border shadow-xs"
                                      :class="statusColors(req.status)"
                                      x-text="req.status.toUpperCase()"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2" x-show="req.status === 'pending'">
                                    <button @click="updateStatus(req.id, 'approved')" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded shadow-sm transition">Setujui</button>
                                    <button @click="updateStatus(req.id, 'rejected')" class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold rounded shadow-sm transition">Tolak</button>
                                </div>
                                <div class="flex gap-2" x-show="req.status === 'approved'">
                                    <button @click="updateStatus(req.id, 'returned')" class="px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-bold rounded shadow-sm transition">Tandai Kembali</button>
                                </div>
                                <span x-show="['rejected', 'returned', 'overdue'].includes(req.status)" class="text-xs text-gray-400 italic">
                                    Oleh: <span x-text="req.approver?.name || 'Sistem'"></span>
                                </span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <template x-if="loading">
            <div class="py-16 flex justify-center items-center">
                <svg class="w-8 h-8 text-bps-blue animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
        </template>
        <template x-if="!loading && filteredRequests.length === 0">
            <div class="py-16 text-center">
                <p class="text-sm text-gray-500">Tidak ada data peminjaman.</p>
            </div>
        </template>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function adminRequestsApp() {
        return {
            loading: true,
            requests: [],
            statusFilter: 'all',
            statusFilters: [
                { value: 'all', label: 'Semua' },
                { value: 'pending', label: 'Menunggu (Pending)' },
                { value: 'approved', label: 'Sedang Dipinjam' },
                { value: 'returned', label: 'Sudah Kembali' }
            ],

            async initRequests() {
                try {
                    const res = await fetch('/api/borrowings/all');
                    this.requests = await res.json();
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },

            get filteredRequests() {
                if (this.statusFilter === 'all') return this.requests;
                return this.requests.filter(r => r.status === this.statusFilter);
            },

            formatCategory(cat) {
                if(!cat) return '';
                const map = {
                    'kendaraan': 'Kendaraan Dinas',
                    'ruang_rapat': 'Ruang Rapat',
                    'peralatan': 'Peralatan',
                    'lainnya': 'Lainnya'
                };
                return map[cat] || cat;
            },

            formatDate(dateStr) {
                if(!dateStr) return '-';
                return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            },

            statusColors(status) {
                const map = {
                    'pending': 'bg-amber-100 text-amber-800 border-amber-200',
                    'approved': 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'rejected': 'bg-rose-100 text-rose-800 border-rose-200',
                    'returned': 'bg-gray-100 text-gray-700 border-gray-200',
                    'overdue': 'bg-red-100 text-red-800 border-red-200',
                };
                return map[status] || 'bg-gray-100 text-gray-800';
            },

            async updateStatus(id, newStatus) {
                if(!confirm(`Yakin mengubah status menjadi ${newStatus.toUpperCase()}?`)) return;
                try {
                    const res = await fetch(`/api/borrowings/${id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ status: newStatus })
                    });
                    if (res.ok) {
                        const updated = await res.json();
                        // Update UI directly
                        const idx = this.requests.findIndex(r => r.id === id);
                        if(idx !== -1) {
                            this.requests[idx].status = updated.data.status;
                            this.requests[idx].approver = updated.data.approver || { name: 'Saya (Admin)' };
                        }
                    } else {
                        const err = await res.json();
                        alert(err.message || 'Gagal mengubah status.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan jaringan.');
                }
            }
        }
    }
</script>
@endsection
