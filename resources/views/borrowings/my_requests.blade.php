@extends('layouts.app')

@section('title', 'Peminjaman Saya — BPS ACT')
@section('header_title', 'Riwayat Peminjaman Fasilitas')

@section('content')
<div x-data="myRequestsApp()" x-init="initRequests()" class="space-y-6">

    <!-- Top Action Bar -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-2xs flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-900">Daftar Peminjaman Anda</h3>
        <a href="{{ route('assets.index') }}" class="px-4 py-2 bg-bps-blue hover:bg-bps-teal text-white text-sm font-bold rounded-lg shadow-md transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Peminjaman Baru
        </a>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-bold">
                    <tr>
                        <th class="px-6 py-4">Aset / Fasilitas</th>
                        <th class="px-6 py-4">Tujuan Penggunaan</th>
                        <th class="px-6 py-4">Tgl Pinjam - Tgl Kembali</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Disetujui Oleh</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="req in requests" :key="req.id">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900" x-text="req.asset?.name"></p>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mt-0.5" x-text="formatCategory(req.asset?.category)"></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-800" x-text="req.purpose"></p>
                                <p class="text-xs text-gray-500 mt-0.5 truncate max-w-[200px]" x-show="req.notes" x-text="req.notes"></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-900" x-text="formatDate(req.borrow_date)"></span>
                                <span class="text-gray-500 mx-1">s/d</span>
                                <span class="font-semibold text-gray-900" x-text="formatDate(req.return_date)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full border shadow-xs"
                                      :class="statusColors(req.status)"
                                      x-text="req.status.toUpperCase()"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span x-show="req.approver" class="text-gray-700" x-text="req.approver?.name"></span>
                                <span x-show="!req.approver" class="text-gray-400 italic">Menunggu...</span>
                            </td>
                            <td class="px-6 py-4">
                                <button @click="deleteRequest(req.id)" x-show="req.status === 'pending'" class="text-rose-500 hover:text-rose-700 font-bold transition flex items-center gap-1 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Batal
                                </button>
                                <span x-show="req.status !== 'pending'" class="text-xs text-gray-400">Tidak ada aksi</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Loading & Empty States -->
        <template x-if="loading">
            <div class="py-16 flex justify-center items-center">
                <svg class="w-8 h-8 text-bps-blue animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
        </template>
        <template x-if="!loading && requests.length === 0">
            <div class="py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <h3 class="mt-2 text-sm font-bold text-gray-900">Belum ada peminjaman</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum pernah mengajukan peminjaman fasilitas.</p>
            </div>
        </template>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function myRequestsApp() {
        return {
            loading: true,
            requests: [],

            async initRequests() {
                try {
                    const res = await fetch('/api/borrowings/my-requests');
                    this.requests = await res.json();
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
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

            async deleteRequest(id) {
                if(!confirm('Yakin ingin membatalkan pengajuan ini?')) return;
                try {
                    const res = await fetch(`/api/borrowings/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    if (res.ok) {
                        this.requests = this.requests.filter(r => r.id !== id);
                    } else {
                        const err = await res.json();
                        alert(err.message || 'Gagal membatalkan.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan jaringan.');
                }
            }
        }
    }
</script>
@endsection
