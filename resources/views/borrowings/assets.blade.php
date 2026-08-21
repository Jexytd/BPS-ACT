@extends('layouts.app')

@section('title', 'Daftar Aset & Fasilitas — BPS ACT')
@section('header_title', 'Daftar Aset & Fasilitas BPS')

@php
    $isAdmin = (session('user')['role'] ?? '') === 'admin' || (session('user')['role'] ?? '') === 'lead';
@endphp

@section('content')
<div x-data="assetsApp" x-init="initAssets" class="space-y-6">

    <!-- Top Action Bar & Filters -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-2xs flex flex-wrap items-center justify-between gap-4">
        <!-- Category Filters -->
        <div class="flex items-center gap-2 bg-gray-100 p-1.5 rounded-lg overflow-x-auto w-full sm:w-auto">
            <template x-for="cat in categories" :key="cat.value">
                <button @click="selectedCategory = cat.value" 
                        :class="selectedCategory === cat.value ? 'bg-bps-blue text-white shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="px-4 py-2 rounded-md text-sm transition cursor-pointer whitespace-nowrap"
                        x-text="cat.label">
                </button>
            </template>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="{{ route('borrowings.create') }}" class="px-4 py-2.5 bg-bps-green hover:bg-green-600 text-white text-sm font-bold rounded-lg shadow-sm transition flex items-center justify-center gap-2 flex-1 sm:flex-initial">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ajukan Peminjaman
            </a>

            @if($isAdmin)
            <button @click="openAssetModal()" class="px-4 py-2.5 bg-bps-blue hover:bg-bps-teal text-white text-sm font-bold rounded-lg shadow-sm transition flex items-center justify-center gap-2 flex-1 sm:flex-initial cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Tambah Aset
            </button>
            @endif
        </div>
    </div>

    <!-- Assets Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <template x-for="asset in filteredAssets" :key="asset.id">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition flex flex-col overflow-hidden relative group">
                <div class="h-44 bg-gray-100 relative border-b border-gray-200 flex items-center justify-center overflow-hidden">
                    <img x-show="asset.photo" :src="asset.photo" class="w-full h-full object-cover">
                    <svg x-show="!asset.photo && asset.category === 'kendaraan'" class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <svg x-show="!asset.photo && asset.category === 'ruang_rapat'" class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <svg x-show="!asset.photo && asset.category === 'peralatan'" class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <svg x-show="!asset.photo && asset.category !== 'kendaraan' && asset.category !== 'ruang_rapat' && asset.category !== 'peralatan'" class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>

                    <!-- Status Badge -->
                    <div class="absolute top-3 left-3">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full text-white shadow-sm"
                              :class="{
                                'bg-emerald-500': asset.status === 'tersedia',
                                'bg-amber-500': asset.status === 'dipinjam',
                                'bg-rose-500': asset.status === 'maintenance'
                              }" x-text="asset.status.toUpperCase()"></span>
                    </div>

                    @if($isAdmin)
                    <!-- Admin Actions Hover -->
                    <div class="absolute top-3 right-3 flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity bg-white/90 backdrop-blur-xs p-1 rounded-lg shadow-sm border border-gray-200">
                        <button type="button" @click.stop="openAssetModal(asset)" class="p-1.5 text-gray-700 hover:text-bps-blue hover:bg-blue-50 rounded transition" title="Edit Aset">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button type="button" @click.stop="deleteAsset(asset.id)" class="p-1.5 text-gray-700 hover:text-red-600 hover:bg-red-50 rounded transition" title="Hapus Aset">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                    @endif
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1" x-text="formatCategory(asset.category)"></span>
                    <h3 class="font-bold text-gray-900 text-lg leading-tight mb-2" x-text="asset.name"></h3>
                    <p class="text-sm text-gray-600 mb-4 flex-1 line-clamp-2" x-text="asset.description || 'Tidak ada deskripsi spesifik.'"></p>
                    
                    <template x-if="asset.status === 'tersedia'">
                        <a :href="'/borrowings/create?asset_id=' + asset.id"
                           class="w-full py-2.5 rounded-lg text-sm font-bold bg-bps-blue hover:bg-bps-teal text-white shadow-sm transition flex justify-center items-center gap-2 mt-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Ajukan Peminjaman
                        </a>
                    </template>
                    <template x-if="asset.status !== 'tersedia'">
                        <button disabled
                                class="w-full py-2.5 rounded-lg text-sm font-bold bg-gray-100 text-gray-400 cursor-not-allowed flex justify-center items-center gap-2 mt-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            Sedang Tidak Tersedia
                        </button>
                    </template>
                </div>
            </div>
        </template>
        
        <!-- Loading State -->
        <template x-if="loading">
            <div class="col-span-full py-20 flex justify-center items-center">
                <svg class="w-8 h-8 text-bps-blue animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
        </template>

        <!-- Empty State -->
        <template x-if="!loading && filteredAssets.length === 0">
            <div class="col-span-full py-16 text-center bg-white rounded-xl border border-gray-200 border-dashed">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                <h3 class="mt-2 text-sm font-bold text-gray-900">Tidak ada aset</h3>
                <p class="mt-1 text-sm text-gray-500">Aset pada kategori ini belum tersedia.</p>
            </div>
        </template>
    </div>

    @if($isAdmin)
    <!-- Admin Asset Modal (Add/Edit) -->
    <div x-show="showAssetModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs" @click="showAssetModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-2xl border border-gray-200 sm:my-8 relative z-10"
                 @click.away="showAssetModal = false">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900" x-text="assetForm.id ? 'Edit Aset / Fasilitas' : 'Tambah Aset Baru'"></h3>
                    <button type="button" @click="showAssetModal = false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form @submit.prevent="submitAsset" class="mt-5 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Nama Aset/Fasilitas *</label>
                        <input type="text" x-model="assetForm.name" required placeholder="Contoh: Avanza BPS Plat Merah 01" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Kategori *</label>
                        <select x-model="assetForm.category" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm bg-white">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="kendaraan">Kendaraan Dinas</option>
                            <option value="ruang_rapat">Ruang Rapat</option>
                            <option value="peralatan">Peralatan & Elektronik</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Status Ketersediaan *</label>
                        <select x-model="assetForm.status" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm bg-white">
                            <option value="tersedia">Tersedia</option>
                            <option value="dipinjam">Dipinjam</option>
                            <option value="maintenance">Dalam Perbaikan (Maintenance)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Deskripsi / Spesifikasi</label>
                        <textarea x-model="assetForm.description" rows="3" placeholder="Contoh: Kapasitas 7 penumpang, kondisi prima, kunci ada di pos keamanan." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue focus:border-bps-blue shadow-sm resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Foto Aset (Opsional)</label>
                        <input type="file" id="assetPhoto" accept="image/png, image/jpeg, image/jpg" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-bps-blue file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-bps-blue hover:file:bg-blue-100">
                        <template x-if="assetForm.photo && assetForm.id">
                            <div class="mt-2 flex items-center gap-2">
                                <span class="text-xs text-gray-500">Foto saat ini:</span>
                                <img :src="assetForm.photo" class="w-10 h-10 object-cover rounded border border-gray-200">
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-gray-200">
                        <button type="button" @click="showAssetModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-bold rounded-lg transition cursor-pointer">Batal</button>
                        <button type="submit" :disabled="submittingAsset" class="px-5 py-2.5 bg-bps-blue hover:bg-bps-teal text-white text-sm font-bold rounded-lg shadow-md transition flex items-center gap-2 cursor-pointer disabled:opacity-50">
                            <span x-text="submittingAsset ? 'Menyimpan...' : 'Simpan Aset'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assetsApp', () => ({
            loading: true,
            assets: [],
            selectedCategory: 'all',
            categories: [
                { value: 'all', label: 'Semua Kategori' },
                { value: 'kendaraan', label: 'Kendaraan Dinas' },
                { value: 'ruang_rapat', label: 'Ruang Rapat' },
                { value: 'peralatan', label: 'Peralatan & Elektronik' }
            ],

            // Admin Asset Management
            showAssetModal: false,
            submittingAsset: false,
            assetForm: {
                id: null,
                name: '',
                category: '',
                description: '',
                status: 'tersedia',
                photo: null
            },

            async initAssets() {
                this.loading = true;
                try {
                    const res = await fetch('/api/assets');
                    this.assets = await res.json();
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },

            get filteredAssets() {
                if (this.selectedCategory === 'all') return this.assets;
                return this.assets.filter(a => a.category === this.selectedCategory);
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

            // --- Admin Logic ---
            openAssetModal(asset = null) {
                if (asset) {
                    this.assetForm = { ...asset };
                } else {
                    this.assetForm = { id: null, name: '', category: '', description: '', status: 'tersedia', photo: null };
                }
                const photoInput = document.getElementById('assetPhoto');
                if (photoInput) photoInput.value = '';
                this.showAssetModal = true;
            },

            async submitAsset() {
                this.submittingAsset = true;
                try {
                    const formData = new FormData();
                    formData.append('name', this.assetForm.name);
                    formData.append('category', this.assetForm.category);
                    formData.append('description', this.assetForm.description || '');
                    formData.append('status', this.assetForm.status);
                    
                    const photoInput = document.getElementById('assetPhoto');
                    if (photoInput && photoInput.files[0]) {
                        formData.append('photo', photoInput.files[0]);
                    }

                    const url = this.assetForm.id ? `/api/admin/assets/${this.assetForm.id}` : '/api/admin/assets';

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });

                    const data = await res.json();

                    if (res.ok) {
                        this.showAssetModal = false;
                        await this.initAssets();
                    } else {
                        alert(data.message || data.error || 'Gagal menyimpan aset.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan jaringan.');
                } finally {
                    this.submittingAsset = false;
                }
            },

            async deleteAsset(id) {
                if (!confirm('Apakah Anda yakin ingin menghapus aset ini?')) return;
                try {
                    const res = await fetch(`/api/admin/assets/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    const data = await res.json();
                    if (res.ok) {
                        await this.initAssets();
                    } else {
                        alert(data.error || data.message || 'Gagal menghapus aset.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan jaringan.');
                }
            }
        }));
    });
</script>
@endsection
